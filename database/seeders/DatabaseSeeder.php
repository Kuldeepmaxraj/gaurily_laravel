<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Shift;
use App\Models\LeaveType;
use App\Models\AttendanceSetting;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $roles = [
            ['name' => 'employee',  'display_name' => 'Employee'],
            ['name' => 'team_lead', 'display_name' => 'Team Lead'],
            ['name' => 'hr',        'display_name' => 'HR'],
            ['name' => 'admin',     'display_name' => 'Admin'],
        ];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }

        // Shifts (US-based overnight shifts)
        $shifts = [
            ['name' => 'Day Shift',     'start_time' => '09:00:00', 'end_time' => '18:00:00', 'overnight' => false, 'grace_minutes' => 15],
            ['name' => 'Evening Shift', 'start_time' => '15:40:00', 'end_time' => '00:00:00', 'overnight' => true,  'grace_minutes' => 15],
            ['name' => 'Night Shift',   'start_time' => '19:00:00', 'end_time' => '03:00:00', 'overnight' => true,  'grace_minutes' => 15],
            ['name' => 'Late Night',    'start_time' => '21:00:00', 'end_time' => '05:00:00', 'overnight' => true,  'grace_minutes' => 15],
        ];
        foreach ($shifts as $shift) {
            Shift::firstOrCreate(['name' => $shift['name']], $shift);
        }

        // Leave Types
        $leaveTypes = [
            ['name' => 'Earned Leave',    'code' => 'EL', 'is_paid' => true,  'requires_approval' => true],
            ['name' => 'Sick Leave',      'code' => 'SL', 'is_paid' => true,  'requires_approval' => false],
            ['name' => 'Emergency Leave', 'code' => 'EM', 'is_paid' => true,  'requires_approval' => true],
            ['name' => 'Unpaid Leave',    'code' => 'UL', 'is_paid' => false, 'requires_approval' => true],
        ];
        foreach ($leaveTypes as $lt) {
            LeaveType::firstOrCreate(['code' => $lt['code']], $lt);
        }

        // Attendance Settings
        $settings = [
            ['key' => 'present_hours',        'value' => '8',   'description' => 'Minimum hours for Present status'],
            ['key' => 'half_day_hours',        'value' => '4',   'description' => 'Minimum hours for Half Day status'],
            ['key' => 'el_per_month',          'value' => '1.5', 'description' => 'Earned leaves credited per month'],
            ['key' => 'el_carry_forward_max',  'value' => '10',  'description' => 'Max earned leaves carry forward'],
            ['key' => 'working_days_per_week', 'value' => '5',   'description' => 'Working days per week'],
        ];
        foreach ($settings as $s) {
            AttendanceSetting::firstOrCreate(['key' => $s['key']], $s);
        }

        // Default admin account
        $adminRole = Role::where('name', 'admin')->first();
        Employee::firstOrCreate(
            ['email' => 'admin@gaurily.com'],
            [
                'employee_code' => 'EMP001',
                'name'          => 'Admin',
                'email'         => 'admin@gaurily.com',
                'password'      => Hash::make('Admin@1234'),
                'role_id'       => $adminRole->id,
                'designation'   => 'System Administrator',
                'status'        => 'active',
            ]
        );
    }
}
