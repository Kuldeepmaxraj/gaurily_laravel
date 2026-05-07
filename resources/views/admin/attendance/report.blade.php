@extends('layouts.dashboard')
@section('title', 'Attendance Report')
@section('content')

{{-- Page Header --}}
<div class="mb-4">
    <h4 class="fw-bold mb-1">Attendance Report</h4>
    <p class="text-muted small mb-0">View and export attendance records with work log comments.</p>
</div>

{{-- Filter + Action Bar --}}
<div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
    <div class="card-body p-3">
        <form id="filterForm" method="GET" action="{{ route('admin.attendance') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold mb-1">Employee</label>
                    <select name="employee_id" class="form-select form-select-sm">
                        <option value="">All Employees</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ $employeeId == $emp->id ? 'selected' : '' }}>
                                {{ $emp->name }} ({{ $emp->employee_code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold mb-1">Month</label>
                    <input type="month" name="month" class="form-control form-control-sm" value="{{ $month }}">
                </div>
                <div class="col-md-5">
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                        @if($employeeId || $month !== now()->format('Y-m'))
                            <a href="{{ route('admin.attendance') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                <i class="bi bi-x me-1"></i>Clear
                            </a>
                        @endif
                        <a href="{{ route('admin.attendance.export', request()->only(['employee_id','month'])) }}"
                           class="btn btn-success btn-sm rounded-pill px-4 ms-auto">
                            <i class="bi bi-download me-1"></i>Export CSV
                        </a>
                        @if(in_array(auth()->user()?->role?->name, ['admin','hr']))
                        <button type="button" class="btn btn-outline-warning btn-sm rounded-pill px-3"
                                data-bs-toggle="modal" data-bs-target="#alertModal">
                            <i class="bi bi-envelope me-1"></i>Absence Alerts
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@if(session('success'))
<div class="alert border-0 rounded-3 mb-4" style="background:#f0fdf4;color:#166534;font-size:13.5px;">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close float-end" data-bs-dismiss="alert" style="font-size:11px;"></button>
</div>
@endif
@if(session('error'))
<div class="alert border-0 rounded-3 mb-4" style="background:#fef2f2;color:#991b1b;font-size:13.5px;">
    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close float-end" data-bs-dismiss="alert" style="font-size:11px;"></button>
</div>
@endif

{{-- Results summary --}}
<div class="d-flex align-items-center justify-content-between mb-2 px-1">
    <span class="text-muted small">
        Showing <strong>{{ $logs->total() }}</strong> records for
        <strong>{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</strong>
        @if($employeeId)
            &mdash; <strong>{{ $employees->find($employeeId)?->name }}</strong>
        @endif
    </span>
</div>

{{-- Table --}}
<div class="card border-0 shadow-sm" style="border-radius:14px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Employee</th>
                        <th>Shift</th>
                        <th>Clock In</th>
                        <th>Clock Out</th>
                        <th>Break</th>
                        <th>Net Hrs</th>
                        <th>Status</th>
                        <th>Late</th>
                        <th>Work Logs</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="text-muted small align-middle">{{ $log->attendance_date->format('d M Y') }}</td>
                        <td class="align-middle">
                            <div class="fw-semibold" style="font-size:13.5px;">{{ $log->employee?->name }}</div>
                            <div class="text-muted small">{{ $log->employee?->employee_code }}</div>
                        </td>
                        <td class="align-middle small">{{ $log->shift?->name ?? '—' }}</td>
                        <td class="align-middle fw-medium small">{{ $log->login_time?->format('h:i A') ?? '—' }}</td>
                        <td class="align-middle fw-medium small">{{ $log->logout_time?->format('h:i A') ?? '—' }}</td>
                        <td class="align-middle small text-muted">{{ $log->total_break_minutes }}m</td>
                        <td class="align-middle fw-semibold small">{{ $log->net_hours ? number_format($log->net_hours,2).'h' : '—' }}</td>
                        <td class="align-middle">
                            @php
                                $statusColor = match($log->status ?? '') {
                                    'present'  => 'success',
                                    'half_day' => 'warning',
                                    'absent'   => 'danger',
                                    default    => 'secondary',
                                };
                            @endphp
                            <span class="badge rounded-pill bg-{{ $statusColor }}">
                                {{ ucfirst(str_replace('_',' ', $log->status ?? 'Pending')) }}
                            </span>
                        </td>
                        <td class="align-middle small">
                            @if($log->is_late)
                                <span class="badge bg-warning text-dark">Late</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="align-middle">
                            @if($log->workLogs->count())
                                <button class="btn btn-sm btn-outline-primary rounded-pill"
                                        style="font-size:12px;padding:2px 10px;"
                                        data-bs-toggle="modal"
                                        data-bs-target="#workLogsModal"
                                        data-name="{{ $log->employee?->name }}"
                                        data-date="{{ $log->attendance_date->format('d M Y') }}"
                                        data-logs="{{ $log->workLogs->map(fn($w) => ['time' => $w->created_at->format('h:i A'), 'comment' => $w->comment])->toJson() }}">
                                    <i class="bi bi-journal-text me-1"></i>{{ $log->workLogs->count() }}
                                </button>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-5">
                            <i class="bi bi-calendar-x display-6 d-block mb-2 opacity-25"></i>
                            No attendance records found for this period.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="px-4 py-3 border-top">{{ $logs->appends(request()->query())->links() }}</div>
        @endif
    </div>
</div>

{{-- Work Logs Modal --}}
<div class="modal fade" id="workLogsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius:14px;">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h6 class="modal-title fw-bold mb-0" id="workLogsModalTitle">Work Updates</h6>
                    <small class="text-muted" id="workLogsModalSub"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr><th style="width:110px;">Time</th><th>Update</th></tr>
                    </thead>
                    <tbody id="workLogsBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Send Alerts Modal --}}
<div class="modal fade" id="alertModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius:14px;">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">Send Absence Alerts</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.attendance.send-alerts') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small">Queues an absence alert email to every active employee who did not clock in on the selected date.</p>
                    <label class="form-label small fw-semibold">Date</label>
                    <input type="date" name="date" class="form-control form-control-sm" value="{{ today()->toDateString() }}" required>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light btn-sm rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-warning btn-sm rounded-pill px-4">Send</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.getElementById('workLogsModal').addEventListener('show.bs.modal', function(e) {
    const btn  = e.relatedTarget;
    const logs = JSON.parse(btn.dataset.logs);
    document.getElementById('workLogsModalTitle').textContent = btn.dataset.name + ' \u2014 Work Updates';
    document.getElementById('workLogsModalSub').textContent   = btn.dataset.date;
    document.getElementById('workLogsBody').innerHTML = logs.map(l =>
        `<tr><td class="text-muted small align-middle" style="white-space:nowrap;">${l.time}</td><td style="white-space:pre-wrap;word-break:break-word;">${l.comment}</td></tr>`
    ).join('');
});
</script>
@endpush