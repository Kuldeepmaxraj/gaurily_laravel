@extends('layouts.dashboard')
@section('title', 'Edit Employee')
@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('admin.employees') }}" class="btn btn-sm btn-outline-secondary me-3"><i class="bi bi-arrow-left"></i></a>
    <h4 class="fw-bold mb-0">Edit Employee — {{ $employee->name }}</h4>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.employees.update', $employee) }}">
            @csrf @method('PUT')
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Employee Code</label>
                    <input type="text" class="form-control" value="{{ $employee->employee_code }}" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Full Name *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $employee->name) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" class="form-control" value="{{ $employee->email }}" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $employee->phone) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Designation</label>
                    <input type="text" name="designation" class="form-control" value="{{ old('designation', $employee->designation) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Status *</label>
                    <select name="status" class="form-select" required>
                        @foreach(['active','inactive','on_leave'] as $s)
                            <option value="{{ $s }}" {{ old('status', $employee->status) === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Role *</label>
                    <select name="role_id" class="form-select" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $employee->role_id) == $role->id ? 'selected' : '' }}>{{ $role->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Team</label>
                    <select name="team_id" class="form-select">
                        <option value="">None</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}" {{ old('team_id', $employee->team_id) == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Shift</label>
                    <select name="shift_id" class="form-select">
                        <option value="">None</option>
                        @foreach($shifts as $shift)
                            <option value="{{ $shift->id }}" {{ old('shift_id', $employee->shift_id) == $shift->id ? 'selected' : '' }}>{{ $shift->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Date of Joining</label>
                    <input type="date" name="date_of_joining" class="form-control" value="{{ old('date_of_joining', $employee->date_of_joining?->format('Y-m-d')) }}">
                </div>
            </div>

            <hr class="my-4">
            <h6 class="fw-semibold mb-3 text-muted">Reset Password <span class="fw-normal">(leave blank to keep current)</span></h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">New Password</label>
                    <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" placeholder="Min 8 characters" autocomplete="new-password">
                    @error('new_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Confirm New Password</label>
                    <input type="password" name="new_password_confirmation" class="form-control" placeholder="Repeat new password" autocomplete="new-password">
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary px-4">Update Employee</button>
                <a href="{{ route('admin.employees') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>

{{-- Profile Picture --}}
<div class="card border-0 shadow-sm mt-4">
    <div class="card-body p-4">
        <h6 class="fw-semibold mb-3">Profile Picture</h6>
        <div class="d-flex align-items-center gap-4 mb-3">
            @if($employee->avatar)
                <img src="{{ Storage::url($employee->avatar) }}" alt="Avatar"
                     class="rounded-circle"
                     style="width:80px;height:80px;object-fit:cover;flex-shrink:0;">
            @else
                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white fs-2"
                     style="width:80px;height:80px;background:#0066FF;flex-shrink:0;">
                    {{ strtoupper(substr($employee->name, 0, 1)) }}
                </div>
            @endif
            <form method="POST" action="{{ route('admin.employees.avatar', $employee) }}" enctype="multipart/form-data">
                @csrf
                <label class="form-label small fw-semibold">Upload New Photo</label>
                <div class="d-flex gap-2">
                    <input type="file" name="avatar" accept="image/*" class="form-control form-control-sm @error('avatar') is-invalid @enderror">
                    <button class="btn btn-sm btn-outline-primary">Upload</button>
                </div>
                @error('avatar')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                <div class="form-text">JPG, PNG or WebP · max 2 MB</div>
            </form>
        </div>
    </div>
</div>
@endsection
