@extends('layouts.dashboard')

@section('title', 'Holidays')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0" style="color:#111827;">Holidays</h4>
        <p class="text-muted mb-0" style="font-size:13px;">Company holidays for {{ $year }}</p>
    </div>
    <form method="GET" action="{{ route('employee.holidays') }}" class="d-flex align-items-center gap-2">
        <select name="year" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
            @foreach($years as $y)
                <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
    </form>
</div>

<div class="card border-0 rounded-4" style="box-shadow:0 1px 8px rgba(0,0,0,.06);">
    <div class="card-body p-0">
        @if($holidays->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:14px;">
                <thead style="background:#f8fafc;">
                    <tr>
                        <th class="px-4 py-3 fw-semibold text-muted" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;">#</th>
                        <th class="px-4 py-3 fw-semibold text-muted" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;">Date</th>
                        <th class="px-4 py-3 fw-semibold text-muted" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;">Day</th>
                        <th class="px-4 py-3 fw-semibold text-muted" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;">Holiday</th>
                        <th class="px-4 py-3 fw-semibold text-muted" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;">Type</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($holidays as $i => $holiday)
                    @php
                        $date = \Carbon\Carbon::parse($holiday->holiday_date);
                        $isPast = $date->isPast();
                        $isToday = $date->isToday();
                    @endphp
                    <tr style="{{ $isPast && !$isToday ? 'opacity:0.5;' : '' }}{{ $isToday ? 'background:#eff6ff;' : '' }}">
                        <td class="px-4 py-3 text-muted">{{ $i + 1 }}</td>
                        <td class="px-4 py-3 fw-semibold">
                            {{ $date->format('d M Y') }}
                            @if($isToday)
                                <span class="badge bg-primary ms-1" style="font-size:10px;">Today</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-muted">{{ $date->format('l') }}</td>
                        <td class="px-4 py-3 fw-semibold">{{ $holiday->name }}</td>
                        <td class="px-4 py-3">
                            @if($holiday->is_optional)
                                <span class="badge rounded-pill" style="background:#fef9c3;color:#854d0e;font-size:11px;">Optional</span>
                            @else
                                <span class="badge rounded-pill" style="background:#dcfce7;color:#166534;font-size:11px;">Public</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-calendar-x" style="font-size:2rem;"></i>
            <p class="mt-2 mb-0">No holidays added for {{ $year }}.</p>
        </div>
        @endif
    </div>
</div>
@endsection
