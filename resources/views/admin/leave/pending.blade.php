@extends('layouts.dashboard')
@section('title', 'Leave Requests & History')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Leave Requests & History</h4>
    <div class="d-flex gap-2">
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#recordLeaveModal">
            <i class="bi bi-plus-lg me-1"></i>Record Leave
        </button>
        @if(Route::has('admin.leave.records'))
        <a href="{{ route('admin.leave.records') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-journal-check me-1"></i>Leave Records
        </a>
        @endif
    </div>
</div>

{{-- Record a leave taken without an application (e.g. emergency). Past dates allowed. --}}
<div class="modal fade" id="recordLeaveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record Leave on Behalf of Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.leave.record') }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info small py-2">
                        Use this for leave already taken (e.g. an emergency) where the employee could not apply in advance.
                        Past dates are allowed, the balance is deducted and attendance is marked as <strong>Leave</strong>.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Employee *</label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">Select employee</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->name }} ({{ $emp->employee_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Leave Type *</label>
                        <select name="leave_type_id" class="form-select" required>
                            <option value="">Select leave type</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}" {{ old('leave_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }} ({{ $type->is_paid ? 'Paid' : 'Unpaid' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">From *</label>
                            <input type="date" name="from_date" class="form-control" value="{{ old('from_date') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">To *</label>
                            <input type="date" name="to_date" class="form-control" value="{{ old('to_date') }}" required>
                        </div>
                    </div>
                    <div>
                        <label class="form-label fw-semibold">Reason *</label>
                        <textarea name="reason" class="form-control" rows="3" maxlength="500" required>{{ old('reason') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Record Leave</button>
                </div>
            </form>
        </div>
    </div>
</div>

<form class="card border-0 shadow-sm p-3 mb-4" method="GET" action="{{ route('admin.leave.pending') }}">
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
            <div class="form-text" style="font-size:11px;">Leave blank to see all months.</div>
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
            <a href="{{ route('admin.leave.pending') }}" class="btn btn-outline-secondary btn-sm w-100">Clear</a>
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
                        <th>Reason / Comment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $req->employee?->name }}</div>
                            <div class="text-muted small">{{ $req->employee?->employee_code }}</div>
                        </td>
                        <td><span class="badge bg-primary">{{ $req->leaveType?->code }}</span></td>
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
                        <td class="text-muted small" style="max-width:260px;">
                            <div>{{ Str::limit($req->reason, 80) }}</div>
                            @if($req->reviewer_comment)
                                <div class="mt-1"><strong>Comment:</strong> {{ Str::limit($req->reviewer_comment, 80) }}</div>
                            @endif
                        </td>
                        <td class="text-nowrap">
                            @if($req->status === 'pending')
                            <form method="POST" action="{{ route('admin.leave.approve', $req) }}" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-success" onclick="return confirm('Approve this leave?')">Approve</button>
                            </form>
                            <button class="btn btn-sm btn-danger ms-1"
                                data-bs-toggle="modal" data-bs-target="#rejectModal{{ $req->id }}">Reject</button>

                            {{-- Reject Modal --}}
                            <div class="modal fade" id="rejectModal{{ $req->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header"><h5 class="modal-title">Reject Leave</h5></div>
                                        <form method="POST" action="{{ route('admin.leave.reject', $req) }}">
                                            @csrf
                                            <div class="modal-body">
                                                <label class="form-label fw-semibold">Reason for rejection *</label>
                                                <textarea name="reviewer_comment" class="form-control" rows="3" required></textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Reject</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @else
                                <span class="text-muted small">No actions</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">No leave requests found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3">{{ $requests->links() }}</div>
    </div>
</div>
@endsection
