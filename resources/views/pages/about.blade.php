@extends('layouts.app')
@section('title', 'About Gaurily | Our Story & Team | Software Company Dehradun')
@section('meta_description', 'Learn about Gaurily — a software development company in Dehradun building impactful digital products. Meet our team and discover our mission to transform businesses through technology.')
@section('meta_keywords', 'about Gaurily, software company Dehradun, our team, technology company Uttarakhand, about us')
@section('og_title', 'About Gaurily | Our Story & Team')
@section('og_description', 'Learn about Gaurily — our story, team, and mission to build impactful digital products for businesses across India.')
@section('content')
<div style="padding-top:80px;">
    <section class="py-5" style="background:linear-gradient(to bottom,#eff6ff,#fff);">
        <div class="container py-4 text-center">
            <span class="badge rounded-pill px-4 py-2 mb-3" style="background:#dbeafe;color:#0066FF;">About Gaurily</span>
            <h1 class="display-5 fw-bold mb-3">Building with a <span style="color:#0066FF;">Large</span> Engineering Team</h1>
            <p class="text-muted lead mx-auto" style="max-width:680px;">Gaurily is a technology partner focused on building reliable, scalable software for businesses across industries. Our growing team of software engineers, designers, and analysts work together to deliver measurable outcomes.</p>
        </div>
    </section>

    {{-- Highlights --}}
    <section class="py-5 bg-white">
        <div class="container py-2">
            <div class="row g-4 mb-5">
                @foreach([
                    ['Engineering at Scale','A large, multi-disciplinary engineering team delivering web, mobile, data, and AI solutions.'],
                    ['Delivery Excellence','Agile squads, dedicated QA, and DevOps support ensure high-quality releases.'],
                    ['Client-Centric','We partner closely with clients to align on KPIs, timelines, and long-term growth.'],
                ] as $h)
                <div class="col-md-4">
                    <div class="bg-white border border-light rounded-3 p-4 h-100">
                        <h5 class="fw-semibold mb-2">{{ $h[0] }}</h5>
                        <p class="text-muted small mb-0">{{ $h[1] }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="row g-5 align-items-center">
                <div class="col-md-6">
                    <h2 class="fw-bold mb-4">A Strong Team of Software Engineers</h2>
                    <p class="text-muted mb-3">Our engineering organization includes frontend, backend, mobile, data, and cloud specialists. This depth lets us assemble cross-functional teams quickly and deliver complex projects on time.</p>
                    <ul class="text-muted small">
                        <li class="mb-2">Product squads with engineers, QA, and designers</li>
                        <li class="mb-2">Experienced leads guiding architecture and best practices</li>
                        <li class="mb-2">Dedicated support for maintenance and optimization</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <div class="row g-3">
                        @foreach([['50+','Engineers & Specialists'],['100+','Projects Delivered'],['10+','Industries Served'],['24/7','Support & Monitoring']] as $s)
                        <div class="col-6">
                            <div class="border border-light rounded-3 p-4 text-center">
                                <div class="fw-bold fs-3 mb-1" style="color:#0066FF;">{{ $s[0] }}</div>
                                <div class="text-muted small">{{ $s[1] }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="py-5">
        <div class="container py-2">
            <div class="rounded-4 text-white text-center p-5" style="background:#0077FF;">
                <h2 class="fw-bold mb-2">Work with a team built to scale</h2>
                <p class="mb-4" style="opacity:.85;">Let's discuss your goals and assign the right engineering squad.</p>
                <a href="{{ route('contact') }}" class="btn btn-light fw-semibold rounded-pill px-5">Contact Us</a>
            </div>
        </div>
    </section>
</div>
@endsection
