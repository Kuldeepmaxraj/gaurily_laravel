@extends('layouts.app')
@section('title', 'Contact Gaurily | Software Development Company in Dehradun')
@section('meta_description', 'Get in touch with Gaurily for your software project. Based in Dehradun, Uttarakhand. Email: care@gaurily.com | Phone: 8699902209. We\'d love to hear from you!')
@section('meta_keywords', 'contact Gaurily, software company contact Dehradun, hire developers India, software project inquiry')
@section('og_title', 'Contact Gaurily | Let\'s Build Something Together')
@section('og_description', 'Reach out to Gaurily for your software project. Email care@gaurily.com or call 8699902209. Based in Dehradun, Uttarakhand.')
@push('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "name": "Gaurily",
  "url": "{{ url('/') }}",
  "telephone": "+91-8699902209",
  "email": "care@gaurily.com",
  "address": {
    "@type": "PostalAddress",
    "addressLocality": "Dehradun",
    "addressRegion": "Uttarakhand",
    "addressCountry": "IN"
  },
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": 30.3165,
    "longitude": 78.0322
  }
}
</script>
@endpush
@section('content')
<div style="padding-top:80px;">
    <section class="py-5" style="background:linear-gradient(to bottom,#eff6ff,#fff);">
        <div class="container py-4 text-center">
            <span class="badge rounded-pill px-4 py-2 mb-3" style="background:#dbeafe;color:#0066FF;">Contact Us</span>
            <h1 class="display-5 fw-bold mb-3">Get in <span style="color:#0066FF;">Touch</span></h1>
            <p class="text-muted lead mx-auto" style="max-width:640px;">We'd love to hear from you! Whether you have a project in mind, need support, or just want to learn more about our services, feel free to reach out.</p>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container py-4">
            {{-- Contact Cards --}}
            <div class="row g-4 justify-content-center mb-5" style="max-width:800px;margin:0 auto;">
                <div class="col-md-4 text-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width:64px;height:64px;background:#eff6ff;">
                        <i class="bi bi-geo-alt-fill fs-4" style="color:#0066FF;"></i>
                    </div>
                    <h5 class="fw-semibold mb-1">Address</h5>
                    <p class="text-muted small mb-0">Gaurily, Dehradun, Uttarakhand, India</p>
                </div>
                <div class="col-md-4 text-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width:64px;height:64px;background:#eff6ff;">
                        <i class="bi bi-envelope-fill fs-4" style="color:#0066FF;"></i>
                    </div>
                    <h5 class="fw-semibold mb-1">Email</h5>
                    <a href="mailto:care@gaurily.com" class="text-muted small text-decoration-none">care@gaurily.com</a>
                </div>
                <div class="col-md-4 text-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width:64px;height:64px;background:#eff6ff;">
                        <i class="bi bi-telephone-fill fs-4" style="color:#0066FF;"></i>
                    </div>
                    <h5 class="fw-semibold mb-1">Phone</h5>
                    <a href="tel:8699902209" class="text-muted small text-decoration-none">8699902209</a>
                </div>
            </div>

            {{-- Form + Map --}}
            <div class="row g-5">
                <div class="col-md-6">
                    <h3 class="fw-bold mb-2">Send Us a Message</h3>
                    <p class="text-muted small mb-4">Fill out the form and our team will get back to you as soon as possible.</p>
                    <form>
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="text" class="form-control" placeholder="Full Name">
                            </div>
                            <div class="col-md-6">
                                <input type="email" class="form-control" placeholder="Email Address">
                            </div>
                            <div class="col-md-6">
                                <input type="tel" class="form-control" placeholder="Phone Number">
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control" placeholder="Subject">
                            </div>
                            <div class="col-12">
                                <textarea class="form-control" rows="5" placeholder="Message"></textarea>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary w-100 rounded-pill">Send Message</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-6">
                    <h3 class="fw-bold mb-2">Our Location</h3>
                    <p class="text-muted small mb-4">Based in Dehradun, Uttarakhand, we serve clients across India and globally.</p>
                    <div class="rounded-3 overflow-hidden" style="height:400px;">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d55074.69857787872!2d77.9801924!3d30.3164945!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390929c356c888af%3A0x4c3562c032518799!2sDehradun%2C%20Uttarakhand!5e0!3m2!1sen!2sin!4v1710437265040!5m2!1sen!2sin"
                            width="100%" height="100%" style="border:0;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection