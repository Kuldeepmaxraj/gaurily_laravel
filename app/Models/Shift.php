<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Shift extends Model
{
    protected $fillable = ['name', 'start_time', 'end_time', 'overnight', 'grace_minutes', 'is_active'];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Determine if a given login Carbon datetime is late for this shift.
     */
    public function isLate(Carbon $loginTime): bool
    {
        $shiftStart = Carbon::parse($loginTime->format('Y-m-d') . ' ' . $this->start_time);
        $cutoff = $shiftStart->copy()->addMinutes($this->grace_minutes);
        return $loginTime->gt($cutoff);
    }
}
