<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceBreak extends Model
{
    protected $fillable = ['attendance_log_id', 'break_start', 'break_end', 'duration_minutes'];

    protected function casts(): array
    {
        return [
            'break_start' => 'datetime',
            'break_end'   => 'datetime',
        ];
    }

    public function attendanceLog()
    {
        return $this->belongsTo(AttendanceLog::class);
    }

    public function end(): void
    {
        $this->break_end = now();
        $this->duration_minutes = $this->break_start->diffInMinutes($this->break_end);
        $this->save();

        // Accumulate break on parent log
        $log = $this->attendanceLog;
        $log->increment('total_break_minutes', $this->duration_minutes);
    }
}
