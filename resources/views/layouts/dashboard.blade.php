<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | Gaurily</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #f7f8fc; }

        /* ── Sidebar ── */
        .sidebar {
            height: 100vh;
            background: #fff;
            width: 240px;
            position: fixed;
            top: 0; left: 0;
            z-index: 1040;
            border-right: 1px solid #eef0f6;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            overflow-x: hidden;
            scrollbar-width: thin;
            scrollbar-color: #e5e7eb transparent;
        }
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 4px; }
        .sidebar-logo {
            padding: 20px 20px 16px;
            border-bottom: 1px solid #eef0f6;
            flex-shrink: 0;
        }
        .sidebar a {
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 16px;
            margin: 2px 8px;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            border-radius: 8px;
            transition: all .15s;
        }
        .sidebar a:hover { color: #0066FF; background: #eff6ff; }
        .sidebar a.active { color: #0066FF; background: #eff6ff; font-weight: 600; }
        .sidebar a i { font-size: 15px; width: 18px; flex-shrink: 0; }
        .nav-section {
            color: #9ca3af;
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            padding: 16px 24px 4px;
            flex-shrink: 0;
        }

        /* Sidebar overlay for mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 1039;
        }
        .sidebar-overlay.show { display: block; }

        /* ── Topbar ── */
        .topbar {
            position: fixed;
            top: 0; left: 240px; right: 0;
            height: 60px;
            background: #fff;
            border-bottom: 1px solid #eef0f6;
            z-index: 99;
            display: flex;
            align-items: center;
            padding: 0 28px;
        }
        .topbar-title {
            font-weight: 600;
            font-size: 15px;
            color: #111827;
        }
        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 20px;
            color: #374151;
            padding: 0;
            margin-right: 12px;
            cursor: pointer;
        }

        /* ── Main ── */
        .main-content { margin-left: 240px; padding: 80px 28px 40px; }

        /* ── Cards ── */
        .card { border-radius: 12px !important; }
        .stat-card { border: 1px solid #eef0f6 !important; border-radius: 14px !important; }

        /* ── Tables ── */
        .table th { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; color: #9ca3af; border-bottom: 1px solid #eef0f6 !important; }
        .table td { font-size: 13.5px; color: #374151; vertical-align: middle; border-color: #f3f4f6 !important; }
        .table-hover tbody tr:hover { background: #f9fafb; }

        /* ── Buttons ── */
        .btn-primary { background: #0066FF; border-color: #0066FF; }
        .btn-primary:hover { background: #0052cc; border-color: #0052cc; }
        .btn-outline-primary { color: #0066FF; border-color: #0066FF; }
        .btn-outline-primary:hover { background: #0066FF; border-color: #0066FF; }

        /* ── Forms ── */
        .form-control, .form-select { font-size: 13.5px; border-color: #e5e7eb; border-radius: 8px; }
        .form-control:focus, .form-select:focus { border-color: #0066FF; box-shadow: 0 0 0 3px rgba(0,102,255,.12); }
        .form-label { font-size: 13px; font-weight: 500; color: #374151; }

        /* ── Avatar ── */
        .user-avatar {
            width: 34px; height: 34px;
            background: #eff6ff;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700; color: #0066FF;
        }

        @media(max-width: 991px){
            .sidebar { transform: translateX(-100%); transition: transform .25s ease; }
            .sidebar.show { transform: translateX(0); }
            .topbar { left: 0; }
            .main-content { margin-left: 0; }
            .sidebar-toggle { display: block; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- Sidebar overlay (mobile) --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

{{-- Sidebar --}}
<nav class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <a href="{{ route('home') }}" class="d-block" style="padding:0;margin:0;background:none;border-radius:0;">
            <img src="{{ asset('logo.svg') }}" alt="Gaurily" style="height:36px;">
        </a>
    </div>

    @php
        $user = auth()->user();
        $isAdmin = $user?->hasRole('admin');
        $isHr = $user?->hasRole('hr');
        $isTeamLead = $user?->hasRole('team_lead');
    @endphp

    @if($isAdmin || $isHr || $isTeamLead)
        <div class="nav-section">Admin</div>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2"></i> Dashboard
        </a>
        <a href="{{ route('admin.employees') }}" class="{{ request()->routeIs('admin.employees*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Employees
        </a>
        <a href="{{ route('admin.teams') }}" class="{{ request()->routeIs('admin.teams*') ? 'active' : '' }}">
            <i class="bi bi-diagram-3"></i> Teams
        </a>
        @if($isAdmin || $isHr)
        <a href="{{ route('admin.blogs.index') }}" class="{{ request()->routeIs('admin.blogs*') ? 'active' : '' }}">
            <i class="bi bi-newspaper"></i> Blog
        </a>
        @endif
        <a href="{{ route('admin.attendance') }}" class="{{ request()->routeIs('admin.attendance') ? 'active' : '' }}">
            <i class="bi bi-calendar-check"></i> Attendance
        </a>
        @if($isAdmin || $isTeamLead)
        <a href="{{ route('admin.leave.pending') }}" class="{{ request()->routeIs('admin.leave.pending') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-text"></i> Leave Requests
        </a>
        @if(Route::has('admin.leave.records'))
        <a href="{{ route('admin.leave.records') }}" class="{{ request()->routeIs('admin.leave.records') ? 'active' : '' }}">
            <i class="bi bi-journal-check"></i> Leave Records
        </a>
        @endif
        @endif
        @if($isAdmin || $isHr)
        <a href="{{ route('admin.shifts') }}" class="{{ request()->routeIs('admin.shifts*') ? 'active' : '' }}">
            <i class="bi bi-clock"></i> Shifts & Timings
        </a>
        <a href="{{ route('admin.leave-balances') }}" class="{{ request()->routeIs('admin.leave-balances*') ? 'active' : '' }}">
            <i class="bi bi-calendar2-check"></i> Leave Balances
        </a>
        <a href="{{ route('admin.holidays') }}" class="{{ request()->routeIs('admin.holidays*') ? 'active' : '' }}">
            <i class="bi bi-calendar-heart"></i> Holidays
        </a>
        <a href="{{ route('admin.settings') }}" class="{{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
            <i class="bi bi-sliders"></i> Settings
        </a>
        @endif
    @endif

    <div class="nav-section">My Account</div>
    <a href="{{ route('employee.dashboard') }}" class="{{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
        <i class="bi bi-house"></i> Dashboard
    </a>
    <a href="{{ route('employee.attendance.history') }}" class="{{ request()->routeIs('employee.attendance*') ? 'active' : '' }}">
        <i class="bi bi-clock-history"></i> My Attendance
    </a>
    <a href="{{ route('employee.leave.index') }}" class="{{ request()->routeIs('employee.leave*') ? 'active' : '' }}">
        <i class="bi bi-calendar3"></i> My Leaves
    </a>
    <a href="{{ route('employee.holidays') }}" class="{{ request()->routeIs('employee.holidays') ? 'active' : '' }}">
        <i class="bi bi-calendar-heart"></i> Holidays
    </a>
    <a href="{{ route('employee.profile') }}" class="{{ request()->routeIs('employee.profile*') ? 'active' : '' }}">
        <i class="bi bi-person"></i> My Profile
    </a>
    <a href="{{ route('employee.chat.index') }}" class="{{ request()->routeIs('employee.chat*') ? 'active' : '' }}">
        <i class="bi bi-chat-dots"></i> Chat
        <span id="chatNavBadge" class="ms-auto" style="background:#0066FF;color:#fff;border-radius:99px;font-size:10px;font-weight:700;padding:1px 6px;min-width:18px;text-align:center;display:none;"></span>
    </a>
</nav>

{{-- Topbar --}}
<div class="topbar">
    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
        <i class="bi bi-list"></i>
    </button>
    <span class="topbar-title d-none d-md-block">@yield('title', 'Dashboard')</span>
    <div class="ms-auto d-flex align-items-center gap-3">
        @php $authUser = auth()->user(); @endphp
        @if($authUser?->avatar)
            <img src="{{ \Illuminate\Support\Facades\Storage::url($authUser->avatar) }}"
                 alt="Avatar"
                 class="rounded-circle"
                 style="width:34px;height:34px;object-fit:cover;flex-shrink:0;">
        @else
            <div class="user-avatar">{{ strtoupper(substr($authUser?->name ?? 'U', 0, 1)) }}</div>
        @endif
        <div class="d-none d-sm-block">
            <div style="font-size:13px;font-weight:600;color:#111827;line-height:1.2;">{{ $authUser?->name }}</div>
            <div style="font-size:11px;color:#9ca3af;">{{ $authUser?->role?->display_name }}</div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="mb-0">
            @csrf
            <button class="btn btn-sm rounded-pill px-3" style="border:1px solid #fee2e2;color:#ef4444;font-size:12.5px;background:#fff;">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </button>
        </form>
    </div>
</div>

{{-- Main content --}}
<div class="main-content">
    @if(session('success'))
        <div class="alert border-0 rounded-3 mb-4" style="background:#f0fdf4;color:#166534;font-size:13.5px;">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close float-end" data-bs-dismiss="alert" style="font-size:11px;"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert border-0 rounded-3 mb-4" style="background:#fef2f2;color:#991b1b;font-size:13.5px;">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close float-end" data-bs-dismiss="alert" style="font-size:11px;"></button>
        </div>
    @endif

    @yield('content')
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggle  = document.getElementById('sidebarToggle');
    if (toggle) {
        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        });
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
        // Close sidebar when a nav link is clicked on mobile
        sidebar.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992) {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                }
            });
        });
    }
</script>
@stack('scripts')
@auth
<script>
// Live unread chat badge on all pages (polls every 30s)
(function() {
    const badge = document.getElementById('chatNavBadge');
    if (!badge) return;
    function fetchUnread() {
        fetch('/dashboard/chat/unread-total', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        }).then(r => r.json()).then(d => {
            const n = d.total || 0;
            badge.textContent = n > 0 ? (n > 99 ? '99+' : n) : '';
            badge.style.display = n > 0 ? 'inline-flex' : 'none';
        }).catch(() => {});
    }
    fetchUnread();
    setInterval(fetchUnread, 30000);
})();
</script>
@endauth
</body>
</html>
