@extends('layouts.app')

@section('title', 'Gaurily | Software Development Company in Dehradun')
@section('meta_description', 'Gaurily builds high-performance web, mobile and AI solutions that help businesses scale faster. Software development company based in Dehradun, Uttarakhand, India.')
@section('meta_keywords', 'software development company Dehradun, web application development India, mobile app development, AI solutions, custom software, Gaurily')
@section('og_title', 'Gaurily | Software Development Company in Dehradun')
@section('og_description', 'We build high-performance web, mobile and AI solutions for businesses across India. Based in Dehradun, Uttarakhand.')

@push('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "Gaurily",
  "url": "{{ url('/') }}",
  "logo": "{{ asset('logo.svg') }}",
  "description": "Software development company in Dehradun building web, mobile and AI solutions.",
  "address": {
    "@type": "PostalAddress",
    "addressLocality": "Dehradun",
    "addressRegion": "Uttarakhand",
    "addressCountry": "IN"
  },
  "contactPoint": {
    "@type": "ContactPoint",
    "telephone": "+91-8699902209",
    "contactType": "customer service",
    "email": "care@gaurily.com"
  },
  "sameAs": []
}
</script>
@endpush

@section('content')
{{-- Hero --}}
<section class="position-relative overflow-hidden" style="padding-top:160px;padding-bottom:80px;min-height:100vh;">
    <div class="position-absolute inset-0 w-100 h-100" style="background:url('https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1800&q=80') center/cover;opacity:.4;top:0;left:0;right:0;bottom:0;"></div>
    <div class="position-absolute top-0 start-0 end-0 bottom-0" style="background:linear-gradient(to bottom,rgba(255,255,255,.75),rgba(255,255,255,.65),#fff);"></div>
    <div class="container position-relative text-center" style="max-width:860px;">
        <span class="badge rounded-pill px-4 py-2 mb-4" style="background:#eff6ff;color:#0066FF;font-size:13px;font-weight:500;">
            <span class="me-1" style="display:inline-block;width:8px;height:8px;background:#0066FF;border-radius:50%;"></span>
            Dehradun's trusted technology partner
        </span>
        <h1 class="display-3 fw-bold mb-4 lh-sm">
            Your Partner in <span style="color:#0066FF;">Digital<br>Innovation</span>
        </h1>
        <p class="lead text-secondary mb-5 mx-auto" style="max-width:680px;line-height:1.7;">
            We build high-performance web, mobile, and AI solutions that help businesses scale faster with a strong engineering team and proven delivery process.
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-3 mb-5">
            <a href="{{ route('services') }}" class="btn btn-primary btn-lg rounded-pill px-5 shadow">Explore Services</a>
            <a href="{{ route('contact') }}" class="btn btn-outline-secondary btn-lg rounded-pill px-5">Get a Free Consultation &rarr;</a>
        </div>

        <div class="row g-3 justify-content-center">
            @foreach([['50+','Engineers &amp; Specialists'],['100+','Projects Delivered'],['10+','Industries Served'],['24/7','Support &amp; Monitoring']] as $s)
            <div class="col-6 col-md-3">
                <div class="bg-white border border-light rounded-3 p-3 shadow-sm">
                    <p class="fw-bold fs-3 mb-0" style="color:#0066FF;">{{ $s[0] }}</p>
                    <p class="text-muted small mb-0">{!! $s[1] !!}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Trust & Value --}}
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="row g-4">
            @foreach([
                ['Large Engineering Team','Dedicated squads for frontend, backend, mobile, data, and DevOps.'],
                ['Rapid Delivery','Agile sprints, weekly demos, and predictable timelines.'],
                ['Quality Focused','QA-driven releases and post-launch performance optimization.'],
            ] as $f)
            <div class="col-md-4">
                <div class="border border-light rounded-3 p-4 h-100 hover-shadow">
                    <h5 class="fw-semibold mb-2">{{ $f[0] }}</h5>
                    <p class="text-muted small mb-0">{{ $f[1] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Blog Preview --}}
<section class="py-5" style="background:#f9fafb;">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
            <div>
                <div class="small fw-medium mb-1" style="color:#0066FF;">INSIGHTS</div>
                <h2 class="fw-bold mb-1">Latest from our blog</h2>
                <p class="text-muted small mb-0">Ideas, best practices, and trends from our engineering teams in Dehradun.</p>
            </div>
            <a href="{{ route('blogs') }}" style="color:#0066FF;">View all posts &rarr;</a>
        </div>
        <div class="row g-4">
            @foreach([
                ['Web Development','How we build scalable web apps for growth','A practical guide to architecture, performance, and security for modern web products.'],
                ['AI &amp; Analytics','Turning business data into decisions','How Power BI dashboards and ML models help teams move faster with clarity.'],
                ['Mobile Development','Why cross-platform is the default now','A comparison of React Native and Flutter for building production-grade mobile apps.'],
            ] as $p)
            <div class="col-md-4">
                <div class="bg-white rounded-3 border border-light p-4 h-100">
                    <p class="small fw-medium mb-2" style="color:#0066FF;">{!! $p[0] !!}</p>
                    <h6 class="fw-semibold mb-2">{{ $p[1] }}</h6>
                    <p class="text-muted small mb-3">{!! $p[2] !!}</p>
                    <a href="{{ route('blogs') }}" style="color:#0066FF;" class="small fw-medium">Read more &rarr;</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-5">
    <div class="container py-4">
        <div class="rounded-4 text-white text-center p-5" style="background:#0066FF;">
            <h2 class="fw-bold mb-2">Ready to build something great?</h2>
            <p class="mb-4" style="opacity:.85;">Let's discuss your goals and assign the right engineering squad.</p>
            <a href="{{ route('contact') }}" class="btn btn-light fw-semibold rounded-pill px-5">Get in Touch</a>
        </div>
    </div>
</section>
@endsection
