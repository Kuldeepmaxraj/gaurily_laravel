@extends('layouts.dashboard')
@section('title', 'My Profile')
@section('content')
<h4 class="fw-bold mb-4">My Profile</h4>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="row g-4">
    {{-- Profile info --}}
    <div class="col-md-5">
        <div class="card border-0 shadow-sm p-4">
            <div class="d-flex align-items-center gap-3 mb-4">
                @if($employee->avatar)
                    <img src="{{ Storage::url($employee->avatar) }}" alt="Avatar"
                         class="rounded-circle object-fit-cover"
                         style="width:72px;height:72px;flex-shrink:0;object-fit:cover;">
                @else
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white fs-3"
                         style="width:72px;height:72px;background:#0066FF;flex-shrink:0;">
                        {{ strtoupper(substr($employee->name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <div class="fw-bold fs-5">{{ $employee->name }}</div>
                    <div class="text-muted small">{{ $employee->designation }}</div>
                    <span class="badge bg-success-subtle text-success">{{ ucfirst($employee->status) }}</span>
                </div>
            </div>

            {{-- Avatar upload --}}
            <form action="{{ route('employee.profile.avatar') }}" method="POST" enctype="multipart/form-data" class="mb-4">
                @csrf
                <label class="form-label small fw-semibold">Update Profile Picture</label>
                <div class="d-flex gap-2">
                    <input type="file" name="avatar" accept="image/*" class="form-control form-control-sm @error('avatar') is-invalid @enderror">
                    <button class="btn btn-sm btn-outline-primary">Upload</button>
                </div>
                @error('avatar')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                <div class="form-text">JPG, PNG or WebP · max 2 MB</div>
            </form>
            <table class="table table-sm table-borderless mb-0">
                <tr><td class="text-muted" style="width:40%;">Employee Code</td><td class="fw-medium">{{ $employee->employee_code }}</td></tr>
                <tr><td class="text-muted">Email</td><td>{{ $employee->email }}</td></tr>
                <tr><td class="text-muted">Phone</td><td>{{ $employee->phone ?? '—' }}</td></tr>
                <tr><td class="text-muted">Role</td><td>{{ $employee->role?->display_name ?? '—' }}</td></tr>
                <tr><td class="text-muted">Team</td><td>{{ $employee->team?->name ?? '—' }}</td></tr>
                <tr><td class="text-muted">Shift</td><td>
                    @if($employee->shift)
                        {{ $employee->shift->name }}
                        <span class="text-muted small">({{ \Carbon\Carbon::parse($employee->shift->start_time)->format('h:i A') }} – {{ \Carbon\Carbon::parse($employee->shift->end_time)->format('h:i A') }})</span>
                    @else
                        —
                    @endif
                </td></tr>
                <tr><td class="text-muted">Joined</td><td>{{ $employee->date_of_joining?->format('d M Y') ?? '—' }}</td></tr>
            </table>
        </div>
    </div>

    {{-- Change password --}}
    <div class="col-md-7">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-semibold mb-3">Change Password</h5>
            <form action="{{ route('employee.profile.password') }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label small fw-medium">Current Password</label>
                    <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                    @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-medium">New Password</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required minlength="8">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">Minimum 8 characters.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-medium">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <button class="btn btn-primary rounded-pill px-4">Update Password</button>
            </form>
        </div>
    </div>
</div>
@endsection
