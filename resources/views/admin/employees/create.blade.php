@extends('layouts.dashboard')
@section('title', 'Add Employee')
@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('admin.employees') }}" class="btn btn-sm btn-outline-secondary me-3"><i class="bi bi-arrow-left"></i></a>
    <h4 class="fw-bold mb-0">Add New Employee</h4>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.employees.store') }}">
            @csrf
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Employee Code *</label>
                    <input type="text" name="employee_code" class="form-control" value="{{ old('employee_code') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Full Name *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Email *</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Password *</label>
                    <input type="password" name="password" class="form-control" required minlength="8">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Designation</label>
                    <input type="text" name="designation" class="form-control" value="{{ old('designation') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Role *</label>
                    <select name="role_id" class="form-select" required>
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Team</label>
                    <select name="team_id" class="form-select">
                        <option value="">None</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}" {{ old('team_id') == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Shift</label>
                    <select name="shift_id" class="form-select">
                        <option value="">None</option>
                        @foreach($shifts as $shift)
                            <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>{{ $shift->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Date of Joining</label>
                    <input type="date" name="date_of_joining" class="form-control" value="{{ old('date_of_joining') }}">
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary px-4">Create Employee</button>
                <a href="{{ route('admin.employees') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
