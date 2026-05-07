@extends('layouts.app')
@section('title', 'Services | Web, Mobile & AI Development | Gaurily Dehradun')
@section('meta_description', 'Explore Gaurily\'s software services: custom web application development, mobile apps, AI & analytics, UI/UX design, cloud solutions and more. Based in Dehradun, India.')
@section('meta_keywords', 'web development services India, mobile app development Dehradun, AI analytics, custom software development, UI UX design, cloud solutions')
@section('og_title', 'Our Services | Web, Mobile & AI Development | Gaurily')
@section('og_description', 'Custom web apps, mobile development, AI analytics, UI/UX, and cloud solutions from Gaurily — Dehradun\'s trusted technology partner.')
@section('content')
<div style="padding-top:80px;">
    <section class="py-5" style="background:linear-gradient(to bottom,#eff6ff,#fff);">
        <div class="container py-4 text-center">
            <span class="badge rounded-pill px-4 py-2 mb-3" style="background:#dbeafe;color:#0066FF;">Our Services</span>
            <h1 class="display-5 fw-bold mb-3">Comprehensive <span style="color:#0066FF;">Technology</span> Solutions</h1>
            <p class="text-muted lead mx-auto" style="max-width:680px;">From product strategy to delivery and support, Gaurily provides end-to-end digital solutions that help you innovate, scale, and outperform competitors.</p>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="row g-4">
                @foreach([
                    ['Web & Application Development','Secure, scalable web applications aligned with your business goals.',['Custom Web Applications','Enterprise Platforms','API Integrations']],
                    ['Mobile App Development','Build high-performance iOS and Android apps your users love.',['Native & Cross-Platform','App Store Launch','Maintenance & Updates']],
                    ['AI & ML Solutions','Automate workflows and unlock insights with AI-powered solutions.',['AI Assistants & Chatbots','Custom ML Models','Predictive Analytics']],
                    ['Power BI & Analytics','Transform data into actionable insights with advanced dashboards.',['Custom Dashboards','Data Visualization','Business Intelligence']],
                    ['Staff Augmentation','Scale your team with vetted engineers and domain experts.',['Dedicated Developers','Flexible Engagements','Fast Onboarding']],
                    ['UI/UX & Product Design','Design intuitive, high-converting user experiences.',['UX Research','Wireframes & Prototypes','Design Systems']],
                ] as $s)
                <div class="col-md-4">
                    <div class="border border-light rounded-3 p-4 h-100">
                        <h5 class="fw-semibold mb-2">{{ $s[0] }}</h5>
                        <p class="text-muted small mb-3">{{ $s[1] }}</p>
                        <ul class="list-unstyled small text-muted">
                            @foreach($s[2] as $item)
                            <li class="mb-1">{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-5" style="background:#f9fafb;">
        <div class="container py-4 text-center">
            <h2 class="fw-bold mb-2">How We Deliver</h2>
            <p class="text-muted mb-5">A simple, transparent process that keeps you in control.</p>
            <div class="row g-4">
                @foreach([['Discover','We align on goals, scope, and success criteria.'],['Build','Agile delivery with weekly demos and progress updates.'],['Scale','Optimization, support, and long-term partnership.']] as $p)
                <div class="col-md-4">
                    <div class="bg-white border border-light rounded-3 p-4">
                        <h5 class="fw-semibold mb-2">{{ $p[0] }}</h5>
                        <p class="text-muted small mb-0">{{ $p[1] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container py-2">
            <div class="rounded-4 text-white text-center p-5" style="background:#0066FF;">
                <h2 class="fw-bold mb-2">Ready to start your project?</h2>
                <p class="mb-4" style="opacity:.85;">Let's discuss your goals and build something great together.</p>
                <a href="{{ route('contact') }}" class="btn btn-light fw-semibold rounded-pill px-5">Talk to Us</a>
            </div>
        </div>
    </section>
</div>
@endsection
