<!DOCTYPE html>
<html lang="en" prefix="og: https://ogp.me/ns#">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Primary SEO --}}
    <title>@yield('title', 'Gaurily | Software Development Company in Dehradun')</title>
    <meta name="description" content="@yield('meta_description', 'Gaurily is a software development company in Dehradun building high-performance web, mobile and AI solutions for businesses across India.')">
    <meta name="keywords" content="@yield('meta_keywords', 'software development Dehradun, web development India, mobile app development, AI solutions, Gaurily')">
    <meta name="robots" content="@yield('meta_robots', 'index, follow')">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    {{-- Open Graph --}}
    <meta property="og:type"        content="@yield('og_type', 'website')">
    <meta property="og:site_name"   content="Gaurily">
    <meta property="og:locale"      content="en_IN">
    <meta property="og:title"       content="@yield('og_title', 'Gaurily | Software Development Company in Dehradun')">
    <meta property="og:description" content="@yield('og_description', 'Gaurily builds high-performance web, mobile and AI solutions for businesses across India. Based in Dehradun, Uttarakhand.')">
    <meta property="og:url"         content="@yield('canonical', url()->current())">
    <meta property="og:image"       content="@yield('og_image', asset('og-default.jpg'))">

    {{-- Twitter / X Card --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="@yield('og_title', 'Gaurily | Software Development Company')">
    <meta name="twitter:description" content="@yield('og_description', 'Gaurily builds high-performance web, mobile and AI solutions for businesses across India.')">
    <meta name="twitter:image"       content="@yield('og_image', asset('og-default.jpg'))">

    {{-- JSON-LD Structured Data --}}
    @stack('schema')

    {{-- Fonts & CSS --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .navbar-nav .nav-link:hover { color: #0066FF !important; }
    </style>
    @stack('styles')
</head>
<body>

@include('partials.navbar')

<div id="app-content">
    @yield('content')
</div>

@include('partials.footer')

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>