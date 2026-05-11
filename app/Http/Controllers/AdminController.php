<?php

namespace App\Http\Controllers;

use App\Mail\AbsenceAlert;
use App\Models\Employee;
use App\Models\AttendanceLog;
use App\Models\AttendanceSetting;
use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\Holiday;
use App\Models\Role;
use App\Models\Shift;
use App\Models\Team;
use App\Models\WorkLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function dashboard()
    {
        $today = today();

        $totalEmployees  = Employee::where('status', 'active')->count();
        $presentToday    = AttendanceLog::where('attendance_date', $today)
                            ->where('status', 'present')->count();
        $halfDayToday    = AttendanceLog::where('attendance_date', $today)
                            ->where('status', 'half_day')->count();
        $lateToday       = AttendanceLog::where('attendance_date', $today)
                            ->where('is_late', true)->count();
        $pendingLeaves   = LeaveRequest::where('status', 'pending')->count();

        $recentAttendance = AttendanceLog::with('employee')
            ->where('attendance_date', $today)
            ->orderByDesc('login_time')
            ->limit(10)
            ->get();

        $pendingLeaveList = LeaveRequest::with(['employee', 'leaveType'])
            ->where('status', 'pending')
            ->orderBy('from_date')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalEmployees', 'presentToday', 'halfDayToday', 'lateToday',
            'pendingLeaves', 'recentAttendance', 'pendingLeaveList'
        ));
    }

    // ---------- Employee Management ----------

    public function employees(Request $request)
    {
        $authUser = Auth::user()->load('role', 'team');
        $role     = $authUser->role?->name;

        $query = Employee::with(['role', 'shift', 'team'])
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('employee_code', 'like', "%{$s}%"))
            ->when($request->role, fn ($q, $r) => $q->whereHas('role', fn ($q2) => $q2->where('name', $r)))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s));

        // Team leads only see their own team's employees
        if ($role === 'team_lead') {
            $query->where('team_id', $authUser->team_id);
        }

        $employees = $query->orderBy('name')->paginate(20)->withQueryString();
        $roles     = Role::all();
        $shifts    = Shift::where('is_active', true)->get();
        $teams     = Team::where('is_active', true)->get();
        $canEdit   = $role === 'admin';

        return view('admin.employees.index', compact('employees', 'roles', 'shifts', 'teams', 'canEdit'));
    }

    public function createEmployee()
    {
        abort_unless(Auth::user()->role?->name === 'admin', 403);
        $roles  = Role::all();
        $shifts = Shift::where('is_active', true)->get();
        $teams  = Team::where('is_active', true)->get();
        return view('admin.employees.create', compact('roles', 'shifts', 'teams'));
    }

    public function storeEmployee(Request $request)
    {
        $data = $request->validate([
            'employee_code'   => 'required|string|unique:employees',
            'name'            => 'required|string|max:100',
            'email'           => 'required|email|unique:employees',
            'password'        => 'required|string|min:8',
            'phone'           => 'nullable|string|max:20',
            'role_id'         => 'required|exists:roles,id',
            'shift_id'        => 'nullable|exists:shifts,id',
            'team_id'         => 'nullable|exists:teams,id',
            'designation'     => 'nullable|string|max:100',
            'date_of_joining' => 'nullable|date',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['status']   = 'active';

        Employee::create($data);

        return redirect()->route('admin.employees')
            ->with('success', 'Employee created successfully.');
    }

    public function editEmployee(Employee $employee)
    {
        abort_unless(Auth::user()->role?->name === 'admin', 403);
        $roles  = Role::all();
        $shifts = Shift::where('is_active', true)->get();
        $teams  = Team::where('is_active', true)->get();
        return view('admin.employees.edit', compact('employee', 'roles', 'shifts', 'teams'));
    }

    public function updateEmployee(Request $request, Employee $employee)
    {
        abort_unless(Auth::user()->role?->name === 'admin', 403);
        $data = $request->validate([
            'name'            => 'required|string|max:100',
            'phone'           => 'nullable|string|max:20',
            'role_id'         => 'required|exists:roles,id',
            'shift_id'        => 'nullable|exists:shifts,id',
            'team_id'         => 'nullable|exists:teams,id',
            'designation'     => 'nullable|string|max:100',
            'date_of_joining' => 'nullable|date',
            'status'          => 'required|in:active,inactive,on_leave',
            'new_password'    => 'nullable|string|min:8|confirmed',
        ]);

        if (!empty($data['new_password'])) {
            $data['password'] = Hash::make($data['new_password']);
        }
        unset($data['new_password'], $data['new_password_confirmation']);

        $employee->update($data);

        return redirect()->route('admin.employees')
            ->with('success', 'Employee updated.');
    }

    public function uploadEmployeeAvatar(Request $request, Employee $employee)
    {
        $request->validate(['avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048']);

        if ($employee->avatar) {
            Storage::disk('public')->delete($employee->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $employee->update(['avatar' => $path]);

        return back()->with('success', 'Profile picture updated for ' . $employee->name . '.');
    }

    // ---------- Attendance Report ----------

    public function attendanceReport(Request $request)
    {
        $month      = $request->input('month', now()->format('Y-m'));
        $employeeId = $request->input('employee_id');
        [$year, $mon] = explode('-', $month);

        $query = AttendanceLog::with(['employee', 'shift', 'workLogs'])
            ->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $mon);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $logs      = $query->orderByDesc('attendance_date')->paginate(50)->withQueryString();
        $employees = Employee::where('status', 'active')->orderBy('name')->get();
        $allowedBreak = (int) AttendanceSetting::getValue('allowed_break_minutes', 30);

        return view('admin.attendance.report', compact('logs', 'month', 'employees', 'employeeId', 'allowedBreak'));
    }

    public function exportAttendance(Request $request)
    {
        $month      = $request->input('month', now()->format('Y-m'));
        $employeeId = $request->input('employee_id');
        [$year, $mon] = explode('-', $month);

        $query = AttendanceLog::with(['employee', 'shift', 'workLogs'])
            ->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $mon);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $logs = $query->orderBy('attendance_date')->orderBy('employee_id')->get();

        $filename = 'attendance_' . $month . ($employeeId ? '_emp' . $employeeId : '') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fwrite($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'Date', 'Employee Code', 'Employee Name', 'Shift',
                'Clock In', 'Clock Out', 'Break (min)', 'Net Hours',
                'Status', 'Late', 'Work Log Comments',
            ]);

            foreach ($logs as $log) {
                $comments = $log->workLogs
                    ->map(fn ($w) => '[' . $w->created_at->format('h:i A') . '] ' . $w->comment)
                    ->implode(' | ');

                fputcsv($file, [
                    $log->attendance_date->format('d M Y'),
                    $log->employee?->employee_code ?? '',
                    $log->employee?->name ?? '',
                    $log->shift?->name ?? '',
                    $log->login_time?->format('h:i A') ?? '',
                    $log->logout_time?->format('h:i A') ?? '',
                    $log->total_break_minutes ?? 0,
                    $log->net_hours ? number_format($log->net_hours, 2) : '',
                    ucfirst(str_replace('_', ' ', $log->status ?? '')),
                    $log->is_late ? 'Yes' : 'No',
                    $comments,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ---------- Attendance Edit ----------

    public function editAttendance(AttendanceLog $log)
    {
        $log->load(['employee', 'shift', 'breaks']);
        return view('admin.attendance.edit', compact('log'));
    }

    public function updateAttendance(Request $request, AttendanceLog $log)
    {
        $data = $request->validate([
            'login_time'  => 'required|date_format:H:i',
            'logout_time' => 'nullable|date_format:H:i',
            'notes'       => 'nullable|string|max:500',
            'breaks'      => 'nullable|array',
            'breaks.*.id'          => 'nullable|integer|exists:attendance_breaks,id',
            'breaks.*.break_start' => 'required|date_format:H:i',
            'breaks.*.break_end'   => 'nullable|date_format:H:i',
        ]);

        $date = $log->attendance_date->format('Y-m-d');

        $log->login_time  = $date . ' ' . $data['login_time'] . ':00';
        $log->logout_time = $data['logout_time'] ? $date . ' ' . $data['logout_time'] . ':00' : null;
        $log->notes       = $data['notes'] ?? $log->notes;
        $log->save();

        // Update breaks
        if (!empty($data['breaks'])) {
            // Delete breaks not in submitted list
            $submittedIds = collect($data['breaks'])->pluck('id')->filter()->toArray();
            $log->breaks()->whereNotIn('id', $submittedIds)->delete();

            foreach ($data['breaks'] as $breakData) {
                $start = $date . ' ' . $breakData['break_start'] . ':00';
                $end   = !empty($breakData['break_end']) ? $date . ' ' . $breakData['break_end'] . ':00' : null;
                $duration = $end ? (int) \Carbon\Carbon::parse($start)->diffInMinutes(\Carbon\Carbon::parse($end)) : 0;

                if (!empty($breakData['id'])) {
                    $log->breaks()->where('id', $breakData['id'])->update([
                        'break_start'       => $start,
                        'break_end'         => $end,
                        'duration_minutes'  => $duration,
                    ]);
                } else {
                    $log->breaks()->create([
                        'break_start'       => $start,
                        'break_end'         => $end,
                        'duration_minutes'  => $duration,
                    ]);
                }
            }
        } else {
            $log->breaks()->delete();
        }

        // Recalculate total_break_minutes from all breaks then recalculate net hours
        $totalBreak = $log->breaks()->whereNotNull('break_end')->sum('duration_minutes');
        $log->update(['total_break_minutes' => $totalBreak]);
        $log->recalculate();

        return redirect()->route('admin.attendance', [
            'month'       => $log->attendance_date->format('Y-m'),
            'employee_id' => $log->employee_id,
        ])->with('success', 'Attendance record updated and recalculated.');
    }

    // ---------- Shift Management ----------

    public function shifts()
    {
        $shifts = Shift::orderBy('name')->get();
        return view('admin.shifts.index', compact('shifts'));
    }

    public function createShift()
    {
        return view('admin.shifts.create');
    }

    public function storeShift(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100|unique:shifts',
            'start_time'    => 'required|date_format:H:i',
            'end_time'      => 'required|date_format:H:i',
            'overnight'     => 'boolean',
            'grace_minutes' => 'required|integer|min:0|max:120',
            'is_active'     => 'boolean',
        ]);

        $data['overnight'] = $request->boolean('overnight');
        $data['is_active'] = $request->boolean('is_active', true);

        Shift::create($data);

        return redirect()->route('admin.shifts')
            ->with('success', 'Shift created successfully.');
    }

    public function editShift(Shift $shift)
    {
        return view('admin.shifts.edit', compact('shift'));
    }

    public function updateShift(Request $request, Shift $shift)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100|unique:shifts,name,' . $shift->id,
            'start_time'    => 'required|date_format:H:i',
            'end_time'      => 'required|date_format:H:i',
            'overnight'     => 'boolean',
            'grace_minutes' => 'required|integer|min:0|max:120',
            'is_active'     => 'boolean',
        ]);

        $data['overnight'] = $request->boolean('overnight');
        $data['is_active'] = $request->boolean('is_active');

        $shift->update($data);

        return redirect()->route('admin.shifts')
            ->with('success', 'Shift updated.');
    }

    // ---------- Manual Absence Alert ----------

    public function sendAbsenceAlerts(Request $request)
    {
        $data = $request->validate(['date' => 'required|date']);
        $date = Carbon::parse($data['date']);
        $dateStr = $date->toDateString();

        if ($date->isWeekend()) {
            return back()->with('error', 'Cannot send alerts for weekends.');
        }
        if (Holiday::isHoliday($date)) {
            return back()->with('error', 'Cannot send alerts for a public holiday.');
        }

        $absentEmployees = Employee::where('status', 'active')
            ->whereNotNull('email')
            ->whereDoesntHave('attendanceLogs', fn ($q) => $q->where('attendance_date', $dateStr))
            ->get();

        foreach ($absentEmployees as $employee) {
            Mail::to($employee->email)->queue(new AbsenceAlert($employee, $dateStr));
        }

        return back()->with('success', "Queued {$absentEmployees->count()} absence alert(s) for {$dateStr}.");
    }

    // ---------- Leave Balance Management ----------

    public function leaveBalances(Request $request)
    {
        $year = $request->input('year', now()->year);
        $leaveTypes = LeaveType::where('is_active', true)->get();

        $employees = Employee::where('status', 'active')
            ->with(['leaveBalances' => fn ($q) => $q->where('year', $year)->with('leaveType')])
            ->orderBy('name')
            ->get();

        // Auto-initialize missing leave balance rows for all active employees
        foreach ($employees as $emp) {
            foreach ($leaveTypes as $lt) {
                LeaveBalance::firstOrCreate(
                    ['employee_id' => $emp->id, 'leave_type_id' => $lt->id, 'year' => $year],
                    ['allocated' => 0, 'used' => 0, 'carried_forward' => 0, 'balance' => 0]
                );
            }
        }

        // Reload with freshly created rows
        $employees->each->load(['leaveBalances' => fn ($q) => $q->where('year', $year)->with('leaveType')]);

        return view('admin.leave-balances', compact('employees', 'leaveTypes', 'year'));
    }

    public function employeeLeaveBalance(Employee $employee)
    {
        $year = request('year', now()->year);
        $balances = LeaveBalance::with('leaveType')
            ->where('employee_id', $employee->id)
            ->where('year', $year)
            ->get();
        return response()->json($balances);
    }

    public function adjustLeaveBalance(Request $request, LeaveBalance $balance)
    {
        $data = $request->validate([
            'allocated'       => 'required|numeric|min:0',
            'carried_forward' => 'required|numeric|min:0',
        ]);
        $balance->update($data);
        $balance->recalculate();
        return back()->with('success', 'Leave balance updated for ' . $balance->employee->name . '.');
    }

    // ── Teams CRUD ─────────────────────────────────────────────────────────────

    public function teams()
    {
        $teams = Team::withCount('employees')->orderBy('name')->get();
        return view('admin.teams.index', compact('teams'));
    }

    public function storeTeam(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:teams,name',
            'description' => 'nullable|string|max:500',
        ]);
        $data['is_active'] = true;
        Team::create($data);
        return back()->with('success', 'Team created successfully.');
    }

    public function updateTeam(Request $request, Team $team)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:teams,name,' . $team->id,
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
        ]);
        $data['is_active'] = $request->has('is_active');
        $team->update($data);
        return back()->with('success', 'Team updated.');
    }

    public function destroyTeam(Team $team)
    {
        if ($team->employees()->exists()) {
            return back()->with('error', 'Cannot delete team — it still has employees assigned.');
        }
        $team->delete();
        return back()->with('success', 'Team deleted.');
    }
}
