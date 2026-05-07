<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use App\Models\EmployeeNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $requests = LeaveRequest::with(['employee', 'leaveType'])
            ->where('status', 'pending')
            ->orderBy('from_date')
            ->paginate(20);

        return view('admin.leave.pending', compact('requests'));
    }

    public function approve(LeaveRequest $leaveRequest)
    {
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
}
