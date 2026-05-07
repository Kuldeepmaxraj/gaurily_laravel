<?php

namespace App\Console\Commands;

use App\Mail\AbsenceAlert;
use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendAttendanceAlerts extends Command
{
    protected $signature   = 'attendance:send-alerts {--date= : Date in Y-m-d format, defaults to today}';
    protected $description = 'Send absence alert emails to employees who did not clock in on a working day';

    public function handle(): int
    {
        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))
            : today();

        // Skip weekends
        if ($date->isWeekend()) {
            $this->info("Skipping {$date->toDateString()} — weekend.");
            return 0;
        }

        // Skip public holidays
        if (Holiday::isHoliday($date)) {
            $this->info("Skipping {$date->toDateString()} — public holiday.");
            return 0;
        }

        $dateStr = $date->toDateString();

        // All active employees with an email who did NOT have any attendance log that day
        $absentEmployees = Employee::where('status', 'active')
            ->whereNotNull('email')
            ->whereDoesntHave('attendanceLogs', function ($q) use ($dateStr) {
                $q->where('attendance_date', $dateStr);
            })
            ->get();

        if ($absentEmployees->isEmpty()) {
            $this->info("No absent employees found for {$dateStr}.");
            return 0;
        }

        foreach ($absentEmployees as $employee) {
            try {
                Mail::to($employee->email)
                    ->queue(new AbsenceAlert($employee, $dateStr));
                $this->info("Queued absence alert → {$employee->email}");
            } catch (\Throwable $e) {
                $this->error("Failed for {$employee->email}: {$e->getMessage()}");
            }
        }

        $this->info("Done — queued {$absentEmployees->count()} absence alert(s).");
        return 0;
    }
}
