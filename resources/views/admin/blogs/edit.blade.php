@extends('layouts.dashboard')
@section('title', 'Edit: ' . $blog->title)

@push('styles')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<style>
#editor { min-height: 320px; font-size: 15px; }
.ql-toolbar { border-radius: 8px 8px 0 0; }
.ql-container { border-radius: 0 0 8px 8px; }
.seo-counter { font-size: 11px; color: #6c757d; }
.seo-counter.warn { color: #dc3545; }
</style>
@endpush

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.blogs.index') }}" class="btn btn-sm btn-light rounded-pill">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
    <div>
        <h4 class="fw-bold mb-0">Edit Post</h4>
        <p class="text-muted small mb-0">{{ $blog->title }}</p>
    </div>
    @if($blog->is_published)
    <a href="{{ route('blog.show', $blog->slug) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill ms-auto">
        <i class="bi bi-eye me-1"></i>View Live
    </a>
    @endif
</div>

@if($errors->any())
<div class="alert border-0 rounded-3 mb-4" style="background:#fef2f2;color:#991b1b;font-size:13.5px;">
    <i class="bi bi-exclamation-circle me-2"></i>Please fix the errors below.
</div>
@endif

<form action="{{ route('admin.blogs.update', $blog) }}" method="POST" enctype="multipart/form-data">
@csrf @method('PUT')

<div class="row g-4">
    {{-- Main Content --}}
    <div class="col-lg-8">

        <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
            <div class="card-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="titleInput" class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title', $blog->title) }}" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Slug (URL)</label>
                    <div class="input-group">
                        <span class="input-group-text text-muted small">/blog/</span>
                        <input type="text" name="slug" id="slugInput" class="form-control @error('slug') is-invalid @enderror"
                               value="{{ old('slug', $blog->slug) }}">
                    </div>
                    @error('slug')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Category</label>
                        <input type="text" name="category" class="form-control"
                               value="{{ old('category', $blog->category) }}" placeholder="e.g. Web Development">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Author Name</label>
                        <input type="text" name="author_name" class="form-control"
                               value="{{ old('author_name', $blog->author_name) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
            <div class="card-body p-4">
                <label class="form-label fw-semibold small">Excerpt</label>
                <textarea name="excerpt" class="form-control" rows="3" maxlength="500">{{ old('excerpt', $blog->excerpt) }}</textarea>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
            <div class="card-body p-4">
                <label class="form-label fw-semibold small">Content <span class="text-danger">*</span></label>
                @error('body')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
                <div id="editor">{!! old('body', $blog->body) !!}</div>
                <input type="hidden" name="body" id="bodyInput">
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
            <div class="card-header bg-transparent border-0 px-4 pt-4 pb-0">
                <h6 class="fw-semibold mb-0"><i class="bi bi-search me-2 text-primary"></i>SEO Settings</h6>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-semibold small d-flex justify-content-between">
                        Meta Title <span class="seo-counter" id="metaTitleCounter">0 / 70</span>
                    </label>
                    <input type="text" name="meta_title" id="metaTitleInput" class="form-control"
                           value="{{ old('meta_title', $blog->meta_title) }}" maxlength="70">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small d-flex justify-content-between">
                        Meta Description <span class="seo-counter" id="metaDescCounter">0 / 160</span>
                    </label>
                    <textarea name="meta_description" id="metaDescInput" class="form-control" rows="2"
                              maxlength="160">{{ old('meta_description', $blog->meta_description) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Meta Keywords</label>
                    <input type="text" name="meta_keywords" class="form-control"
                           value="{{ old('meta_keywords', $blog->meta_keywords) }}">
                </div>
                <div>
                    <label class="form-label fw-semibold small">Tags</label>
                    <input type="text" name="tags" class="form-control"
                           value="{{ old('tags', $blog->tags) }}" placeholder="laravel, php, web development">
                </div>
            </div>
        </div>

    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">

        <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3">Publish</h6>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="is_published" id="publishSwitch"
                           value="1" {{ old('is_published', $blog->is_published) ? 'checked' : '' }}>
                    <label class="form-check-label small fw-medium" for="publishSwitch">Published</label>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Publish date</label>
                    <input type="datetime-local" name="published_at" class="form-control form-control-sm"
                           value="{{ old('published_at', $blog->published_at?->format('Y-m-d\TH:i')) }}">
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill">
                        <i class="bi bi-check2 me-1"></i>Update Post
                    </button>
                    <a href="{{ route('admin.blogs.index') }}" class="btn btn-light rounded-pill">Cancel</a>
                </div>
                <hr>
                <form action="{{ route('admin.blogs.destroy', $blog) }}" method="POST"
                      onsubmit="return confirm('Permanently delete this post?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm rounded-pill w-100">
                        <i class="bi bi-trash me-1"></i>Delete Post
                    </button>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3">Cover Image</h6>
                @if($blog->cover_image)
                <div class="rounded-3 overflow-hidden mb-3" style="height:160px;">
                    <img src="{{ Storage::url($blog->cover_image) }}" alt="" class="w-100 h-100 object-fit-cover">
                </div>
                @endif
                <div id="coverPreview" class="rounded-3 overflow-hidden mb-3 d-none" style="height:160px;background:#f1f5f9;">
                    <img id="coverImg" src="" alt="" class="w-100 h-100 object-fit-cover">
                </div>
                <input type="file" name="cover_image" id="coverInput" class="form-control form-control-sm"
                       accept="image/jpeg,image/png,image/webp">
                <div class="form-text">Upload new to replace current. 1200×630 recommended.</div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3">OG / Social Share Image</h6>
                @if($blog->og_image)
                <div class="rounded-3 overflow-hidden mb-3" style="height:120px;">
                    <img src="{{ Storage::url($blog->og_image) }}" alt="" class="w-100 h-100 object-fit-cover">
                </div>
                @endif
                <input type="file" name="og_image" class="form-control form-control-sm"
                       accept="image/jpeg,image/png,image/webp">
                <div class="form-text">Ideal: 1200×630 px. Leave blank to keep current.</div>
            </div>
        </div>

    </div>
</div>

</form>
@endsection

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
const quill = new Quill('#editor', {
    theme: 'snow',
    modules: {
        toolbar: [
            [{ header: [2, 3, 4, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ list: 'ordered' }, { list: 'bullet' }],
            ['blockquote', 'code-block'],
            ['link', 'image'],
            [{ align: [] }],
            ['clean']
        ]
    }
});
document.querySelector('form').addEventListener('submit', function() {
    document.getElementById('bodyInput').value = quill.root.innerHTML;
});
function counter(inputId, counterId, max) {
    const inp = document.getElementById(inputId);
    const ctr = document.getElementById(counterId);
    if (!inp) return;
    function update() {
        const len = inp.value.length;
        ctr.textContent = len + ' / ' + max;
        ctr.classList.toggle('warn', len > max * 0.9);
    }
    inp.addEventListener('input', update);
    update();
}
counter('metaTitleInput', 'metaTitleCounter', 70);
counter('metaDescInput',  'metaDescCounter',  160);
document.getElementById('coverInput').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('coverImg').src = e.target.result;
        document.getElementById('coverPreview').classList.remove('d-none');
    };
    reader.readAsDataURL(file);
});
</script>
@endpush