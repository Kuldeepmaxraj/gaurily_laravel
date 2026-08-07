@extends('layouts.dashboard')

@section('title', 'Leave Records')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Leave Records</h4>
    <a href="{{ route('admin.leave.pending') }}" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-hourglass-split me-1"></i>Pending Requests
    </a>
</div>

<form class="card border-0 shadow-sm p-3 mb-4" method="GET" action="{{ route('admin.leave.records') }}">
    <div class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label small fw-semibold mb-1">Employee</label>
            <select name="employee_id" class="form-select form-select-sm">
                <option value="">All Employees</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ (int) request('employee_id') === $emp->id ? 'selected' : '' }}>
                        {{ $emp->name }} ({{ $emp->employee_code }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold mb-1">Month</label>
            <input type="month" name="month" class="form-control form-control-sm" value="{{ $month }}">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold mb-1">Status</label>
            <select name="status" class="form-select form-select-sm">
                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-primary btn-sm w-100">Filter</button>
            <a href="{{ route('admin.leave.records') }}" class="btn btn-outline-secondary btn-sm w-100">Clear</a>
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Employee</th>
                        <th>Type</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Days</th>
                        <th>Status</th>
                        <th>Reviewed By</th>
                        <th>Comment</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $req->employee?->name }}</div>
                            <div class="text-muted small">{{ $req->employee?->employee_code }}</div>
                        </td>
                        <td>{{ $req->leaveType?->name }}</td>
                        <td>{{ $req->from_date->format('d M Y') }}</td>
                        <td>{{ $req->to_date->format('d M Y') }}</td>
                        <td>{{ $req->total_days }}</td>
                        <td>
                            <span class="badge bg-{{ match($req->status) { 'approved' => 'success', 'rejected' => 'danger', 'cancelled' => 'secondary', default => 'warning text-dark' } }}">
                                {{ ucfirst($req->status) }}
                            </span>
                        </td>
                        <td>
                            @if($req->reviewer)
                                <div class="small fw-semibold">{{ $req->reviewer->name }}</div>
                                <div class="text-muted" style="font-size:11px;">{{ $req->reviewed_at?->format('d M Y h:i A') }}</div>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="text-muted small" style="max-width:240px;">{{ $req->reviewer_comment ?: '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No leave records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3">{{ $requests->links() }}</div>
    </div>
</div>
@endsection
