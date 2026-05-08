<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EmployeeDashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\WorkLogController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;

// ─── Public website pages ────────────────────────────────────────────────────
Route::get('/',          fn () => view('pages.index'))->name('home');
Route::get('/about',     fn () => view('pages.about'))->name('about');
Route::get('/services',  fn () => view('pages.services'))->name('services');
Route::get('/careers',   fn () => view('pages.careers'))->name('careers');
Route::get('/blog',      [BlogController::class, 'index'])->name('blogs');
Route::get('/blog/{blog:slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/contact',   fn () => view('pages.contact'))->name('contact');

// ─── Auth ─────────────────────────────────────────────────────────────────────
Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ─── Employee (logged-in) ─────────────────────────────────────────────────────
Route::middleware('auth')->prefix('dashboard')->name('employee.')->group(function () {
    Route::get('/',  [EmployeeDashboardController::class, 'index'])->name('dashboard');

    // Attendance
    Route::post('/attendance/clock-in',   [AttendanceController::class, 'clockIn'])->name('attendance.clock-in');
    Route::post('/attendance/break-start',[AttendanceController::class, 'startBreak'])->name('attendance.break-start');
    Route::post('/attendance/break-end',  [AttendanceController::class, 'endBreak'])->name('attendance.break-end');
    Route::post('/attendance/clock-out',  [AttendanceController::class, 'clockOut'])->name('attendance.clock-out');
    Route::get('/attendance',             [AttendanceController::class, 'index'])->name('attendance.history');

    // Leave
    Route::get('/leave',              [LeaveController::class, 'index'])->name('leave.index');
    Route::post('/leave/apply',       [LeaveController::class, 'apply'])->name('leave.apply');
    Route::post('/leave/{leaveRequest}/cancel', [LeaveController::class, 'cancel'])->name('leave.cancel');

    // Profile
    Route::get('/profile',            [EmployeeDashboardController::class, 'profile'])->name('profile');
    Route::put('/profile/password',   [EmployeeDashboardController::class, 'changePassword'])->name('profile.password');
    Route::post('/profile/avatar',    [EmployeeDashboardController::class, 'uploadAvatar'])->name('profile.avatar');

    // Work logs
    Route::post('/work-log',          [WorkLogController::class, 'store'])->name('work-log.store');

    // Holidays (read-only)
    Route::get('/holidays',           [EmployeeDashboardController::class, 'holidays'])->name('holidays');
});

// ─── Admin / HR ───────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin,hr,team_lead'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/',               [AdminController::class, 'dashboard'])->name('dashboard');

    // Employees
    Route::get('/employees',             [AdminController::class, 'employees'])->name('employees');
    Route::get('/employees/create',      [AdminController::class, 'createEmployee'])->name('employees.create');
    Route::post('/employees',            [AdminController::class, 'storeEmployee'])->name('employees.store');
    Route::get('/employees/{employee}/edit',   [AdminController::class, 'editEmployee'])->name('employees.edit');
    Route::put('/employees/{employee}',        [AdminController::class, 'updateEmployee'])->name('employees.update');
    Route::post('/employees/{employee}/avatar',[AdminController::class, 'uploadEmployeeAvatar'])->name('employees.avatar');

    // Blogs
    Route::middleware('role:admin,hr')->group(function () {
        Route::get('/blogs',               [AdminBlogController::class, 'index'])->name('blogs.index');
        Route::get('/blogs/create',        [AdminBlogController::class, 'create'])->name('blogs.create');
        Route::post('/blogs',              [AdminBlogController::class, 'store'])->name('blogs.store');
        Route::get('/blogs/{blog}/edit',   [AdminBlogController::class, 'edit'])->name('blogs.edit');
        Route::put('/blogs/{blog}',        [AdminBlogController::class, 'update'])->name('blogs.update');
        Route::delete('/blogs/{blog}',     [AdminBlogController::class, 'destroy'])->name('blogs.destroy');
    });

    // Teams
    Route::get('/teams',              [AdminController::class, 'teams'])->name('teams');
    Route::post('/teams',             [AdminController::class, 'storeTeam'])->name('teams.store');
    Route::put('/teams/{team}',       [AdminController::class, 'updateTeam'])->name('teams.update');
    Route::delete('/teams/{team}',    [AdminController::class, 'destroyTeam'])->name('teams.destroy');

    // Attendance report
    Route::get('/attendance',        [AdminController::class, 'attendanceReport'])->name('attendance');
    Route::get('/attendance/export', [AdminController::class, 'exportAttendance'])->name('attendance.export');
    Route::middleware('role:admin,hr')->group(function () {
        Route::get('/shifts',                    [AdminController::class, 'shifts'])->name('shifts');
        Route::get('/shifts/create',             [AdminController::class, 'createShift'])->name('shifts.create');
        Route::post('/shifts',                   [AdminController::class, 'storeShift'])->name('shifts.store');
        Route::get('/shifts/{shift}/edit',       [AdminController::class, 'editShift'])->name('shifts.edit');
        Route::put('/shifts/{shift}',            [AdminController::class, 'updateShift'])->name('shifts.update');
    });

    // Leave approval (admin/hr only)
    Route::middleware('role:admin,hr')->group(function () {
        Route::get('/leave/pending',                 [LeaveController::class, 'pending'])->name('leave.pending');
        Route::post('/leave/{leaveRequest}/approve', [LeaveController::class, 'approve'])->name('leave.approve');
        Route::post('/leave/{leaveRequest}/reject',  [LeaveController::class, 'reject'])->name('leave.reject');

        // Holidays
        Route::get('/holidays',             [HolidayController::class, 'index'])->name('holidays');
        Route::post('/holidays',            [HolidayController::class, 'store'])->name('holidays.store');
        Route::delete('/holidays/{holiday}',[HolidayController::class, 'destroy'])->name('holidays.destroy');

        // Attendance settings
        Route::get('/settings',  [SettingsController::class, 'index'])->name('settings');
        Route::put('/settings',  [SettingsController::class, 'update'])->name('settings.update');

        // Leave balances
        Route::get('/leave-balances',            [AdminController::class, 'leaveBalances'])->name('leave-balances');
        Route::get('/leave-balances/{employee}', [AdminController::class, 'employeeLeaveBalance'])->name('leave-balances.employee');
        Route::put('/leave-balances/{balance}/adjust', [AdminController::class, 'adjustLeaveBalance'])->name('leave-balances.adjust');

        // Manual absence alert
        Route::post('/attendance/send-alerts', [AdminController::class, 'sendAbsenceAlerts'])->name('attendance.send-alerts');
    });
});

