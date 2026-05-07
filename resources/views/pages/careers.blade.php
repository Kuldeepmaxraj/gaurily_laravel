@extends('layouts.app')
@section('title', 'Careers at Gaurily | Jobs in Dehradun | Software Company')
@section('meta_description', 'Join Gaurily\'s engineering team in Dehradun. We\'re hiring talented developers, designers and product managers. Build great products and grow your career with us.')
@section('meta_keywords', 'jobs Dehradun, software developer jobs Uttarakhand, careers Gaurily, hiring developers, tech jobs India')
@section('og_title', 'Careers at Gaurily | Join Our Engineering Team')
@section('og_description', 'We\'re hiring talented developers, designers and product managers in Dehradun. Build great products and grow your career with Gaurily.')
@section('content')
<div style="padding-top:80px;">
    <section class="py-5" style="background:linear-gradient(to bottom,#eff6ff,#fff);">
        <div class="container py-4 text-center">
            <span class="badge rounded-pill px-4 py-2 mb-3" style="background:#dbeafe;color:#0066FF;">Careers</span>
            <h1 class="display-5 fw-bold mb-3">Join the <span style="color:#0066FF;">Gaurily</span> Team</h1>
            <p class="text-muted lead mx-auto" style="max-width:680px;">We're looking for passionate people to build world-class products. Explore open positions and grow your career with us in Dehradun.</p>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="row g-4">
                @php
                $jobs = [
                    [
                        'title'  => 'PHP Developer',
                        'type'   => 'Full Time',
                        'exp'    => '3 - 7 years • Dehradun (Doon IT Park)',
                        'perks'  => 'Health insurance, Annual bonus',
                        'dept'   => 'Full Stack Developer • IT Services & Consulting • Engineering - Software & QA',
                        'skills' => ['Core PHP','Laravel','CodeIgniter','JavaScript','CSS','HTML','Bootstrap'],
                        'resp'   => ['Collaborate with cross-functional teams on project requirements and deliverables.','Develop scalable PHP applications using Laravel, CodeIgniter, Core PHP.'],
                    ],
                    [
                        'title'  => 'Graphic Designer',
                        'type'   => 'Full Time',
                        'exp'    => '3 - 8 years • Dehradun',
                        'perks'  => 'Health insurance',
                        'dept'   => 'Graphic Designer • IT Services & Consulting • UX, Design & Architecture',
                        'skills' => ['Illustrator','InDesign','Photoshop','Branding'],
                        'resp'   => ['Create visually appealing designs using Adobe Creative Cloud tools (Illustrator, InDesign, Photoshop).','Ensure brand consistency across all materials.'],
                    ],
                    [
                        'title'  => 'QA Engineer',
                        'type'   => 'Full Time',
                        'exp'    => '2 - 5 years • Dehradun (Doon IT Park)',
                        'perks'  => 'Health insurance, Annual bonus',
                        'dept'   => 'QA Engineer • IT Services & Consulting • Engineering - Software & QA',
                        'skills' => ['Manual Testing','Test Cases','Bug Tracking'],
                        'resp'   => ['Design and execute test plans for web and mobile applications.','Collaborate with developers to reproduce, track, and verify bug fixes.','Support manual and basic automation testing efforts.'],
                    ],
                ];
                @endphp

                @foreach($jobs as $job)
                <div class="col-md-4">
                    <div class="border border-light rounded-3 p-4 h-100">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="fw-semibold mb-0">{{ $job['title'] }}</h5>
                            <span class="badge rounded-pill px-3" style="background:#dbeafe;color:#0066FF;font-size:11px;">{{ $job['type'] }}</span>
                        </div>
                        <p class="text-muted small mb-3">{{ $job['exp'] }}</p>

                        <p class="fw-medium small mb-1">Responsibilities</p>
                        <ul class="small text-muted mb-3 ps-3">
                            @foreach($job['resp'] as $r)
                            <li class="mb-1">{{ $r }}</li>
                            @endforeach
                        </ul>

                        <p class="fw-medium small mb-1">Perks & Benefits</p>
                        <p class="text-muted small mb-3">{{ $job['perks'] }}</p>

                        <p class="text-muted small mb-3">{{ $job['dept'] }}</p>

                        <div class="d-flex flex-wrap gap-1 mb-3">
                            @foreach($job['skills'] as $skill)
                            <span class="badge bg-light text-secondary border" style="font-size:11px;">{{ $skill }}</span>
                            @endforeach
                        </div>

                        <a href="mailto:care@gaurily.com?subject=Application - {{ urlencode($job['title']) }}" style="color:#0066FF;" class="small fw-medium">Apply Now &rarr;</a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
</div>
@endsection
