@extends('layouts.dashboard')
@section('title', 'Pending Leave Requests')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Pending Leave Requests</h4>
    <a href="{{ route('admin.leave.records') }}" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-journal-check me-1"></i>Leave Records
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Employee</th><th>Type</th><th>From</th><th>To</th><th>Days</th><th>Reason</th><th>Actions</th>
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
                        <td class="text-muted small" style="max-width:200px">{{ Str::limit($req->reason, 60) }}</td>
                        <td>
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
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No pending leave requests.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3">{{ $requests->links() }}</div>
    </div>
</div>
@endsection
