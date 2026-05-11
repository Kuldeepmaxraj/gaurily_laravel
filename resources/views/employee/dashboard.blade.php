@extends('layouts.dashboard')

@section('title', 'My Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Welcome, {{ $employee->name }}</h4>
        <small class="text-muted">{{ $employee->designation }} &bull; {{ $employee->role?->display_name }} &bull; {{ $employee->shift?->name ?? 'No shift assigned' }}</small>
    </div>
    <span class="badge bg-{{ $employee->status === 'active' ? 'success' : 'secondary' }} fs-6">{{ ucfirst($employee->status) }}</span>
</div>

{{-- Clock-In / Out Card --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-clock me-2 text-primary"></i>Today's Attendance</h5>

        <div class="row g-3 align-items-center">
            <div class="col-md-4">
                <div class="text-muted small">Clock In</div>
                <div class="fw-semibold fs-5">{{ $todayLog?->login_time?->format('h:i A') ?? '—' }}</div>
                @if($todayLog?->is_late)
                    <span class="badge bg-warning text-dark">Late</span>
                @endif
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Clock Out</div>
                <div class="fw-semibold fs-5">{{ $todayLog?->logout_time?->format('h:i A') ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Net Hours</div>
                <div class="fw-semibold fs-5">{{ $todayLog?->net_hours ? floor($todayLog->net_hours).'h '.round(($todayLog->net_hours - floor($todayLog->net_hours)) * 60).'m' : '—' }}</div>
                @if($todayLog?->status)
                    <span class="badge bg-{{ match($todayLog->status) { 'present'=>'success','half_day'=>'warning','absent'=>'danger',default=>'secondary'} }}">
                        {{ ucfirst(str_replace('_',' ',$todayLog->status)) }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Break summary bar --}}
        @if($todayLog && $todayLog->login_time && !$todayLog->logout_time)
        @php
            $activeBreak    = $todayLog->activeBreak();
            $liveBreakMins  = $activeBreak ? (int) \Carbon\Carbon::parse($activeBreak->break_start)->diffInMinutes(now()) : 0;
            $breakUsed      = ($todayLog->total_break_minutes ?? 0) + $liveBreakMins;
            $breakPct       = $allowedBreak > 0 ? min(100, round($breakUsed / $allowedBreak * 100)) : 100;
            $breakOver      = max(0, $breakUsed - $allowedBreak);
            $barColor       = $breakOver > 0 ? '#ef4444' : ($breakPct >= 80 ? '#f59e0b' : '#22c55e');
        @endphp
        <div class="mt-3 p-3 rounded-3" style="background:#f8fafc;border:1px solid #eef0f6;">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small fw-medium" style="color:#374151;">
                    Break Used
                    @if($liveBreakMins > 0)
                        <span class="badge ms-1" style="background:#eff6ff;color:#0066FF;font-size:10px;">● On break</span>
                    @endif
                </span>
                <span class="small fw-semibold {{ $breakOver > 0 ? 'text-danger' : 'text-muted' }}">
                    {{ $breakUsed }}m used &mdash;
                    @if($breakOver > 0)
                        <span class="text-danger">{{ $breakOver }}m over (will be deducted from net hours)</span>
                    @else
                        {{ $allowedBreak - $breakUsed }}m remaining
                    @endif
                </span>
            </div>
            <div class="progress" style="height:6px;border-radius:4px;background:#e5e7eb;">
                <div class="progress-bar" style="width:{{ $breakPct }}%;background:{{ $barColor }};border-radius:4px;transition:width .4s;"></div>
            </div>
            <div class="text-muted mt-1" style="font-size:11px;">{{ $allowedBreak }}m free break allowed per day</div>
        </div>
        @endif

        <div class="mt-4 d-flex flex-wrap gap-2">
            @if(!$todayLog || !$todayLog->login_time)
                <form method="POST" action="{{ route('employee.attendance.clock-in') }}">@csrf
                    <button class="btn btn-success"><i class="bi bi-play-fill me-1"></i>Clock In</button>
                </form>
            @elseif(!$todayLog->logout_time)
                @if(!$todayLog->activeBreak())
                    <form method="POST" action="{{ route('employee.attendance.break-start') }}">@csrf
                        <button class="btn btn-warning"><i class="bi bi-pause-fill me-1"></i>Start Break</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('employee.attendance.break-end') }}">@csrf
                        <button class="btn btn-info text-white"><i class="bi bi-play-fill me-1"></i>End Break</button>
                    </form>
                @endif
                <form method="POST" action="{{ route('employee.attendance.clock-out') }}">@csrf
                    <button class="btn btn-danger"><i class="bi bi-stop-fill me-1"></i>Clock Out</button>
                </form>
            @else
                <span class="text-muted">Shift completed for today.</span>
            @endif
        </div>
    </div>
</div>

{{-- Monthly Stats --}}
<div class="row g-3 mb-4">
    @foreach([
        ['label'=>'Present Days','value'=>$presentDays,'icon'=>'bi-check-circle','color'=>'success'],
        ['label'=>'Half Days','value'=>$halfDays,'icon'=>'bi-dash-circle','color'=>'warning'],
        ['label'=>'Late Arrivals','value'=>$lateDays,'icon'=>'bi-exclamation-circle','color'=>'orange'],
        ['label'=>'Absent Days','value'=>$absentDays,'icon'=>'bi-x-circle','color'=>'danger'],
        ['label'=>'Pending Leaves','value'=>$pendingLeaves,'icon'=>'bi-hourglass-split','color'=>'secondary'],
    ] as $stat)
    <div class="col-6 col-md-2-4">
        <div class="card border-0 shadow-sm stat-card text-center p-3">
            <i class="bi {{ $stat['icon'] }} fs-3 text-{{ $stat['color'] }}"></i>
            <div class="fw-bold fs-4 mt-1">{{ $stat['value'] }}</div>
            <div class="text-muted small">{{ $stat['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Leave Balances --}}
@if($leaveBalances->count())
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h5 class="fw-bold mb-3"><i class="bi bi-calendar3 me-2 text-primary"></i>Leave Balances ({{ now()->year }})</h5>
        <div class="row g-3">
            @foreach($leaveBalances as $bal)
            <div class="col-6 col-md-3">
                <div class="border rounded p-3 text-center">
                    <div class="text-muted small">{{ $bal->leaveType->name }}</div>
                    <div class="fw-bold fs-4">{{ $bal->balance }}</div>
                    <div class="text-muted" style="font-size:11px">{{ $bal->used }} used / {{ $bal->allocated + $bal->carried_forward }} total</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Work Log --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h5 class="fw-bold mb-3"><i class="bi bi-journal-text me-2 text-primary"></i>What are you working on?</h5>

        @if($todayLog && $todayLog->login_time && !$todayLog->logout_time)
        <form method="POST" action="{{ route('employee.work-log.store') }}" class="mb-4">
            @csrf
            <div class="d-flex gap-2">
                <textarea name="comment" rows="2" class="form-control @error('comment') is-invalid @enderror"
                    placeholder="Describe what you're working on right now…" required maxlength="1000">{{ old('comment') }}</textarea>
                <button class="btn btn-primary align-self-end px-3" style="white-space:nowrap;">
                    <i class="bi bi-send me-1"></i> Submit
                </button>
            </div>
            @error('comment')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </form>
        @else
        <p class="text-muted small mb-4">Clock in to start logging your work.</p>
        @endif

        @if($todayWorkLogs->count())
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:130px;">Time</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($todayWorkLogs as $log)
                    <tr>
                        <td class="text-muted small align-middle">{{ $log->created_at->format('h:i A') }}</td>
                        <td style="white-space:pre-wrap;word-break:break-word;">{{ $log->comment }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-muted small mb-0">No updates logged today.</p>
        @endif
    </div>
</div>

{{-- Upcoming Holidays --}}
@if($upcomingHolidays->count())
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h5 class="fw-bold mb-3"><i class="bi bi-calendar-heart me-2 text-primary"></i>Upcoming Holidays</h5>
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead class="table-light">
                    <tr><th>Date</th><th>Day</th><th>Holiday</th><th>Type</th></tr>
                </thead>
                <tbody>
                    @foreach($upcomingHolidays as $holiday)
                    <tr>
                        <td class="align-middle fw-medium small">{{ \Carbon\Carbon::parse($holiday->holiday_date)->format('d M Y') }}</td>
                        <td class="align-middle text-muted small">{{ \Carbon\Carbon::parse($holiday->holiday_date)->format('l') }}</td>
                        <td class="align-middle fw-semibold small">{{ $holiday->name }}</td>
                        <td class="align-middle">
                            <span class="badge rounded-pill {{ $holiday->is_optional ? 'bg-warning text-dark' : 'bg-success' }}" style="font-size:11px;">
                                {{ $holiday->is_optional ? 'Optional' : 'Public' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@endsection

@push('styles')
<style>
    .col-md-2-4 { flex: 0 0 auto; width: 20%; }
    @media(max-width:768px) { .col-md-2-4 { width: 50%; } }
</style>
@endpush
