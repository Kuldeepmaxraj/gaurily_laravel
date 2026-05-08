@extends('layouts.dashboard')

@section('title', 'Employees')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Employees</h4>
    @if($canEdit)
    <a href="{{ route('admin.employees.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Add Employee
    </a>
    @endif
</div>

{{-- Filters --}}
<form class="card border-0 shadow-sm p-3 mb-4">
    <div class="row g-2">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search name or code..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="role" class="form-select">
                <option value="">All Roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>{{ $role->display_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="active" {{ request('status')==='active'?'selected':'' }}>Active</option>
                <option value="inactive" {{ request('status')==='inactive'?'selected':'' }}>Inactive</option>
                <option value="on_leave" {{ request('status')==='on_leave'?'selected':'' }}>On Leave</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Filter</button>
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Code</th><th>Name</th><th>Role</th><th>Team</th><th>Shift</th><th>Status</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                    <tr>
                        <td><span class="badge bg-light text-dark">{{ $emp->employee_code }}</span></td>
                        <td>
                            <div class="fw-semibold">{{ $emp->name }}</div>
                            <div class="text-muted small">{{ $emp->email }}</div>
                        </td>
                        <td>{{ $emp->role?->display_name ?? '—' }}</td>
                        <td>{{ $emp->team?->name ?? '—' }}</td>
                        <td>{{ $emp->shift?->name ?? '—' }}</td>
                        <td>
                            <span class="badge bg-{{ match($emp->status) { 'active'=>'success','inactive'=>'secondary',default=>'warning text-dark'} }}">
                                {{ ucfirst($emp->status) }}
                            </span>
                        </td>
                        <td>
                            @if($canEdit)
                            <a href="{{ route('admin.employees.edit', $emp) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No employees found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3">{{ $employees->links() }}</div>
    </div>
</div>
@endsection
