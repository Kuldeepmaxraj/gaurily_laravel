<?php

namespace App\Http\Controllers;

use App\Models\WorkLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkLogController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['comment' => 'required|string|max:1000']);

        $employee  = Auth::user();
        $todayLog  = $employee->todayAttendance();

        // Only allow if clocked in and not yet clocked out
        if (!$todayLog || !$todayLog->login_time || $todayLog->logout_time) {
            return back()->with('error', 'You can only add work updates while clocked in.');
        }

        WorkLog::create([
            'employee_id'       => $employee->id,
            'attendance_log_id' => $todayLog->id,
            'comment'           => $request->comment,
        ]);

        return back()->with('success', 'Work update added.');
    }
}
