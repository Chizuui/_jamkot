<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- UI Theme Setup -->
    <script>
        (function() {
            const uiVersion = localStorage.getItem('jamkot-ui-version') || 'v1';
            document.documentElement.setAttribute('data-ui-version', uiVersion);
        })();
    </script>
    
    <title>@yield('title', 'Dashboard') — JAMKOT</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    
    <!-- Existing Theme CSS -->
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}?v={{ filemtime(public_path('css/panel.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}?v={{ filemtime(public_path('css/mobile.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/material3.css') }}?v={{ filemtime(public_path('css/material3.css')) }}">
    
    <!-- Vite for Auto Reload (HMR) -->
    @vite(['resources/js/app.js'])
    
    <!-- Adapter for new layout classes -->
    <style>
        /* Fix for page transition overlay blocking interaction with seamless transition */
        .page-transition-overlay.hidden {
            opacity: 0 !important;
            pointer-events: none !important;
            visibility: hidden !important;
            transition: opacity 0.3s, visibility 0.3s !important;
        }
        /* Base layout structure from _example */
        body {
            display: flex;
            min-height: 100vh;
            background: var(--m3-background, #0f1412);
            color: var(--m3-on-background, #e0e3e1);
            font-family: 'Outfit', sans-serif;
            margin: 0;
        }
        
        .sidebar {
            width: 280px;
            background: var(--m3-surface-container, #1b221f);
            border-right: 1px solid var(--m3-outline-variant, rgba(255,255,255,0.05));
            display: flex;
            flex-direction: column;
            height: 100vh;
            position: sticky;
            top: 0;
            z-index: 100;
            transition: all 0.3s ease;
        }
        
        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }
        
        .topbar {
            background: transparent;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: none;
        }
        
        .page {
            padding: 2.5rem;
            flex: 1;
            overflow-y: auto;
        }
        
        /* Global padding adjustment for cards */
        .glow-card {
            padding: 2rem !important;
        }
        
        /* Sidebar components */
        .sb-brand {
            padding: 1.5rem;
            border-bottom: 1px solid var(--m3-outline-variant, rgba(255,255,255,0.05));
        }
        .sb-brand-name { font-size: 1.25rem; font-weight: 700; color: var(--m3-primary, #80dec5); }
        .sb-brand-sub { font-size: 0.75rem; color: var(--m3-on-surface-variant, #a2aba7); }
        
        .sb-user { padding: 1rem 1.5rem; }
        .sb-user-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem;
            background: rgba(255,255,255,0.03);
            border-radius: 12px;
        }
        .sb-avatar {
            width: 40px; height: 40px;
            background: var(--m3-primary, #80dec5);
            color: var(--m3-on-primary, #00382d);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 600;
        }
        .sb-user-name { font-weight: 600; font-size: 0.9rem; }
        .sb-user-meta { font-size: 0.75rem; color: var(--m3-on-surface-variant, #a2aba7); display: flex; align-items: center; gap: 0.25rem; }
        .sb-online-dot { width: 6px; height: 6px; background: #10b981; border-radius: 50%; }
        
        .sb-nav { padding: 1rem; flex: 1; }
        .sb-link, .sb-dropdown-btn {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: var(--m3-on-surface-variant, #a2aba7);
            text-decoration: none;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s;
            background: transparent; border: none; width: 100%; text-align: left; cursor: pointer;
        }
        .sb-link:hover, .sb-dropdown-btn:hover {
            background: rgba(255,255,255,0.05);
            color: var(--m3-on-surface, #e0e3e1);
        }
        .sb-link.active {
            background: var(--m3-primary-container, #005142);
            color: var(--m3-on-primary-container, #9cf1e1);
        }
        
        .sb-icon { width: 18px; height: 18px; }
        
        /* Dropdown */
        .sb-submenu {
            display: none;
            padding-left: 1.5rem;
            margin-top: 0.25rem;
        }
        .sb-submenu.open { display: block; }
        .sb-sub-link {
            display: flex; align-items: center; gap: 0.5rem;
            padding: 0.5rem 1rem;
            color: var(--m3-on-surface-variant, #a2aba7);
            text-decoration: none;
            font-size: 0.85rem;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .sb-sub-link:hover { color: var(--m3-on-surface, #e0e3e1); }
        .sb-sub-link.active { color: var(--m3-primary, #80dec5); font-weight: 600; }
        .sb-chevron { width: 14px; height: 14px; margin-left: auto; transition: transform 0.2s; }
        .sb-dropdown-btn.open .sb-chevron { transform: rotate(180deg); }
        
        .sb-divider { height: 1px; background: rgba(255,255,255,0.05); margin: 1rem 0; }
        .sb-section-label { font-size: 0.75rem; color: #4b5563; padding: 0 1rem 0.5rem; text-transform: uppercase; letter-spacing: 0.05em; }
        
        /* Neon Glow Overrides (v2) */
        html[data-ui-version="v2"] body {
            background: #0a0a0a;
            color: #ededed;
        }
        html[data-ui-version="v2"] .sidebar {
            background: #141414;
            border-color: #262626;
        }
        html[data-ui-version="v2"] .sb-link.active {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            box-shadow: inset 3px 0 0 #10b981;
            border-radius: 0;
        }
        html[data-ui-version="v2"] .sb-brand-name { color: #10b981; text-shadow: 0 0 10px rgba(16,185,129,0.3); }
        html[data-ui-version="v2"] .topbar { background: transparent; border-color: transparent; }
        
        /* Clock */
        .topbar-clock { text-align: right; }
        .clock-time { font-size: 1.25rem; font-weight: 700; color: var(--m3-primary, #80dec5); }
        .clock-date { font-size: 0.75rem; color: var(--m3-on-surface-variant, #a2aba7); }
        html[data-ui-version="v2"] .clock-time { color: #10b981; }
        
        /* Desktop Collapse */
        .sidebar {
            transition: all 0.3s ease;
        }
        .sidebar.active {
            margin-left: -280px;
        }

        /* Mobile Toggle */
        .btn-toggle-sidebar { display: block; }
        
        /* Mobile Floating Nav */
        .mobile-nav {
            display: none;
        }
        
        @media (max-width: 768px) {
            .sidebar { 
                display: none !important; /* Hide sidebar completely on mobile */
            }
            .btn-toggle-sidebar { display: none !important; } /* Hide hamburger too */
            
            .mobile-nav {
                display: flex;
                position: fixed;
                bottom: 1rem;
                left: 1.5rem;
                right: 1.5rem;
                background: var(--m3-surface-container, #1b221f);
                border-radius: 16px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.3);
                justify-content: space-around;
                align-items: center;
                padding: 0.4rem;
                z-index: 1000;
                border: 1px solid var(--m3-outline-variant, rgba(255,255,255,0.05));
            }
            .mobile-nav-link {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 0.15rem;
                color: var(--m3-on-surface-variant, #a2aba7);
                text-decoration: none;
                font-size: 0.65rem;
                padding: 0.3rem 0.5rem;
                border-radius: 10px;
                transition: all 0.2s;
            }
            .mobile-nav-link i {
                font-size: 1.1rem;
            }
            .mobile-nav-link.active {
                color: var(--m3-primary, #80dec5);
                background: rgba(128, 222, 197, 0.1);
            }
            
            /* Neon Glow Theme Adaptation for Mobile Nav */
            html[data-ui-version="v2"] .mobile-nav {
                background: rgba(10, 14, 12, 0.9) !important;
                border-color: rgba(16, 185, 129, 0.2) !important;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5), 0 0 10px rgba(16, 185, 129, 0.1) !important;
            }
            html[data-ui-version="v2"] .mobile-nav-link {
                color: #a2aba7 !important;
            }
            html[data-ui-version="v2"] .mobile-nav-link.active {
                color: #10b981 !important;
                background: rgba(16, 185, 129, 0.1) !important;
                text-shadow: 0 0 5px rgba(16,185,129,0.5);
            }
            
            .topbar { padding: 1rem 1.5rem !important; }
            .page { 
                padding: 1.5rem !important; 
                padding-bottom: 6rem !important; /* Give space for floating nav */
            }
            .glow-card { padding: 1.25rem !important; }
        }
    </style>
    
    @yield('styles')
</head>
<body>

{{-- ── Sidebar ── --}}
<aside class="sidebar">
    <div class="sb-brand">
        <div class="sb-brand-name">JAMKOT</div>
        <div class="sb-brand-sub">Mushroom Monitor</div>
    </div>

    <div class="sb-user">
        <div class="sb-user-card">
            <div class="sb-avatar">
                {{ strtoupper(substr(Auth::user()->username ?? 'AD', 0, 2)) }}
            </div>
            <div class="sb-user-info">
                <div class="sb-user-name">{{ Auth::user()->username ?? 'Admin' }}</div>
                <div class="sb-user-meta">
                    <span class="sb-online-dot"></span>
                    @if(Auth::user()->canAccess('admin'))
                        <span class="role-badge role-admin">Admin</span>
                    @else
                        <span class="role-badge role-user">User</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <nav class="sb-nav">
        @if(Auth::user()->canAccess('panel'))
        <a href="{{ route('panel') }}" class="sb-link {{ request()->routeIs('panel') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge sb-icon"></i>
            <span>Panel Utama</span>
        </a>
        @endif

        @if(Auth::user()->canAccess('analisis'))
        <a href="{{ route('analisis') }}" class="sb-link {{ request()->routeIs('analisis') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-simple sb-icon"></i>
            <span>Analisis</span>
        </a>
        @endif

        {{-- ── Data Sensor dropdown ── --}}
        <button
            class="sb-dropdown-btn {{ request()->routeIs('sensor.*') ? 'open' : '' }}"
            id="sensorDropdownBtn"
            aria-expanded="{{ request()->routeIs('sensor.*') ? 'true' : 'false' }}"
            aria-controls="sensorSubmenu"
            type="button"
        >
            <i class="fa-solid fa-microchip sb-icon"></i>
            <span>Data Sensor</span>
            <i class="fa-solid fa-chevron-down sb-chevron"></i>
        </button>

        <div
            class="sb-submenu {{ request()->routeIs('sensor.*') ? 'open' : '' }}"
            id="sensorSubmenu"
        >
            {{-- Sensor 1: LDR --}}
            <a href="{{ route('sensor.ldr') }}" class="sb-sub-link {{ request()->routeIs('sensor.ldr') ? 'active' : '' }}">
                <i class="fa-solid fa-sun"></i>
                <span>Sensor LDR</span>
            </a>

            {{-- Sensor 2: DHT22 --}}
            <a href="{{ route('sensor.dht22') }}" class="sb-sub-link {{ request()->routeIs('sensor.dht22') ? 'active' : '' }}">
                <i class="fa-solid fa-temperature-half"></i>
                <span>Sensor DHT22</span>
            </a>
        </div>

        @if(Auth::user()->canAccess('schedule'))
        <a href="{{ route('schedule') }}" class="sb-link {{ request()->routeIs('schedule') ? 'active' : '' }}">
            <i class="fa-solid fa-clock sb-icon"></i>
            <span>Schedules</span>
        </a>
        @endif

        @if(Auth::user()->canAccess('settings'))
        <a href="{{ route('settings.index') }}" class="sb-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <i class="fa-solid fa-gear sb-icon"></i>
            <span>Settings</span>
        </a>
        @endif

        @if(Auth::user()->canAccess('view3d'))
        <a href="{{ route('view3d') }}" class="sb-link {{ request()->routeIs('view3d') ? 'active' : '' }}">
            <i class="fa-solid fa-cube sb-icon"></i>
            <span>3D View</span>
        </a>
        @endif

        @if(Auth::user()->canAccess('admin'))
        <div class="sb-divider"></div>
        <div class="sb-section-label">Admin</div>
        <a href="{{ route('admin.users') }}" class="sb-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">
            <i class="fa-solid fa-users-gear sb-icon"></i>
            <span>Kelola User</span>
        </a>
        @endif
    </nav>

    <div class="sb-footer" style="padding: 1rem;">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="sb-link" type="submit" style="width: 100%; justify-content: flex-start;">
                <i class="fa-solid fa-right-from-bracket sb-icon"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>

{{-- ── Main ── --}}
<div class="main panel-content">
    <header class="topbar">
        <div class="topbar-left" style="display: flex; align-items: center; gap: 1rem;">
            <button class="btn-toggle-sidebar" id="sidebar-toggle" style="background: none; border: none; color: inherit; font-size: 1.25rem; cursor: pointer;">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div>
                <h1 style="margin: 0; font-size: 1.5rem; font-weight: 700;">@yield('page-title')</h1>
                <p style="margin: 0.25rem 0 0; font-size: 0.85rem; color: var(--m3-on-surface-variant, #a2aba7);">@yield('page-sub')</p>
            </div>
        </div>
        <div class="topbar-clock">
            <div class="clock-time" id="clk">00:00:00</div>
            <div class="clock-date" id="clkd"></div>
        </div>
    </header>

    <div class="page">
        @yield('content')
    </div>
</div>

{{-- ── Mobile Floating Nav ── --}}
<nav class="mobile-nav">
    <a href="{{ route('panel') }}" class="mobile-nav-link {{ request()->routeIs('panel') ? 'active' : '' }}">
        <i class="fa-solid fa-gauge"></i>
        <span>Panel</span>
    </a>
    <a href="{{ route('analisis') }}" class="mobile-nav-link {{ request()->routeIs('analisis') ? 'active' : '' }}">
        <i class="fa-solid fa-chart-simple"></i>
        <span>Analisis</span>
    </a>
    <a href="{{ route('sensor.ldr') }}" class="mobile-nav-link {{ request()->routeIs('sensor.*') ? 'active' : '' }}">
        <i class="fa-solid fa-microchip"></i>
        <span>Sensor</span>
    </a>
    <a href="{{ route('view3d') }}" class="mobile-nav-link {{ request()->routeIs('view3d') ? 'active' : '' }}">
        <i class="fa-solid fa-cube"></i>
        <span>3D</span>
    </a>
    <a href="{{ route('settings.index') }}" class="mobile-nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
        <i class="fa-solid fa-gear"></i>
        <span>Settings</span>
    </a>
</nav>

<script>
(function(){
    /* ── Clock ── */
    var D=['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    var M=['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    function p(n){return String(n).padStart(2,'0');}
    function tick(){
        var d=new Date();
        var clkEl = document.getElementById('clk');
        var clkdEl = document.getElementById('clkd');
        if (clkEl) clkEl.textContent=p(d.getHours())+':'+p(d.getMinutes())+':'+p(d.getSeconds());
        if (clkdEl) clkdEl.textContent=D[d.getDay()]+', '+d.getDate()+' '+M[d.getMonth()]+' '+d.getFullYear();
    }
    tick(); setInterval(tick,1000);

    /* ── Sidebar Toggle ── */
    var sidebarToggle = document.getElementById('sidebar-toggle');
    var sidebar = document.querySelector('.sidebar');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }

    /* ── Sensor dropdown toggle ── */
    var btn     = document.getElementById('sensorDropdownBtn');
    var submenu = document.getElementById('sensorSubmenu');

    if (btn && submenu) {
        btn.addEventListener('click', function(){
            var isOpen = submenu.classList.contains('open');
            if(isOpen){
                submenu.classList.remove('open');
                btn.classList.remove('open');
                btn.setAttribute('aria-expanded','false');
            } else {
                submenu.classList.add('open');
                btn.classList.add('open');
                btn.setAttribute('aria-expanded','true');
            }
        });
    }
}());
</script>

<script src="{{ asset('js/sidebar.js') }}"></script>

@yield('scripts')
</body>
</html>
