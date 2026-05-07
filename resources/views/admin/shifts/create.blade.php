@extends('layouts.dashboard')
@section('title', 'Add Shift')
@section('content')
<div class="d-flex align-items-center mb-4 gap-3">
    <a href="{{ route('admin.shifts') }}" class="btn btn-sm btn-outline-secondary rounded-pill">&larr; Back</a>
    <h4 class="fw-bold mb-0">Add New Shift</h4>
</div>

<div class="card border-0 shadow-sm p-4" style="max-width:600px;">
    <form action="{{ route('admin.shifts.store') }}" method="POST">
        @csrf
        @include('admin.shifts._form')
        <div class="mt-4">
            <button class="btn btn-primary rounded-pill px-4">Create Shift</button>
        </div>
    </form>
</div>
@endsection
