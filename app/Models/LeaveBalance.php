<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    protected $fillable = ['employee_id', 'leave_type_id', 'year', 'allocated', 'used', 'carried_forward', 'balance'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function recalculate(): void
    {
        $this->balance = $this->allocated + $this->carried_forward - $this->used;
        $this->save();
    }
}
