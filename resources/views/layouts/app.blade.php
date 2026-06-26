<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'UTM Sports Facilities') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Lora:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

{{-- ══ Sidebar ════════════════════════════════════════════ --}}
<aside class="utm-sidebar" id="sidebar">

    {{-- Brand --}}
    <div class="utm-sidebar-brand">
        <a href="{{ route('dashboard') }}" class="utm-sidebar-logo">
            <div class="utm-sidebar-logo-mark">
                {{-- UTM Logo SVG --}}
                <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" width="28" height="28">
                    <rect width="40" height="40" rx="8" fill="#8B0000"/>
                    <text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle"
                          font-family="Georgia,serif" font-weight="700" font-size="13" fill="white">UTM</text>
                </svg>
            </div>
            <div class="utm-sidebar-logo-text">
                <span class="system-name">Sports Facilities</span>
                <span class="university-name">Universiti Teknologi Malaysia</span>
            </div>
        </a>
    </div>

    {{-- Navigation --}}
    <nav class="utm-sidebar-nav">

        @auth
            {{-- ── Admin ── --}}
            @if(Auth::user()->isAdmin())
                <span class="utm-nav-section-label">Administration</span>

                <a href="{{ route('dashboard') }}"
                   class="utm-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('admin.users.index') }}"
                   class="utm-nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    User Management
                </a>

            {{-- ── Rental Officer ── --}}
            @elseif(Auth::user()->isRentalOfficer())
                <span class="utm-nav-section-label">Management</span>

                <a href="{{ route('dashboard') }}"
                   class="utm-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('facilities.index') }}"
                   class="utm-nav-item {{ request()->routeIs('facilities.*') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Facilities
                </a>

                <a href="{{ route('bookings.all') }}"
                   class="utm-nav-item {{ request()->routeIs('bookings.all') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Bookings
                </a>

                <a href="{{ route('feedback.all') }}"
                   class="utm-nav-item {{ request()->routeIs('feedback.all') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                    Feedbacks
                </a>

                <a href="{{ route('officer.announcements.index') }}"
                   class="utm-nav-item {{ request()->routeIs('officer.announcements.*') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                    Announcements
                </a>

                <a href="{{ route('analytics.index') }}"
                   class="utm-nav-item {{ request()->routeIs('analytics.index') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Analytics
                </a>

            {{-- ── Staff / Student / Guest ── --}}
            @else
                <span class="utm-nav-section-label">Navigation</span>

                <a href="{{ route('dashboard') }}"
                   class="utm-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('facilities.index') }}"
                   class="utm-nav-item {{ request()->routeIs('facilities.*') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Browse Facilities
                </a>

                <a href="{{ route('bookings.my') }}"
                   class="utm-nav-item {{ request()->routeIs('bookings.my') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    My Bookings
                </a>

                <a href="{{ route('feedback.my') }}"
                   class="utm-nav-item {{ request()->routeIs('feedback.my') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                    My Feedbacks
                </a>

                <a href="{{ route('announcements.index') }}"
                   class="utm-nav-item {{ request()->routeIs('announcements.index') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                    Announcements
                </a>

                <a href="{{ route('recommendations.index') }}"
                   class="utm-nav-item {{ request()->routeIs('recommendations.*') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Recommended for You
                </a>
            @endif

            {{-- ── Profile (all roles) ── --}}
            <span class="utm-nav-section-label" style="margin-top:24px;">Account</span>

            <a href="{{ route('profile.edit') }}"
               class="utm-nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Profile
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="utm-nav-item" style="color:rgba(255,255,255,.55);">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Sign Out
                </button>
            </form>
        @endauth
    </nav>

    {{-- User Card --}}
    @auth
    <div class="utm-sidebar-footer">
        <div class="utm-user-card">
            <div class="utm-user-avatar">
                {{ strtoupper(substr(Auth::user()->fullname, 0, 1)) }}
            </div>
            <div class="utm-user-info" style="min-width:0">
                <div class="user-name">{{ Auth::user()->fullname }}</div>
                <div class="user-role">{{ Auth::user()->role_label }}</div>
            </div>
        </div>
    </div>
    @endauth
</aside>

{{-- ══ Main Content ═══════════════════════════════════════ --}}
<div class="utm-main">

    {{-- Topbar --}}
    <div class="utm-topbar">
        {{-- Mobile hamburger --}}
        <button id="sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')"
                class="lg:hidden p-2 rounded-lg text-slate-500 hover:bg-slate-100 transition"
                style="display:none;">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <div>
            @isset($header)
                <div class="utm-topbar-title">{{ $header }}</div>
            @else
                <div class="utm-topbar-title">UTM Sports Facilities</div>
            @endisset
        </div>

        {{-- Right side --}}
        <div style="display:flex;align-items:center;gap:12px;">
            @auth
                <div style="font-size:12px;color:var(--slate-400);text-align:right;display:none;" class="sm:block">
                    <div style="font-weight:600;color:var(--slate-600);">{{ Auth::user()->fullname }}</div>
                    <div>{{ Auth::user()->role_label }}</div>
                </div>
            @endauth
        </div>
    </div>

    {{-- Page content --}}
    <div class="utm-page-content">
        {{ $slot }}
    </div>

    {{-- Footer --}}
    <footer style="padding:20px 32px;border-top:1px solid var(--slate-100);font-size:12px;color:var(--slate-400);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
        <span>© {{ date('Y') }} Universiti Teknologi Malaysia. All rights reserved.</span>
        <span>UTM Sports Facilities Rental System</span>
    </footer>
</div>

<style>
@media (max-width:1024px) {
    #sidebar-toggle { display:flex !important; }
}
</style>

</body>
</html>