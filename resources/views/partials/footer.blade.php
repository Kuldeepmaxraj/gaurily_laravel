<footer class="bg-white px-4 py-5 border-top">
    <div class="container py-4">
        <div class="row g-4">
            <!-- Company Info -->
            <div class="col-md-4">
                <img src="{{ asset('logo.svg') }}" alt="Gaurily" style="height:56px;" class="mb-3">
                <p class="text-muted small">Delivering cutting-edge technology solutions that drive business success.</p>
                <div class="d-flex gap-3 mt-3">
                    <a href="#" class="text-muted"><i class="bi bi-linkedin fs-5"></i></a>
                    <a href="#" class="text-muted"><i class="bi bi-twitter-x fs-5"></i></a>
                    <a href="#" class="text-muted"><i class="bi bi-facebook fs-5"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-6 col-md-2">
                <h6 class="fw-semibold mb-3">Quick Links</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="{{ route('home') }}" class="text-muted text-decoration-none">Home</a></li>
                    <li class="mb-2"><a href="{{ route('about') }}" class="text-muted text-decoration-none">About Us</a></li>
                    <li class="mb-2"><a href="{{ route('services') }}" class="text-muted text-decoration-none">Services</a></li>
                    <li class="mb-2"><a href="{{ route('contact') }}" class="text-muted text-decoration-none">Contact</a></li>
                </ul>
            </div>

            <!-- Services -->
            <div class="col-6 col-md-3">
                <h6 class="fw-semibold mb-3">Services</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="{{ route('services') }}" class="text-muted text-decoration-none">Web Development</a></li>
                    <li class="mb-2"><a href="{{ route('services') }}" class="text-muted text-decoration-none">AI Solutions</a></li>
                    <li class="mb-2"><a href="{{ route('services') }}" class="text-muted text-decoration-none">Mobile Apps</a></li>
                    <li class="mb-2"><a href="{{ route('services') }}" class="text-muted text-decoration-none">Power BI Analytics</a></li>
                    <li class="mb-2"><a href="{{ route('services') }}" class="text-muted text-decoration-none">Staff Augmentation</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="col-md-3">
                <h6 class="fw-semibold mb-3">Contact Info</h6>
                <ul class="list-unstyled small">
                    <li class="d-flex gap-2 mb-3">
                        <i class="bi bi-geo-alt-fill text-primary mt-1"></i>
                        <span class="text-muted">Gaurily, Dehradun, Uttarakhand, India</span>
                    </li>
                    <li class="d-flex gap-2 mb-3">
                        <i class="bi bi-envelope-fill text-primary"></i>
                        <a href="mailto:care@gaurily.com" class="text-muted text-decoration-none">care@gaurily.com</a>
                    </li>
                    <li class="d-flex gap-2 mb-3">
                        <i class="bi bi-telephone-fill text-primary"></i>
                        <a href="tel:8699902209" class="text-muted text-decoration-none">8699902209</a>
                    </li>
                </ul>
                <a href="{{ route('contact') }}" class="btn btn-primary btn-sm rounded-pill px-4">Contact Us</a>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="mt-4 pt-4 border-top d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <p class="text-muted small mb-0">&copy; {{ date('Y') }} Gaurily. All rights reserved.</p>
            <div class="d-flex gap-4">
                <a href="#" class="text-muted small text-decoration-none">Privacy Policy</a>
                <a href="#" class="text-muted small text-decoration-none">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>
