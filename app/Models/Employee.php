<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $fillable = [
        'employee_code', 'name', 'email', 'password',
        'phone', 'role_id', 'team_id', 'shift_id',
        'designation', 'date_of_joining', 'status', 'profile_photo', 'avatar',
        'last_seen_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'date_of_joining' => 'date',
            'password'        => 'hashed',
            'last_seen_at'    => 'datetime',
        ];
    }

    // ---------- Relationships ----------

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function notifications()
    {
        return $this->hasMany(EmployeeNotification::class);
    }

    public function workLogs()
    {
        return $this->hasMany(WorkLog::class);
    }

    // ---------- Helpers ----------

    public function hasRole(string $role): bool
    {
        return self::normalizeRoleName($this->role?->name) === self::normalizeRoleName($role);
    }

    public function hasAnyRole(array $roles): bool
    {
        $current = self::normalizeRoleName($this->role?->name);
        return in_array($current, array_map([self::class, 'normalizeRoleName'], $roles), true);
    }

    public function isAdmin(): bool
    {
        return $this->hasAnyRole(['admin', 'hr']);
    }

    public function todayAttendance()
    {
        // Prefer any open (not clocked-out) session — handles overnight / cross-midnight shifts
        $open = $this->attendanceLogs()
            ->whereNotNull('login_time')
            ->whereNull('logout_time')
            ->latest('login_time')
            ->first();

        if ($open) {
            return $open;
        }

        return $this->attendanceLogs()->where('attendance_date', today())->first();
    }

    public function leaveBalance(string $leaveTypeCode)
    {
        return $this->leaveBalances()
            ->whereHas('leaveType', fn ($q) => $q->where('code', $leaveTypeCode))
            ->where('year', now()->year)
            ->first();
    }

    public function chatRoomMembers()
    {
        return $this->hasMany(ChatRoomMember::class);
    }

    public function isOnline(): bool
    {
        return $this->last_seen_at && $this->last_seen_at->gte(now()->subMinutes(5));
    }

    private static function normalizeRoleName(?string $role): string
    {
        if (!$role) {
            return '';
        }

        return str_replace([' ', '-'], '_', strtolower(trim($role)));
    }
}
