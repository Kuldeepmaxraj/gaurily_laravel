@extends('layouts.dashboard')
@section('title', 'Attendance Settings')
@section('content')
<h4 class="fw-bold mb-1">Attendance Settings</h4>
<p class="text-muted small mb-4">These values control how attendance status and leave accrual are calculated system-wide.</p>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="row g-4">
    <div class="col-md-7">
        <div class="card border-0 shadow-sm p-4">
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf @method('PUT')
                <div class="row g-3">
                    @foreach($keys as $key => $meta)
                    <div class="col-12">
                        <label class="form-label fw-medium">{{ $meta['label'] }}</label>
                        <input type="{{ $meta['type'] }}" step="{{ $meta['step'] }}" name="{{ $key }}"
                               class="form-control @error($key) is-invalid @enderror"
                               value="{{ old($key, $settings[$key]?->value ?? '') }}" required>
                        @error($key)<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text text-muted">{{ $meta['description'] }}</div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    <button class="btn btn-primary rounded-pill px-4">Save Settings</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card border-0 p-4" style="background:#eff6ff;">
            <h6 class="fw-semibold mb-3" style="color:#0066FF;"><i class="bi bi-info-circle me-1"></i> How these settings work</h6>
            <ul class="small text-muted ps-3 mb-0">
                <li class="mb-2"><strong>Full-day threshold:</strong> If net hours ≥ this value, status = Present.</li>
                <li class="mb-2"><strong>Half-day threshold:</strong> If net hours ≥ this value (but less than full-day), status = Half Day. Below this = Absent.</li>
                <li class="mb-2"><strong>EL accrual per month:</strong> Earned Leave days added to each employee's balance at month-end.</li>
                <li class="mb-2"><strong>EL carry-forward max:</strong> At year-end, unused EL above this value is forfeited.</li>
                <li><strong>Working days/week:</strong> Used to compute total working days when calculating leave periods.</li>
            </ul>
        </div>
    </div>
</div>
@endsection
