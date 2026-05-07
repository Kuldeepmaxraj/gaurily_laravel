@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')

@section('content')
<h4 class="fw-bold mb-4">Admin Dashboard</h4>

<div class="row g-3 mb-4">
    @foreach([
        ['label'=>'Total Employees','value'=>$totalEmployees,'icon'=>'bi-people','color'=>'primary'],
        ['label'=>'Present Today','value'=>$presentToday,'icon'=>'bi-check-circle','color'=>'success'],
        ['label'=>'Half Day Today','value'=>$halfDayToday,'icon'=>'bi-dash-circle','color'=>'warning'],
        ['label'=>'Late Today','value'=>$lateToday,'icon'=>'bi-exclamation-circle','color'=>'orange'],
        ['label'=>'Pending Leaves','value'=>$pendingLeaves,'icon'=>'bi-hourglass','color'=>'danger'],
    ] as $stat)
    <div class="col-6 col-md">
        <div class="card border-0 shadow-sm text-center p-3">
            <i class="bi {{ $stat['icon'] }} fs-2 text-{{ $stat['color'] }}"></i>
            <div class="fw-bold fs-3 mt-1">{{ $stat['value'] }}</div>
            <div class="text-muted small">{{ $stat['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4">
    {{-- Recent Attendance --}}
    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold border-0 pt-4 px-4">
                Today's Clock-Ins
                <a href="{{ route('admin.attendance') }}" class="btn btn-sm btn-outline-primary float-end">View All</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Employee</th><th>Clock In</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse($recentAttendance as $log)
                        <tr>
                            <td>{{ $log->employee?->name }}</td>
                            <td>{{ $log->login_time?->format('h:i A') }}</td>
                            <td>
                                <span class="badge bg-{{ $log->is_late ? 'warning text-dark' : 'success' }}">
                                    {{ $log->is_late ? 'Late' : 'On Time' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted py-3">No clock-ins yet today.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pending Leaves --}}
    <div class="col-md-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold border-0 pt-4 px-4">
                Pending Leave Requests
                <a href="{{ route('admin.leave.pending') }}" class="btn btn-sm btn-outline-primary float-end">View All</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Employee</th><th>Type</th><th>Days</th></tr></thead>
                    <tbody>
                        @forelse($pendingLeaveList as $req)
                        <tr>
                            <td>{{ $req->employee?->name }}</td>
                            <td>{{ $req->leaveType?->code }}</td>
                            <td>{{ $req->total_days }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted py-3">No pending requests.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
