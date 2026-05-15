@extends('layouts.dashboard')

@section('title', 'Edit Attendance')

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.attendance', ['month' => $log->attendance_date->format('Y-m'), 'employee_id' => $log->employee_id]) }}"
       class="btn btn-sm btn-outline-secondary rounded-pill">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
    <div>
        <h4 class="fw-bold mb-0">Edit Attendance Record</h4>
        <p class="text-muted mb-0" style="font-size:13px;">
            {{ $log->employee?->name }} &bull; {{ $log->attendance_date->format('d M Y') }} &bull; {{ $log->shift?->name ?? 'No shift' }}
        </p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <form method="POST" action="{{ route('admin.attendance.update', $log) }}">
            @csrf
            @method('PUT')

            {{-- Clock In / Out --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-clock me-2 text-primary"></i>Clock In / Out</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Clock In Time <span class="text-danger">*</span></label>
                            <input type="time" name="login_time" class="form-control @error('login_time') is-invalid @enderror"
                                   value="{{ old('login_time', $log->login_time?->format('H:i')) }}" required>
                            @error('login_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Clock Out <span class="text-muted small">(date &amp; time)</span></label>
                            <input type="datetime-local" name="logout_time" class="form-control @error('logout_time') is-invalid @enderror"
                                   value="{{ old('logout_time', $log->logout_time?->format('Y-m-d\TH:i')) }}">
                            <div class="form-text">Leave empty if employee hasn't clocked out yet. For overnight shifts, pick the next day's date.</div>
                            @error('logout_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Breaks --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-bold mb-0"><i class="bi bi-pause-circle me-2 text-warning"></i>Breaks</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" id="addBreakBtn">
                            <i class="bi bi-plus-lg me-1"></i>Add Break
                        </button>
                    </div>

                    <div id="breaksContainer">
                        @forelse($log->breaks as $i => $break)
                        <div class="break-row border rounded-3 p-3 mb-3" style="background:#f8fafc;">
                            <input type="hidden" name="breaks[{{ $i }}][id]" value="{{ $break->id }}">
                            <div class="row g-2 align-items-end">
                                <div class="col-5">
                                    <label class="form-label small fw-medium">Break Start</label>
                                    <input type="datetime-local" name="breaks[{{ $i }}][break_start]"
                                           class="form-control form-control-sm"
                                           value="{{ $break->break_start?->format('Y-m-d\TH:i') }}" required>
                                </div>
                                <div class="col-5">
                                    <label class="form-label small fw-medium">Break End</label>
                                    <input type="datetime-local" name="breaks[{{ $i }}][break_end]"
                                           class="form-control form-control-sm"
                                           value="{{ $break->break_end?->format('Y-m-d\TH:i') }}">
                                    <div class="form-text" style="font-size:10px;">Empty = break still ongoing</div>
                                </div>
                                <div class="col-2">
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill w-100 remove-break-btn">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted small mb-0" id="noBreaksMsg">No breaks recorded. Click "Add Break" to add one.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-pencil me-2 text-secondary"></i>Admin Notes</h6>
                    <textarea name="notes" class="form-control" rows="2"
                              placeholder="Reason for manual adjustment (e.g. forgot to clock out)...">{{ old('notes', $log->notes) }}</textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-primary px-4 rounded-pill">
                <i class="bi bi-check-lg me-1"></i>Save & Recalculate
            </button>
        </form>
    </div>

    {{-- Info sidebar --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm mb-3" style="border-radius:14px;">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Current Record</h6>
                <table class="table table-sm mb-0" style="font-size:13px;">
                    <tr><td class="text-muted">Employee</td><td class="fw-semibold">{{ $log->employee?->name }}</td></tr>
                    <tr><td class="text-muted">Date</td><td class="fw-semibold">{{ $log->attendance_date->format('d M Y, l') }}</td></tr>
                    <tr><td class="text-muted">Shift</td><td>{{ $log->shift?->name ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Clock In</td><td>{{ $log->login_time?->format('h:i A') ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Clock Out</td><td>{{ $log->logout_time?->format('h:i A') ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Break</td><td>{{ $log->total_break_minutes }}m</td></tr>
                    <tr><td class="text-muted">Net Hours</td>
                        <td class="fw-semibold">
                            {{ $log->net_hours ? floor($log->net_hours).'h '.round(($log->net_hours - floor($log->net_hours)) * 60).'m' : '—' }}
                        </td>
                    </tr>
                    <tr><td class="text-muted">Status</td>
                        <td>
                            <span class="badge bg-{{ match($log->status ?? '') { 'present'=>'success','half_day'=>'warning','absent'=>'danger',default=>'secondary'} }}">
                                {{ ucfirst(str_replace('_',' ', $log->status ?? 'Pending')) }}
                            </span>
                        </td>
                    </tr>
                    <tr><td class="text-muted">Late</td>
                        <td>
                            @if($log->is_late)
                                <span class="badge bg-warning text-dark">Late</span>
                            @else
                                <span class="text-muted">No</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card border-0 p-4" style="background:#eff6ff;border-radius:14px;">
            <h6 class="fw-semibold mb-2" style="color:#0066FF;"><i class="bi bi-info-circle me-1"></i>How it works</h6>
            <ul class="small text-muted ps-3 mb-0">
                <li class="mb-1">After saving, net hours and status are <strong>automatically recalculated</strong>.</li>
                <li class="mb-1">Break time beyond the allowed limit is deducted from net hours.</li>
                <li class="mb-1">Leave clock out empty if the employee is still working.</li>
                <li>Add a note so you have a record of why this was manually adjusted.</li>
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let breakIndex = {{ $log->breaks->count() }};
    const attendanceDate = "{{ $log->attendance_date->format('Y-m-d') }}";
    const container = document.getElementById('breaksContainer');
    const noMsg     = document.getElementById('noBreaksMsg');

    document.getElementById('addBreakBtn').addEventListener('click', () => {
        if (noMsg) noMsg.remove();
        const row = document.createElement('div');
        row.className = 'break-row border rounded-3 p-3 mb-3';
        row.style.background = '#f8fafc';
        row.innerHTML = `
            <div class="row g-2 align-items-end">
                <div class="col-5">
                    <label class="form-label small fw-medium">Break Start</label>
                    <input type="datetime-local" name="breaks[${breakIndex}][break_start]" class="form-control form-control-sm" value="${attendanceDate}T" required>
                </div>
                <div class="col-5">
                    <label class="form-label small fw-medium">Break End</label>
                    <input type="datetime-local" name="breaks[${breakIndex}][break_end]" class="form-control form-control-sm">
                    <div class="form-text" style="font-size:10px;">Empty = break still ongoing</div>
                </div>
                <div class="col-2">
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill w-100 remove-break-btn">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>`;
        container.appendChild(row);
        breakIndex++;
        bindRemove(row.querySelector('.remove-break-btn'));
    });

    function bindRemove(btn) {
        btn.addEventListener('click', () => btn.closest('.break-row').remove());
    }
    document.querySelectorAll('.remove-break-btn').forEach(bindRemove);
</script>
@endpush
