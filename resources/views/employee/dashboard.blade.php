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
                <div class="fw-semibold fs-5">{{ $todayLog?->net_hours ? number_format($todayLog->net_hours, 2).'h' : '—' }}</div>
                @if($todayLog?->status)
                    <span class="badge bg-{{ match($todayLog->status) { 'present'=>'success','half_day'=>'warning','absent'=>'danger',default=>'secondary'} }}">
                        {{ ucfirst(str_replace('_',' ',$todayLog->status)) }}
                    </span>
                @endif
            </div>
        </div>

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
                        <td class="align-middle fw-medium small">{{ $holiday->date->format('d M Y') }}</td>
                        <td class="align-middle text-muted small">{{ $holiday->date->format('l') }}</td>
                        <td class="align-middle fw-semibold small">{{ $holiday->name }}</td>
                        <td class="align-middle">
                            <span class="badge rounded-pill {{ $holiday->type === 'public' ? 'bg-success' : 'bg-warning text-dark' }}" style="font-size:11px;">
                                {{ ucfirst($holiday->type ?? 'Public') }}
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
