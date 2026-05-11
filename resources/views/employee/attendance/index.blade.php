@extends('layouts.dashboard')

@section('title', 'My Attendance')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">My Attendance History</h4>
    <form class="d-flex gap-2">
        <input type="month" name="month" class="form-control form-control-sm" value="{{ $month }}">
        <button class="btn btn-sm btn-primary">Filter</button>
    </form>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Shift</th>
                        <th>Clock In</th>
                        <th>Clock Out</th>
                        <th>Break</th>
                        <th>Net Hours</th>
                        <th>Status</th>
                        <th>Late?</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->attendance_date->format('d M Y') }}</td>
                        <td>{{ $log->shift?->name ?? '—' }}</td>
                        <td>{{ $log->login_time?->format('h:i A') ?? '—' }}</td>
                        <td>{{ $log->logout_time?->format('h:i A') ?? '—' }}</td>
                        <td>{{ $log->total_break_minutes ? $log->total_break_minutes.'m' : '0m' }}</td>
                        <td>@if($log->net_hours){{ floor($log->net_hours) }}h {{ round(($log->net_hours - floor($log->net_hours)) * 60) }}m@else —@endif</td>
                        <td>
                            <span class="badge bg-{{ match($log->status) { 'present'=>'success','half_day'=>'warning','absent'=>'danger','holiday'=>'info','leave'=>'primary',default=>'secondary'} }}">
                                {{ ucfirst(str_replace('_',' ',$log->status)) }}
                            </span>
                        </td>
                        <td>
                            @if($log->is_late)
                                <span class="badge bg-warning text-dark">Yes</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No attendance records found for this period.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
