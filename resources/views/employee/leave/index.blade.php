@extends('layouts.dashboard')

@section('title', 'My Leaves')

@section('content')
<div class="row g-4">

    {{-- Apply Leave --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Apply Leave</h5>
                <form method="POST" action="{{ route('employee.leave.apply') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Leave Type</label>
                        <select name="leave_type_id" class="form-select" required>
                            <option value="">Select...</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}">{{ $type->name }} ({{ $type->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">From Date</label>
                        <input type="date" name="from_date" class="form-control" required min="{{ today()->toDateString() }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">To Date</label>
                        <input type="date" name="to_date" class="form-control" required min="{{ today()->toDateString() }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Reason</label>
                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                    </div>
                    <button class="btn btn-primary w-100">Submit Application</button>
                </form>

                {{-- Leave Balances --}}
                @if($balances->count())
                <hr>
                <h6 class="fw-bold">Balances ({{ now()->year }})</h6>
                @foreach($balances as $bal)
                <div class="d-flex justify-content-between small py-1 border-bottom">
                    <span>{{ $bal->leaveType->name }}</span>
                    <span class="fw-bold">{{ $bal->balance }} days</span>
                </div>
                @endforeach
                @endif
            </div>
        </div>
    </div>

    {{-- Leave History --}}
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">My Leave History</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Type</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Days</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $req)
                            <tr>
                                <td>{{ $req->leaveType->name }}</td>
                                <td>{{ $req->from_date->format('d M Y') }}</td>
                                <td>{{ $req->to_date->format('d M Y') }}</td>
                                <td>{{ $req->total_days }}</td>
                                <td>
                                    <span class="badge bg-{{ match($req->status) { 'approved'=>'success','rejected'=>'danger','cancelled'=>'secondary',default=>'warning text-dark'} }}">
                                        {{ ucfirst($req->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($req->status === 'pending')
                                    <form method="POST" action="{{ route('employee.leave.cancel', $req) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-danger">Cancel</button>
                                    </form>
                                    @else
                                        <span class="text-muted small">{{ $req->reviewer_comment ?? '—' }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">No leave requests found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3">{{ $requests->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
