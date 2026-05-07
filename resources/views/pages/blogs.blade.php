@extends('layouts.app')

@section('title', 'Blog | Gaurily - Software Development Insights from Dehradun')
@section('meta_description', 'Engineering insights, best practices, and technology trends from the Gaurily team. Web, mobile, AI, and cloud development articles from Dehradun.')
@section('meta_keywords', 'software development blog, web development articles, AI insights, Gaurily blog, tech blog India')
@section('og_title', 'Blog | Gaurily')
@section('og_description', 'Engineering insights and technology trends from the Gaurily team in Dehradun.')

@push('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Blog",
  "name": "Gaurily Blog",
  "url": "{{ url('/blog') }}",
  "description": "Engineering insights and technology trends from Gaurily, software development company in Dehradun.",
  "publisher": {
    "@type": "Organization",
    "name": "Gaurily",
    "url": "{{ url('/') }}"
  }
}
</script>
@endpush

@section('content')
<div style="padding-top:80px;">

    {{-- Hero --}}
    <section class="py-5" style="background:linear-gradient(to bottom,#eff6ff,#fff);">
        <div class="container py-4 text-center">
            <span class="badge rounded-pill px-4 py-2 mb-3" style="background:#dbeafe;color:#0066FF;">Insights</span>
            <h1 class="display-5 fw-bold mb-3">Latest from our Blog</h1>
            <p class="text-muted lead mx-auto" style="max-width:600px;">Ideas, best practices, and trends from our engineering teams in Dehradun.</p>
        </div>
    </section>

    {{-- Category Filter --}}
    @if($categories->count())
    <section class="py-3 bg-white border-bottom sticky-top" style="top:76px;z-index:10;">
        <div class="container">
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('blogs') }}" class="btn btn-sm rounded-pill {{ !$category ? 'btn-primary' : 'btn-outline-secondary' }}">
                    All
                </a>
                @foreach($categories as $cat)
                <a href="{{ route('blogs', ['category' => $cat]) }}"
                   class="btn btn-sm rounded-pill {{ $category === $cat ? 'btn-primary' : 'btn-outline-secondary' }}">
                    {{ $cat }}
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Posts Grid --}}
    <section class="py-5 bg-white">
        <div class="container py-3">
            @if($blogs->count())
            <div class="row g-4">
                @foreach($blogs as $post)
                <div class="col-md-6 col-lg-4">
                    <article class="card border-0 shadow-sm h-100" style="border-radius:14px;overflow:hidden;">
                        @if($post->cover_image)
                        <a href="{{ route('blog.show', $post->slug) }}">
                            <img src="{{ Storage::url($post->cover_image) }}" alt="{{ $post->title }}"
                                 class="card-img-top" style="height:200px;object-fit:cover;">
                        </a>
                        @else
                        <div class="d-flex align-items-center justify-content-center" style="height:160px;background:linear-gradient(135deg,#eff6ff,#dbeafe);">
                            <i class="bi bi-journal-richtext" style="font-size:2.5rem;color:#93c5fd;"></i>
                        </div>
                        @endif
                        <div class="card-body p-4 d-flex flex-column">
                            @if($post->category)
                            <span class="small fw-semibold mb-2" style="color:#0066FF;">{{ $post->category }}</span>
                            @endif
                            <h2 class="h6 fw-bold mb-2 lh-sm">
                                <a href="{{ route('blog.show', $post->slug) }}" class="text-dark text-decoration-none stretched-link">
                                    {{ $post->title }}
                                </a>
                            </h2>
                            @if($post->excerpt)
                            <p class="text-muted small mb-3 flex-grow-1">{{ Str::limit($post->excerpt, 120) }}</p>
                            @endif
                            <div class="d-flex align-items-center justify-content-between mt-auto pt-3 border-top">
                                <span class="text-muted" style="font-size:12px;">
                                    <i class="bi bi-person me-1"></i>{{ $post->author_name }}
                                </span>
                                <span class="text-muted" style="font-size:12px;">
                                    <i class="bi bi-clock me-1"></i>{{ $post->read_time }} min read
                                    &nbsp;&bull;&nbsp;
                                    {{ ($post->published_at ?? $post->created_at)->format('d M Y') }}
                                </span>
                            </div>
                        </div>
                    </article>
                </div>
                @endforeach
            </div>

            @if($blogs->hasPages())
            <div class="mt-5 d-flex justify-content-center">
                {{ $blogs->appends(request()->query())->links() }}
            </div>
            @endif

            @else
            <div class="text-center py-5">
                <i class="bi bi-journal-x display-4 text-muted opacity-25 d-block mb-3"></i>
                <h5 class="fw-semibold text-muted">No posts yet</h5>
                <p class="text-muted small">Check back soon for the latest from our team.</p>
            </div>
            @endif
        </div>
    </section>

</div>
@endsection