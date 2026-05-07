@extends('layouts.dashboard')
@section('title', 'Blog Posts')
@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0">Blog Posts</h4>
        <p class="text-muted small mb-0">Manage all articles published on the website.</p>
    </div>
    <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary rounded-pill px-4">
        <i class="bi bi-plus-lg me-1"></i>New Post
    </a>
</div>

@if(session('success'))
<div class="alert border-0 rounded-3 mb-4" style="background:#f0fdf4;color:#166534;font-size:13.5px;">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close float-end" data-bs-dismiss="alert" style="font-size:11px;"></button>
</div>
@endif

<div class="card border-0 shadow-sm" style="border-radius:14px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:40%">Title</th>
                        <th>Category</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Published</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($blogs as $blog)
                    <tr>
                        <td class="align-middle">
                            <div class="fw-semibold" style="font-size:13.5px;">{{ $blog->title }}</div>
                            <div class="text-muted" style="font-size:12px;">/blog/{{ $blog->slug }}</div>
                        </td>
                        <td class="align-middle small text-muted">{{ $blog->category ?: '—' }}</td>
                        <td class="align-middle small">{{ $blog->author_name }}</td>
                        <td class="align-middle">
                            @if($blog->is_published)
                                <span class="badge rounded-pill bg-success">Published</span>
                            @else
                                <span class="badge rounded-pill bg-secondary">Draft</span>
                            @endif
                        </td>
                        <td class="align-middle small text-muted">
                            {{ $blog->published_at?->format('d M Y') ?? ($blog->is_published ? $blog->created_at->format('d M Y') : '—') }}
                        </td>
                        <td class="align-middle text-end">
                            @if($blog->is_published)
                            <a href="{{ route('blog.show', $blog->slug) }}" target="_blank"
                               class="btn btn-sm btn-outline-secondary rounded-pill me-1" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            @endif
                            <a href="{{ route('admin.blogs.edit', $blog) }}"
                               class="btn btn-sm btn-outline-primary rounded-pill me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.blogs.destroy', $blog) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete this post permanently?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger rounded-pill">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-journal-x display-6 d-block mb-2 opacity-25"></i>
                            No blog posts yet. <a href="{{ route('admin.blogs.create') }}">Create the first one</a>.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($blogs->hasPages())
        <div class="px-4 py-3 border-top">{{ $blogs->links() }}</div>
        @endif
    </div>
</div>

@endsection