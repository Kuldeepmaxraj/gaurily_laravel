<?php

namespace App\Http\Controllers;

use App\Mail\LateLoginAlert;
use App\Models\AttendanceLog;
use App\Models\AttendanceBreak;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AttendanceController extends Controller
{
    /**
     * Show employee's attendance history page.
     */
    public function index(Request $request)
    {
        $employee = Auth::user();
        $month    = $request->input('month', now()->format('Y-m'));

        [$year, $mon] = explode('-', $month);

        $logs = $employee->attendanceLogs()
            ->with('shift')
            ->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $mon)
            ->orderByDesc('attendance_date')
            ->get();

        return view('employee.attendance.index', compact('employee', 'logs', 'month'));
    }

    /**
     * Clock In.
     */
    public function clockIn(Request $request)
    {
        $employee = Auth::user();
        $now      = Carbon::now();  // Asia/Kolkata

        // Block if there is already an open session (no clock-out yet) — covers overnight/cross-midnight
        $openSession = $employee->attendanceLogs()
            ->whereNotNull('login_time')
            ->whereNull('logout_time')
            ->latest('login_time')
            ->first();

        if ($openSession) {
            return back()->with('error',
                'You have an open session from '
                . $openSession->attendance_date->format('d M Y')
                . ' at ' . $openSession->login_time->format('h:i A')
                . '. Please clock out before starting a new session.');
        }

        // Determine attendance_date (always shift-start day for overnight)
        $shift           = $employee->shift;
        $attendanceDate  = $this->resolveAttendanceDate($now, $shift);

        $isLate = $shift ? $shift->isLate($now) : false;

        AttendanceLog::create([
            'employee_id'     => $employee->id,
            'attendance_date' => $attendanceDate,
            'shift_id'        => $shift?->id,
            'login_time'      => $now,
            'status'          => 'pending',
            'is_late'         => $isLate,
        ]);

        // Send late alert email if applicable
        if ($isLate && $employee->email) {
            $shiftStart = Carbon::parse($now->format('Y-m-d') . ' ' . $shift->start_time);
            $minutesLate = (int) $shiftStart->diffInMinutes($now);
            Mail::to($employee->email)
                ->queue(new LateLoginAlert(
                    $employee,
                    $now->format('h:i A'),
                    $shiftStart->format('h:i A'),
                    $minutesLate,
                ));
        }

        return back()->with('success', 'Clocked in at ' . $now->format('h:i A') . ($isLate ? ' (Late)' : ''));
    }

    /**
     * Start Break.
     */
    public function startBreak(Request $request)
    {
        $employee = Auth::user();
        $log      = $this->getTodayLog($employee);

        if (!$log) {
            return back()->with('error', 'You need to clock in first.');
        }
        if ($log->activeBreak()) {
            return back()->with('error', 'You already have an active break.');
        }

        AttendanceBreak::create([
            'attendance_log_id' => $log->id,
            'break_start'       => Carbon::now(),
        ]);

        return back()->with('success', 'Break started.');
    }

    /**
     * End Break.
     */
    public function endBreak(Request $request)
    {
        $employee = Auth::user();
        $log      = $this->getTodayLog($employee);

        if (!$log) {
            return back()->with('error', 'No active session.');
        }

        $break = $log->activeBreak();
        if (!$break) {
            return back()->with('error', 'No active break found.');
        }

        $break->end();

        return back()->with('success', 'Break ended.');
    }

    /**
     * Clock Out.
     */
    public function clockOut(Request $request)
    {
        $employee = Auth::user();
        $log      = $this->getTodayLog($employee);

        if (!$log || !$log->login_time) {
            return back()->with('error', 'You are not clocked in.');
        }
        if ($log->logout_time) {
            return back()->with('error', 'You have already clocked out.');
        }

        // End any open break first
        $break = $log->activeBreak();
        if ($break) {
            $break->end();
            $log->refresh();
        }

        $log->logout_time = Carbon::now();
        $log->save();
        $log->recalculate();

        return back()->with('success', 'Clocked out. Total hours: ' . $log->fresh()->net_hours . 'h');
    }

    // ---------- Helpers ----------

    private function getTodayLog($employee): ?AttendanceLog
    {
        // First: return any open (not yet clocked-out) session — handles overnight / cross-midnight
        $open = $employee->attendanceLogs()
            ->whereNotNull('login_time')
            ->whereNull('logout_time')
            ->latest('login_time')
            ->first();

        if ($open) {
            return $open;
        }

        // Fallback: return today's completed log
        $shift          = $employee->shift;
        $now            = Carbon::now();
        $attendanceDate = $this->resolveAttendanceDate($now, $shift);

        return $employee->attendanceLogs()
            ->where('attendance_date', $attendanceDate)
            ->first();
    }

    /**
     * For overnight shifts the attendance_date is always the day login occurs,
     * not the day logout occurs.
     */
    private function resolveAttendanceDate(Carbon $now, $shift): string
    {
        return $now->toDateString();
    }
}
