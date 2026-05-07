@extends('layouts.dashboard')
@section('title', 'Teams')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="fw-bold mb-0">Teams</h5>
        <p class="text-muted small mb-0">Organise employees into teams</p>
    </div>
    <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addTeamModal">
        <i class="bi bi-plus-lg me-1"></i> Add Team
    </button>
</div>

<div class="card border-0 shadow-sm" style="border-radius:14px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Team Name</th>
                        <th>Description</th>
                        <th>Members</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teams as $team)
                    <tr>
                        <td class="text-muted small">{{ $loop->iteration }}</td>
                        <td class="fw-semibold">{{ $team->name }}</td>
                        <td class="text-muted small">{{ $team->description ?? '—' }}</td>
                        <td>
                            <span class="badge rounded-pill bg-light text-dark border">
                                <i class="bi bi-people me-1"></i>{{ $team->employees_count }}
                            </span>
                        </td>
                        <td>
                            <span class="badge rounded-pill bg-{{ $team->is_active ? 'success' : 'secondary' }} bg-opacity-10 text-{{ $team->is_active ? 'success' : 'secondary' }}">
                                {{ $team->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-secondary me-1"
                                data-bs-toggle="modal"
                                data-bs-target="#editTeamModal"
                                data-id="{{ $team->id }}"
                                data-name="{{ $team->name }}"
                                data-description="{{ $team->description }}"
                                data-active="{{ $team->is_active ? '1' : '0' }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" action="{{ route('admin.teams.destroy', $team) }}" class="d-inline"
                                  onsubmit="return confirm('Delete team \'{{ addslashes($team->name) }}\'? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-people display-6 d-block mb-2"></i>
                            No teams yet. Click <strong>Add Team</strong> to create one.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add Team Modal --}}
<div class="modal fade" id="addTeamModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius:14px;">
            <form method="POST" action="{{ route('admin.teams.store') }}">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-bold">Add Team</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Team Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" placeholder="e.g. Backend Dev" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" rows="2" class="form-control" placeholder="Optional description">{{ old('description') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Create Team</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Team Modal --}}
<div class="modal fade" id="editTeamModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius:14px;">
            <form method="POST" id="editTeamForm" action="">
                @csrf @method('PUT')
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-bold">Edit Team</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Team Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editTeamName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" id="editTeamDescription" rows="2" class="form-control"></textarea>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="editTeamActive" value="1">
                        <label class="form-check-label fw-semibold" for="editTeamActive">Active</label>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('editTeamModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    const id   = btn.dataset.id;
    const name = btn.dataset.name;
    const desc = btn.dataset.description;
    const active = btn.dataset.active === '1';

    document.getElementById('editTeamForm').action = '/admin/teams/' + id;
    document.getElementById('editTeamName').value = name;
    document.getElementById('editTeamDescription').value = desc || '';
    document.getElementById('editTeamActive').checked = active;
});

@if($errors->any())
    var addModal = new bootstrap.Modal(document.getElementById('addTeamModal'));
    addModal.show();
@endif
</script>
@endpush
