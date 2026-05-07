@extends('layouts.dashboard')
@section('title', 'Leave Balances')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Leave Balances</h4>
    <form class="d-flex gap-2">
        <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
            @for($y = now()->year - 1; $y <= now()->year + 1; $y++)
            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
    </form>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th>Employee</th>
                    @foreach($leaveTypes as $lt)
                    <th class="text-center">{{ $lt->code }}<br><span class="text-muted fw-normal" style="font-size:11px;">{{ $lt->name }}</span></th>
                    @endforeach
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $emp)
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $emp->name }}</div>
                        <div class="text-muted small">{{ $emp->employee_code }}</div>
                    </td>
                    @foreach($leaveTypes as $lt)
                    @php
                        $bal = $emp->leaveBalances->firstWhere('leave_type_id', $lt->id);
                    @endphp
                    <td class="text-center">
                        @if($bal)
                            <span class="fw-semibold">{{ $bal->balance }}</span>
                            <div class="text-muted" style="font-size:11px;">{{ $bal->used }} used / {{ $bal->allocated }} alloc</div>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    @endforeach
                    <td>
                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                data-bs-toggle="modal" data-bs-target="#adjustModal{{ $emp->id }}">Adjust</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Adjust modals --}}
@foreach($employees as $emp)
<div class="modal fade" id="adjustModal{{ $emp->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">Adjust Leave — {{ $emp->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @foreach($leaveTypes as $lt)
                @php $bal = $emp->leaveBalances->firstWhere('leave_type_id', $lt->id); @endphp
                @if($bal)
                <form action="{{ route('admin.leave-balances.adjust', $bal) }}" method="POST" class="border rounded p-3 mb-3">
                    @csrf @method('PUT')
                    <div class="fw-medium mb-2">{{ $lt->name }} ({{ $lt->code }})</div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small">Allocated</label>
                            <input type="number" name="allocated" class="form-control form-control-sm" value="{{ $bal->allocated }}" step="0.5" min="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Carried Forward</label>
                            <input type="number" name="carried_forward" class="form-control form-control-sm" value="{{ $bal->carried_forward }}" step="0.5" min="0">
                        </div>
                    </div>
                    <div class="mt-2 text-muted small">Used: {{ $bal->used }} | Current balance: {{ $bal->balance }}</div>
                    <button class="btn btn-sm btn-primary mt-2 rounded-pill px-3">Update</button>
                </form>
                @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
