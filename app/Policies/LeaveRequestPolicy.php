<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\LeaveRequest;

class LeaveRequestPolicy
{
    public function cancel(Employee $employee, LeaveRequest $leaveRequest): bool
    {
        return $employee->id === $leaveRequest->employee_id;
    }
}
