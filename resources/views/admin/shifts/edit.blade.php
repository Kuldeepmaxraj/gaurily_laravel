@extends('layouts.dashboard')
@section('title', 'Edit Shift')
@section('content')
<div class="d-flex align-items-center mb-4 gap-3">
    <a href="{{ route('admin.shifts') }}" class="btn btn-sm btn-outline-secondary rounded-pill">&larr; Back</a>
    <h4 class="fw-bold mb-0">Edit Shift — {{ $shift->name }}</h4>
</div>

<div class="card border-0 shadow-sm p-4" style="max-width:600px;">
    <form action="{{ route('admin.shifts.update', $shift) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.shifts._form')
        <div class="mt-4">
            <button class="btn btn-primary rounded-pill px-4">Save Changes</button>
        </div>
    </form>
</div>

@if($shift->employees()->count() > 0)
<div class="card border-0 shadow-sm mt-4 p-4" style="max-width:600px;">
    <h6 class="fw-semibold mb-3">Employees on this shift ({{ $shift->employees()->count() }})</h6>
    <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
            <thead class="bg-light"><tr><th>Name</th><th>Code</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($shift->employees as $emp)
                <tr>
                    <td>{{ $emp->name }}</td>
                    <td class="text-muted small">{{ $emp->employee_code }}</td>
                    <td><span class="badge bg-{{ $emp->status === 'active' ? 'success' : 'secondary' }}-subtle text-{{ $emp->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($emp->status) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
