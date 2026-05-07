@extends('layouts.dashboard')
@section('title', 'Holidays')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Public Holidays</h4>
    {{-- Year selector --}}
    <form class="d-flex gap-2">
        <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
            @for($y = now()->year - 1; $y <= now()->year + 1; $y++)
            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
    </form>
</div>

@foreach(['success','error'] as $msg)
@if(session($msg))
<div class="alert alert-{{ $msg === 'success' ? 'success' : 'danger' }} alert-dismissible fade show">
    {{ session($msg) }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@endforeach

<div class="row g-4">
    {{-- Add holiday form --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4">
            <h6 class="fw-semibold mb-3">Add Holiday</h6>
            <form action="{{ route('admin.holidays.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-medium">Date</label>
                    <input type="date" name="holiday_date" class="form-control @error('holiday_date') is-invalid @enderror" required>
                    @error('holiday_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-medium">Holiday Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Diwali" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3 form-check">
                    <input class="form-check-input" type="checkbox" name="is_optional" id="is_optional" value="1">
                    <label class="form-check-label small" for="is_optional">Optional holiday</label>
                </div>
                <button class="btn btn-primary w-100 rounded-pill">Add Holiday</button>
            </form>
        </div>
    </div>

    {{-- Holiday list --}}
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr><th>Date</th><th>Day</th><th>Holiday</th><th>Type</th><th></th></tr>
                    </thead>
                    <tbody>
                        @forelse($holidays as $h)
                        <tr>
                            <td>{{ $h->holiday_date->format('d M Y') }}</td>
                            <td class="text-muted small">{{ $h->holiday_date->format('l') }}</td>
                            <td class="fw-medium">{{ $h->name }}</td>
                            <td>
                                @if($h->is_optional)
                                    <span class="badge bg-warning text-dark">Optional</span>
                                @else
                                    <span class="badge bg-success-subtle text-success">Public</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('admin.holidays.destroy', $h) }}" method="POST"
                                      onsubmit="return confirm('Remove this holiday?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-pill px-3">Remove</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No holidays for {{ $year }}.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
