<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AttendanceLog extends Model
{
    protected $fillable = [
        'employee_id', 'attendance_date', 'shift_id',
        'login_time', 'logout_time', 'total_break_minutes',
        'net_hours', 'status', 'is_late', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
            'login_time'      => 'datetime',
            'logout_time'     => 'datetime',
            'is_late'         => 'boolean',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function breaks()
    {
        return $this->hasMany(AttendanceBreak::class);
    }

    public function workLogs()
    {
        return $this->hasMany(WorkLog::class);
    }

    public function activeBreak()
    {
        return $this->breaks()->whereNull('break_end')->latest()->first();
    }

    /**
     * Recalculate and save net_hours and status based on current login/logout/breaks.
     */
    public function recalculate(): void
    {
        if (!$this->login_time || !$this->logout_time) {
            return;
        }

        $totalMinutes = $this->login_time->diffInMinutes($this->logout_time);
        $netMinutes   = $totalMinutes - $this->total_break_minutes;
        $netHours     = round($netMinutes / 60, 2);

        $presentThreshold  = (float) AttendanceSetting::getValue('present_hours', 8);
        $halfDayThreshold  = (float) AttendanceSetting::getValue('half_day_hours', 4);

        if ($netHours >= $presentThreshold) {
            $status = 'present';
        } elseif ($netHours >= $halfDayThreshold) {
            $status = 'half_day';
        } else {
            $status = 'absent';
        }

        $this->update(['net_hours' => $netHours, 'status' => $status]);
    }
}
