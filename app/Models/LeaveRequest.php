<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LeaveRequest extends Model
{
    protected $fillable = [
        'employee_id', 'leave_type_id', 'from_date', 'to_date',
        'total_days', 'reason', 'status', 'reviewed_by', 'reviewer_comment', 'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'from_date'   => 'date',
            'to_date'     => 'date',
            'reviewed_at' => 'datetime',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(Employee::class, 'reviewed_by');
    }

    /**
     * Calculate working days (excluding weekends & holidays) between two dates.
     */
    public static function calculateWorkingDays(Carbon $from, Carbon $to): float
    {
        $days  = 0;
        $current = $from->copy();
        while ($current->lte($to)) {
            if (!$current->isWeekend() && !Holiday::isHoliday($current)) {
                $days++;
            }
            $current->addDay();
        }
        return (float) $days;
    }
}
