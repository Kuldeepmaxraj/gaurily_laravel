<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use App\Models\EmployeeNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaveController extends Controller
{
    public function index()
    {
        $employee = Auth::user();
        $requests = $employee->leaveRequests()->with('leaveType')->latest()->paginate(10);
        $balances = $employee->leaveBalances()->with('leaveType')->where('year', now()->year)->get();
        $types    = LeaveType::where('is_active', true)->get();

        return view('employee.leave.index', compact('employee', 'requests', 'balances', 'types'));
    }

    public function apply(Request $request)
    {
        $data = $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'from_date'     => 'required|date|after_or_equal:today',
            'to_date'       => 'required|date|after_or_equal:from_date',
            'reason'        => 'required|string|max:500',
        ]);

        $employee  = Auth::user();
        $from      = Carbon::parse($data['from_date']);
        $to        = Carbon::parse($data['to_date']);
        $totalDays = LeaveRequest::calculateWorkingDays($from, $to);

        // Check balance for paid leaves
        $leaveType = LeaveType::find($data['leave_type_id']);
        if ($leaveType->is_paid) {
            $balance = $employee->leaveBalances()
                ->where('leave_type_id', $leaveType->id)
                ->where('year', now()->year)
                ->first();

            if (!$balance || $balance->balance < $totalDays) {
                return back()->with('error', 'Insufficient leave balance.');
            }
        }

        LeaveRequest::create([
            'employee_id'    => $employee->id,
            'leave_type_id'  => $data['leave_type_id'],
            'from_date'      => $from,
            'to_date'        => $to,
            'total_days'     => $totalDays,
            'reason'         => $data['reason'],
            'status'         => $leaveType->requires_approval ? 'pending' : 'approved',
        ]);

        return back()->with('success', "Leave applied for {$totalDays} working day(s).");
    }

    public function cancel(LeaveRequest $leaveRequest)
    {
        $this->authorize('cancel', $leaveRequest);

        if ($leaveRequest->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be cancelled.');
        }

        $leaveRequest->update(['status' => 'cancelled']);
        return back()->with('success', 'Leave request cancelled.');
    }

    // ---------- HR / Admin ----------

    public function pending()
    {
        $reviewer = Auth::user()->loadMissing('role');
        abort_unless($reviewer->hasAnyRole(['admin', 'team_lead']), 403);

        $status = request('status', 'all');
        $month = request('month');
        $employeeId = request('employee_id');
        $employeeId = $employeeId ? (int) $employeeId : null;

        $employees = $this->visibleEmployeesQuery($reviewer)->orderBy('name')->get();

        if ($employeeId && !$employees->pluck('id')->contains($employeeId)) {
            abort(403, 'You can only view leave history for your own team members.');
        }

        $requestsQuery = $this->managedLeaveRequestsQuery($reviewer)
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->when($employeeId, fn ($q) => $q->where('employee_id', $employeeId));

        if (!empty($month) && preg_match('/^\d{4}-\d{2}$/', $month)) {
            [$year, $mon] = explode('-', $month);
            $requestsQuery
                ->whereYear('from_date', $year)
                ->whereMonth('from_date', $mon);
        }

        $requests = $requestsQuery
            ->orderByDesc('from_date')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $types = LeaveType::where('is_active', true)->orderBy('name')->get();

        return view('admin.leave.pending', compact('requests', 'employees', 'status', 'month', 'employeeId', 'types'));
    }

    /**
     * Record a leave on behalf of an employee (e.g. emergency leave taken without applying).
     * Unlike employee self-service, past dates are allowed here.
     */
    public function record(Request $request)
    {
        $reviewer = Auth::user()->loadMissing('role');
        abort_unless($reviewer->hasAnyRole(['admin', 'team_lead']), 403);

        $data = $request->validate([
            'employee_id'   => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'from_date'     => 'required|date',
            'to_date'       => 'required|date|after_or_equal:from_date',
            'reason'        => 'required|string|max:500',
        ]);

        $employee = $this->visibleEmployeesQuery($reviewer)->findOrFail($data['employee_id']);

        $from      = Carbon::parse($data['from_date'])->startOfDay();
        $to        = Carbon::parse($data['to_date'])->startOfDay();
        $totalDays = LeaveRequest::calculateWorkingDays($from, $to);

        if ($totalDays <= 0) {
            return back()->with('error', 'The selected range contains no working days.');
        }

        $overlaps = LeaveRequest::where('employee_id', $employee->id)
            ->whereIn('status', ['pending', 'approved'])
            ->where('from_date', '<=', $to)
            ->where('to_date', '>=', $from)
            ->exists();

        if ($overlaps) {
            return back()->with('error', 'This employee already has a leave request covering those dates.');
        }

        $leaveType = LeaveType::findOrFail($data['leave_type_id']);
        $balance   = LeaveBalance::where('employee_id', $employee->id)
            ->where('leave_type_id', $leaveType->id)
            ->where('year', $from->year)
            ->first();

        if ($leaveType->is_paid && (!$balance || $balance->balance < $totalDays)) {
            return back()->with('error', 'Insufficient leave balance. Adjust the balance first or record this as an unpaid leave type.');
        }

        DB::transaction(function () use ($employee, $leaveType, $from, $to, $totalDays, $data, $balance) {
            LeaveRequest::create([
                'employee_id'      => $employee->id,
                'leave_type_id'    => $leaveType->id,
                'from_date'        => $from,
                'to_date'          => $to,
                'total_days'       => $totalDays,
                'reason'           => $data['reason'],
                'status'           => 'approved',
                'reviewed_by'      => Auth::id(),
                'reviewed_at'      => now(),
                'reviewer_comment' => 'Recorded by ' . Auth::user()->name . ' on behalf of the employee.',
            ]);

            if ($balance) {
                $balance->increment('used', $totalDays);
                $balance->decrement('balance', $totalDays);
            }

            $this->syncAttendanceForLeave($employee, $from, $to);

            EmployeeNotification::create([
                'employee_id' => $employee->id,
                'title'       => 'Leave Recorded',
                'message'     => "A {$leaveType->name} of {$totalDays} day(s) from {$from->format('d M Y')} to {$to->format('d M Y')} was recorded on your behalf.",
                'type'        => 'info',
            ]);
        });

        return back()->with('success', "Leave recorded for {$employee->name} ({$totalDays} working day(s)).");
    }

    /**
     * Mark each working day in the range as 'leave' in the attendance register,
     * leaving days the employee actually clocked in for untouched.
     */
    private function syncAttendanceForLeave($employee, Carbon $from, Carbon $to): void
    {
        for ($day = $from->copy(); $day->lte($to); $day->addDay()) {
            if ($day->isWeekend() || \App\Models\Holiday::isHoliday($day)) {
                continue;
            }

            $log = AttendanceLog::firstOrNew([
                'employee_id'     => $employee->id,
                'attendance_date' => $day->toDateString(),
            ]);

            if ($log->exists && $log->login_time) {
                continue;
            }

            $log->fill([
                'shift_id' => $log->shift_id ?? $employee->shift_id,
                'status'   => 'leave',
                'notes'    => trim(($log->notes ? $log->notes . ' | ' : '') . 'Marked as leave by ' . Auth::user()->name),
            ])->save();
        }
    }

    public function records(Request $request)
    {
        $reviewer = Auth::user()->loadMissing('role');
        abort_unless($reviewer->hasAnyRole(['admin', 'team_lead']), 403);

        $status = $request->input('status', 'all');
        $month = $request->input('month', now()->format('Y-m'));
        $employeeId = $request->input('employee_id');
        $employeeId = $employeeId ? (int) $employeeId : null;
        [$year, $mon] = explode('-', $month);

        $employees = $this->visibleEmployeesQuery($reviewer)->orderBy('name')->get();

        if ($employeeId && !$employees->pluck('id')->contains($employeeId)) {
            abort(403, 'You can only view leave records for your own team members.');
        }

        $requests = $this->managedLeaveRequestsQuery($reviewer)
            ->whereYear('from_date', $year)
            ->whereMonth('from_date', $mon)
            ->when($employeeId, fn ($q) => $q->where('employee_id', $employeeId))
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->orderByDesc('from_date')
            ->paginate(20)
            ->withQueryString();

        return view('admin.leave.records', compact('requests', 'employees', 'status', 'month', 'employeeId'));
    }

    public function approve(LeaveRequest $leaveRequest)
    {
        $reviewer = Auth::user()->loadMissing('role');
        $this->ensureCanManageLeave($reviewer, $leaveRequest);

        if ($leaveRequest->status !== 'pending') {
            return back()->with('error', 'Request is not pending.');
        }

        $leaveRequest->update([
            'status'      => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'reviewer_comment' => request('reviewer_comment'),
        ]);

        // Deduct balance
        $balance = LeaveBalance::where('employee_id', $leaveRequest->employee_id)
            ->where('leave_type_id', $leaveRequest->leave_type_id)
            ->where('year', now()->year)
            ->first();

        if ($balance) {
            $balance->increment('used', $leaveRequest->total_days);
            $balance->decrement('balance', $leaveRequest->total_days);
        }

        // Notify employee
        EmployeeNotification::create([
            'employee_id' => $leaveRequest->employee_id,
            'title'       => 'Leave Approved',
            'message'     => "Your {$leaveRequest->leaveType->name} ({$leaveRequest->total_days} day(s)) has been approved.",
            'type'        => 'success',
        ]);

        return back()->with('success', 'Leave approved.');
    }

    public function reject(LeaveRequest $leaveRequest)
    {
        $reviewer = Auth::user()->loadMissing('role');
        $this->ensureCanManageLeave($reviewer, $leaveRequest);

        $data = request()->validate(['reviewer_comment' => 'required|string|max:300']);

        if ($leaveRequest->status !== 'pending') {
            return back()->with('error', 'Request is not pending.');
        }

        $leaveRequest->update([
            'status'           => 'rejected',
            'reviewed_by'      => Auth::id(),
            'reviewed_at'      => now(),
            'reviewer_comment' => $data['reviewer_comment'],
        ]);

        EmployeeNotification::create([
            'employee_id' => $leaveRequest->employee_id,
            'title'       => 'Leave Rejected',
            'message'     => "Your {$leaveRequest->leaveType->name} request was rejected. Reason: {$data['reviewer_comment']}",
            'type'        => 'danger',
        ]);

        return back()->with('success', 'Leave rejected.');
    }

    private function visibleEmployeesQuery($reviewer)
    {
        return \App\Models\Employee::where('status', 'active')
            ->when(
                $reviewer->hasRole('team_lead'),
                fn ($q) => $q->when(
                    $reviewer->team_id,
                    fn ($q2) => $q2->where('team_id', $reviewer->team_id),
                    fn ($q2) => $q2->whereRaw('1 = 0')
                )
            );
    }

    private function managedLeaveRequestsQuery($reviewer)
    {
        return LeaveRequest::with(['employee', 'leaveType', 'reviewer'])
            ->when(
                $reviewer->hasRole('team_lead'),
                fn ($q) => $q->whereHas('employee', fn ($q2) => $q2->where('team_id', $reviewer->team_id ?? 0))
            );
    }

    private function ensureCanManageLeave($reviewer, LeaveRequest $leaveRequest): void
    {
        abort_unless($reviewer->hasAnyRole(['admin', 'team_lead']), 403);

        $leaveRequest->loadMissing('employee');
        if ($reviewer->hasRole('team_lead') && $leaveRequest->employee?->team_id !== $reviewer->team_id) {
            abort(403, 'You can only manage leave requests for your own team members.');
        }
    }
}
