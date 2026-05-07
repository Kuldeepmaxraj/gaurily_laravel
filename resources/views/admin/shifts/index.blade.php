@extends('layouts.dashboard')
@section('title', 'Shift Management')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Shift Management</h4>
    <a href="{{ route('admin.shifts.create') }}" class="btn btn-primary btn-sm rounded-pill px-3">
        <i class="bi bi-plus me-1"></i> Add Shift
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th>Shift Name</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Type</th>
                    <th>Grace</th>
                    <th>Employees</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($shifts as $shift)
                <tr>
                    <td class="fw-semibold">{{ $shift->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }}</td>
                    <td>{{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}</td>
                    <td>
                        @if($shift->overnight)
                            <span class="badge bg-info text-dark">Overnight</span>
                        @else
                            <span class="badge bg-light text-secondary border">Day</span>
                        @endif
                    </td>
                    <td>{{ $shift->grace_minutes }} min</td>
                    <td>{{ $shift->employees()->count() }}</td>
                    <td>
                        @if($shift->is_active)
                            <span class="badge bg-success-subtle text-success">Active</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.shifts.edit', $shift) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Edit</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">No shifts found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card border-0 shadow-sm mt-4 p-4" style="background:#eff6ff;">
    <h6 class="fw-semibold mb-1" style="color:#0066FF;"><i class="bi bi-info-circle me-1"></i> How Shifts Work</h6>
    <ul class="mb-0 small text-muted ps-3 mt-2">
        <li>Employees are assigned a shift on their profile. Their check-in is compared against <strong>Start Time</strong>.</li>
        <li><strong>Grace</strong> — check-ins within this window after start time are not flagged as late.</li>
        <li><strong>Overnight</strong> — shift crosses midnight (e.g. 7 PM – 3 AM). Attendance date is always the login day.</li>
        <li>Late check-ins automatically trigger an email alert to the employee.</li>
        <li>The absence alert runs daily at 11 PM and emails anyone who never checked in that day.</li>
    </ul>
</div>
@endsection
