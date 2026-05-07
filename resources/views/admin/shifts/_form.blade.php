{{-- Shared form fields for create & edit --}}
@if($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0 small">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="row g-3">
    <div class="col-12">
        <label class="form-label fw-medium">Shift Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $shift->name ?? '') }}" placeholder="e.g. Morning Shift" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-medium">Start Time <span class="text-danger">*</span></label>
        <input type="time" name="start_time" class="form-control @error('start_time') is-invalid @enderror"
               value="{{ old('start_time', isset($shift) ? substr($shift->start_time, 0, 5) : '') }}" required>
        @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">Employee check-in is measured against this time.</div>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-medium">End Time <span class="text-danger">*</span></label>
        <input type="time" name="end_time" class="form-control @error('end_time') is-invalid @enderror"
               value="{{ old('end_time', isset($shift) ? substr($shift->end_time, 0, 5) : '') }}" required>
        @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-medium">Grace Period (minutes) <span class="text-danger">*</span></label>
        <input type="number" name="grace_minutes" class="form-control @error('grace_minutes') is-invalid @enderror"
               value="{{ old('grace_minutes', $shift->grace_minutes ?? 10) }}" min="0" max="120" required>
        @error('grace_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">Check-ins within this window won't be marked late.</div>
    </div>

    <div class="col-md-6 d-flex flex-column justify-content-center gap-3 pt-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="overnight" id="overnight" value="1"
                   {{ old('overnight', $shift->overnight ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="overnight">
                <strong>Overnight shift</strong>
                <div class="text-muted" style="font-size:12px;">Shift crosses midnight (e.g. 7 PM – 3 AM)</div>
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                   {{ old('is_active', $shift->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active"><strong>Active</strong></label>
        </div>
    </div>
</div>
