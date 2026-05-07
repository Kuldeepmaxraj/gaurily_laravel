@extends('layouts.app')

@section('title', ($blog->meta_title ?: $blog->title) . ' | Gaurily Blog')
@section('meta_description', $blog->meta_description ?: Str::limit(strip_tags($blog->excerpt ?? $blog->body), 155))
@section('meta_keywords', $blog->meta_keywords ?: 'Gaurily blog, ' . $blog->category)
@section('canonical', route('blog.show', $blog->slug))
@section('og_type', 'article')
@section('og_title', $blog->meta_title ?: $blog->title)
@section('og_description', $blog->meta_description ?: Str::limit(strip_tags($blog->excerpt ?? $blog->body), 155))
@section('og_image', $blog->og_image ? Storage::url($blog->og_image) : ($blog->cover_image ? Storage::url($blog->cover_image) : asset('og-default.jpg')))

@push('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": "{{ addslashes($blog->title) }}",
  "description": "{{ addslashes(Str::limit(strip_tags($blog->excerpt ?? $blog->body), 155)) }}",
  "author": { "@type": "Person", "name": "{{ addslashes($blog->author_name) }}" },
  "publisher": {
    "@type": "Organization",
    "name": "Gaurily",
    "url": "{{ url('/') }}"
  },
  "datePublished": "{{ ($blog->published_at ?? $blog->created_at)->toIso8601String() }}",
  "dateModified": "{{ $blog->updated_at->toIso8601String() }}",
  "url": "{{ route('blog.show', $blog->slug) }}"
  @if($blog->cover_image)
  ,"image": "{{ Storage::url($blog->cover_image) }}"
  @endif
}
</script>
@endpush

@push('styles')
<style>
.blog-body h2, .blog-body h3, .blog-body h4 { font-weight: 700; margin-top: 2rem; margin-bottom: 1rem; }
.blog-body p { line-height: 1.85; margin-bottom: 1.25rem; font-size: 16.5px; }
.blog-body ul, .blog-body ol { padding-left: 1.5rem; margin-bottom: 1.25rem; font-size: 16.5px; }
.blog-body li { margin-bottom: .4rem; line-height: 1.7; }
.blog-body blockquote { border-left: 4px solid #0066FF; padding: 1rem 1.5rem; background: #eff6ff; border-radius: 0 8px 8px 0; margin: 1.5rem 0; color: #1e40af; }
.blog-body pre { background: #1e293b; color: #e2e8f0; padding: 1.25rem; border-radius: 10px; overflow-x: auto; margin-bottom: 1.25rem; font-size: 14px; }
.blog-body code:not(pre code) { background: #eff6ff; color: #0066FF; padding: 2px 6px; border-radius: 4px; font-size: 14px; }
.blog-body img { max-width: 100%; border-radius: 10px; margin: 1rem 0; }
.blog-body a { color: #0066FF; }
</style>
@endpush

@section('content')
<div style="padding-top:80px;">

    {{-- Breadcrumb --}}
    <div class="bg-white border-bottom py-2">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('blogs') }}" class="text-decoration-none">Blog</a></li>
                    @if($blog->category)
                    <li class="breadcrumb-item">
                        <a href="{{ route('blogs', ['category' => $blog->category]) }}" class="text-decoration-none">{{ $blog->category }}</a>
                    </li>
                    @endif
                    <li class="breadcrumb-item active text-muted" aria-current="page">{{ Str::limit($blog->title, 50) }}</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Header --}}
    <section class="py-5" style="background:linear-gradient(to bottom,#eff6ff,#fff);">
        <div class="container" style="max-width:820px;">
            @if($blog->category)
            <span class="badge rounded-pill px-3 py-2 mb-3" style="background:#dbeafe;color:#0066FF;">{{ $blog->category }}</span>
            @endif
            <h1 class="display-6 fw-bold mb-4 lh-sm">{{ $blog->title }}</h1>
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                         style="width:36px;height:36px;background:#0066FF;font-size:14px;">
                        {{ strtoupper(substr($blog->author_name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="fw-semibold" style="font-size:13px;">{{ $blog->author_name }}</div>
                        <div class="text-muted" style="font-size:12px;">{{ ($blog->published_at ?? $blog->created_at)->format('d F Y') }}</div>
                    </div>
                </div>
                <span class="text-muted small">
                    <i class="bi bi-clock me-1"></i>{{ $blog->read_time }} min read
                </span>
                @if($blog->tags)
                <div class="d-flex gap-1 flex-wrap">
                    @foreach(array_map('trim', explode(',', $blog->tags)) as $tag)
                    @if($tag)
                    <span class="badge rounded-pill" style="background:#f1f5f9;color:#475569;font-size:11px;">{{ $tag }}</span>
                    @endif
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Cover Image --}}
    @if($blog->cover_image)
    <div class="container mb-5" style="max-width:820px;">
        <div class="rounded-4 overflow-hidden shadow-sm" style="max-height:460px;">
            <img src="{{ Storage::url($blog->cover_image) }}" alt="{{ $blog->title }}" class="w-100 object-fit-cover" style="max-height:460px;">
        </div>
    </div>
    @endif

    {{-- Body --}}
    <section class="pb-5">
        <div class="container" style="max-width:820px;">
            <div class="blog-body">
                {!! $blog->body !!}
            </div>

            {{-- Share --}}
            <div class="d-flex align-items-center gap-3 py-4 border-top border-bottom my-5">
                <span class="fw-semibold small">Share:</span>
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('blog.show', $blog->slug)) }}&text={{ urlencode($blog->title) }}"
                   target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary rounded-pill">
                    <i class="bi bi-twitter-x me-1"></i>X / Twitter
                </a>
                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('blog.show', $blog->slug)) }}"
                   target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary rounded-pill">
                    <i class="bi bi-linkedin me-1"></i>LinkedIn
                </a>
            </div>

            {{-- Related Posts --}}
            @if($related->count())
            <div class="mt-3">
                <h3 class="h5 fw-bold mb-4">Related Articles</h3>
                <div class="row g-4">
                    @foreach($related as $r)
                    <div class="col-md-4">
                        <a href="{{ route('blog.show', $r->slug) }}" class="text-decoration-none">
                            <div class="card border-0 shadow-sm h-100" style="border-radius:12px;overflow:hidden;">
                                @if($r->cover_image)
                                <img src="{{ Storage::url($r->cover_image) }}" alt="{{ $r->title }}" class="card-img-top" style="height:140px;object-fit:cover;">
                                @else
                                <div style="height:100px;background:linear-gradient(135deg,#eff6ff,#dbeafe);"></div>
                                @endif
                                <div class="p-3">
                                    @if($r->category)<p class="small fw-semibold mb-1" style="color:#0066FF;">{{ $r->category }}</p>@endif
                                    <p class="fw-semibold text-dark mb-0 small lh-sm">{{ $r->title }}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="text-center mt-5">
                <a href="{{ route('blogs') }}" class="btn btn-outline-primary rounded-pill px-5">
                    <i class="bi bi-arrow-left me-2"></i>Back to Blog
                </a>
            </div>
        </div>
    </section>

</div>
@endsection