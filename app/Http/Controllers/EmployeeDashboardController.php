<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\LeaveRequest;
use App\Models\EmployeeNotification;
use App\Models\WorkLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EmployeeDashboardController extends Controller
{
    public function index()
    {
        $employee       = Auth::user()->load(['shift', 'role', 'team']);
        $todayLog       = $employee->todayAttendance();
        $shift          = $employee->shift;

        $todayWorkLogs  = $todayLog
            ? WorkLog::where('attendance_log_id', $todayLog->id)->latest()->get()
            : collect();

        $monthLogs = $employee->attendanceLogs()
            ->whereYear('attendance_date', now()->year)
            ->whereMonth('attendance_date', now()->month)
            ->get();

        $presentDays  = $monthLogs->where('status', 'present')->count();
        $halfDays     = $monthLogs->where('status', 'half_day')->count();
        $lateDays     = $monthLogs->where('is_late', true)->count();
        $absentDays   = $monthLogs->where('status', 'absent')->count();

        $pendingLeaves = $employee->leaveRequests()->where('status', 'pending')->count();
        $leaveBalances = $employee->leaveBalances()->with('leaveType')->where('year', now()->year)->get();

        $notifications = $employee->notifications()
            ->where('is_read', false)
            ->latest()
            ->limit(5)
            ->get();

        return view('employee.dashboard', compact(
            'employee', 'todayLog', 'shift',
            'presentDays', 'halfDays', 'lateDays', 'absentDays',
            'pendingLeaves', 'leaveBalances', 'notifications', 'todayWorkLogs'
        ));
    }

    public function profile()
    {
        $employee = Auth::user()->load(['shift', 'role', 'team']);
        return view('employee.profile', compact('employee'));
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate(['avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048']);

        $employee = Auth::user();

        if ($employee->avatar) {
            Storage::disk('public')->delete($employee->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $employee->update(['avatar' => $path]);

        return back()->with('success', 'Profile picture updated.');
    }

    public function changePassword(Request $request)
    {        $data = $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $employee = Auth::user();

        if (!Hash::check($data['current_password'], $employee->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $employee->update(['password' => Hash::make($data['password'])]);

        return back()->with('success', 'Password changed successfully.');
    }
}
