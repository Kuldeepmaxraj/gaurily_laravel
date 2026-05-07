<nav class="navbar navbar-expand-lg navbar-light fixed-top" style="background:rgba(255,255,255,0.92);backdrop-filter:blur(10px);box-shadow:0 1px 12px rgba(0,0,0,.07);">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('logo.svg') }}" alt="Gaurily" style="height:48px;">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto gap-1">
                <li class="nav-item"><a class="nav-link fw-medium text-dark {{ request()->routeIs('home') ? 'text-primary' : '' }}" href="{{ route('home') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link fw-medium text-dark {{ request()->routeIs('about') ? 'text-primary' : '' }}" href="{{ route('about') }}">About</a></li>
                <li class="nav-item"><a class="nav-link fw-medium text-dark {{ request()->routeIs('services') ? 'text-primary' : '' }}" href="{{ route('services') }}">Services</a></li>
                <li class="nav-item"><a class="nav-link fw-medium text-dark {{ request()->routeIs('careers') ? 'text-primary' : '' }}" href="{{ route('careers') }}">Careers</a></li>
                <li class="nav-item"><a class="nav-link fw-medium text-dark {{ request()->routeIs('blogs') ? 'text-primary' : '' }}" href="{{ route('blogs') }}">Blog</a></li>
                <li class="nav-item"><a class="nav-link fw-medium text-dark {{ request()->routeIs('contact') ? 'text-primary' : '' }}" href="{{ route('contact') }}">Contact</a></li>
                @auth
                <li class="nav-item ms-2">
                    <a class="btn btn-primary btn-sm rounded-pill px-3" href="{{ route('employee.dashboard') }}">My Dashboard</a>
                </li>
                @else
                <li class="nav-item">
                    <a class="nav-link fw-medium text-dark" href="{{ route('login') }}">Login</a>
                </li>
                <li class="nav-item ms-1">
                    <a class="btn btn-sm rounded-pill px-3" style="background:#0066FF;color:#fff;" href="{{ route('careers') }}">We're Hiring</a>
                </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
