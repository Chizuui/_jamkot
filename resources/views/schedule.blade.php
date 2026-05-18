<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- PREVENT FOUC & SETUP UI THEME -->
    <script>
        (function() {
            const uiVersion = localStorage.getItem('jamkot-ui-version') || 'v1';
            document.documentElement.setAttribute('data-ui-version', uiVersion);
        })();
    </script>
    <title>SCHEDULE | JAMKOT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">
    <link rel="stylesheet" href="{{ asset('css/schedule.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/material3.css') }}">
    @vite('resources/js/app.js')
    <style>
        /* --- MATERIAL 3 EXPRESSIVE OVERRIDES FOR SCHEDULE PAGE --- */
        html[data-ui-version="v1"] .schedule-card {
            background: var(--m3-surface-container) !important;
            border: none !important;
            border-radius: 28px !important;
            padding: 2rem !important;
            box-shadow: none !important;
        }

        html[data-ui-version="v1"] .schedule-card:hover {
            background: var(--m3-surface-container-high) !important;
            transform: translateY(-2px) !important;
        }

        html[data-ui-version="v1"] .card-header-flex {
            border-bottom-color: var(--m3-outline-variant) !important;
        }

        html[data-ui-version="v1"] .schedule-card .card-title {
            color: var(--m3-primary) !important;
            font-family: var(--m3-font) !important;
            font-weight: 700 !important;
        }

        html[data-ui-version="v1"] .input-group label {
            color: var(--m3-on-surface-variant) !important;
            font-family: var(--m3-font) !important;
            font-weight: 600 !important;
        }

        html[data-ui-version="v1"] .input-time-modern {
            background-color: var(--m3-surface-container-highest) !important;
            color: var(--m3-on-surface) !important;
            border: 1px solid var(--m3-outline-variant) !important;
            border-radius: 16px !important;
            font-family: var(--m3-font) !important;
        }

        html[data-ui-version="v1"] .input-time-modern:focus {
            border-color: var(--m3-primary) !important;
            box-shadow: 0 0 0 3px rgba(128, 222, 197, 0.15) !important;
        }

        html[data-ui-version="v1"] .input-time-modern::-webkit-calendar-picker-indicator {
            filter: invert(1) brightness(0.9);
            cursor: pointer;
        }

        html[data-ui-version="v1"] .smart-backup-header .title-blue {
            color: var(--m3-primary) !important;
        }

        html[data-ui-version="v1"] .smart-backup-desc {
            color: var(--m3-on-surface-variant) !important;
            font-family: var(--m3-font) !important;
        }

        html[data-ui-version="v1"] .smart-backup-control {
            background: var(--m3-surface-container-low) !important;
            border: 1px solid var(--m3-outline-variant) !important;
            border-radius: 20px !important;
            padding: 1.25rem 2rem !important;
        }

        html[data-ui-version="v1"] .smart-backup-input-wrapper {
            background: var(--m3-surface-container-highest) !important;
            border-color: var(--m3-outline-variant) !important;
            border-radius: 12px !important;
        }

        html[data-ui-version="v1"] .smart-backup-input-wrapper span {
            color: var(--m3-on-surface-variant) !important;
            font-weight: 600 !important;
        }

        html[data-ui-version="v1"] .status-dot.pagi {
            background-color: #a8c7ff !important;
            box-shadow: 0 0 10px rgba(168, 199, 255, 0.4) !important;
        }
        
        html[data-ui-version="v1"] .status-dot.siang {
            background-color: #f9d949 !important;
            box-shadow: 0 0 10px rgba(249, 217, 73, 0.4) !important;
        }

        html[data-ui-version="v1"] .status-dot.sore {
            background-color: var(--m3-tertiary) !important;
            box-shadow: 0 0 10px rgba(255, 182, 143, 0.4) !important;
        }

        html[data-ui-version="v1"] .status-dot.backup {
            background-color: var(--m3-primary) !important;
            box-shadow: 0 0 10px rgba(128, 222, 197, 0.4) !important;
        }
    </style>
</head>

<body>

    <div class="panel-layout">

        <!-- NAVBAR -->
        <header class="mobile-top-nav">
            <div class="mobile-logo">JAMKOT</div>
            <button class="btn-toggle-sidebar" id="sidebar-toggle">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="mobile-top-actions">
                @if(auth()->user()->canAccess('admin'))
                    @if(Route::is('settings.index'))
                    <a href="{{ route('panel') }}" class="btn-mobile-settings" title="Back to Panel">
                        <i class="fa-solid fa-house"></i>
                    </a>
                    @else
                    <a href="{{ route('settings.index') }}" class="btn-mobile-settings" title="Settings">
                        <i class="fa-solid fa-gear"></i>
                    </a>
                    @endif
                @endif
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-mobile-logout" title="Logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </header>

        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        <!-- SIDEBAR -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2>JAMKOT</h2>
            </div>

            <nav class="sidebar-nav">
                @if(auth()->user()->canAccess('admin'))
                <a href="{{ route('admin.users') }}" class="nav-link nav-link-admin {{ Route::is('admin.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users-gear"></i>
                    <span>Admin</span>
                </a>
                @endif
                @if(auth()->user()->canAccess('panel'))
                <a href="{{ route('panel') }}" class="nav-link {{ Route::is('panel') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge"></i>
                    <span>Panel Utama</span>
                </a>
                <a href="{{ route('sensor.dht22') }}" class="nav-link {{ Route::is('sensor.dht22') ? 'active' : '' }}">
                    <i class="fa-solid fa-temperature-half"></i>
                    <span>Sensor DHT22</span>
                </a>
                <a href="{{ route('sensor.ldr') }}" class="nav-link {{ Route::is('sensor.ldr') ? 'active' : '' }}">
                    <i class="fa-solid fa-sun"></i>
                    <span>Sensor LDR</span>
                </a>
                @endif
                @if(auth()->user()->canAccess('analisis'))
                <a href="{{ route('analisis') }}" class="nav-link {{ Route::is('analisis') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-simple"></i>
                    <span>Analisis</span>
                </a>
                @endif
                @if(auth()->user()->canAccess('schedule'))
                <a href="{{ route('schedule') }}" class="nav-link {{ Route::is('schedule') ? 'active' : '' }}">
                    <i class="fa-solid fa-clock"></i>
                    <span>Schedules</span>
                </a>
                @endif
                @if(auth()->user()->canAccess('settings'))
                <a href="{{ route('settings.index') }}" class="nav-link {{ Route::is('settings.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-gear"></i>
                    <span>Settings</span>
                </a>
                @endif
                @if(auth()->user()->canAccess('view3d'))
                <a href="{{ route('view3d') }}" class="nav-link {{ Route::is('view3d') ? 'active' : '' }}">
                    <i class="fa-solid fa-cube"></i>
                    <span>3D View</span>
                </a>
                @endif

            </nav>

            <div class="sidebar-footer">
                <span class="user-greeting">Halo, {{ auth()->user()->username ?? 'admin' }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout-sidebar" title="Logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="panel-content">
            <header class="content-header-flex">
                <div>
                    <h1>SCHEDULES</h1>
                    <p>Atur jadwal pompa air dan misting untuk menjaga kelembapan kumbung.</p>
                </div>
            </header>

            <form action="{{ route('schedule.store') }}" method="POST">
                @csrf

                <div class="summary-grid">
                    <!-- SESI PAGI -->
                    <div class="schedule-card">
                        <div class="card-header-flex">
                            <h3 class="card-title">Sesi Pagi</h3>
                            <div class="status-dot pagi"></div>
                        </div>
                        <div class="input-group">
                            <label>JAM MULAI</label>
                            <input type="time" name="jadwal_pagi_mulai" class="input-time-modern"
                                value="{{ $schedule->pagi_mulai ?? '08:00' }}">
                        </div>
                        <div class="input-group mt-1">
                            <label>JAM SELESAI</label>
                            <input type="time" name="jadwal_pagi_selesai" class="input-time-modern"
                                value="{{ $schedule->pagi_selesai ?? '08:05' }}">
                        </div>
                    </div>

                    <!-- SESI SIANG -->
                    <div class="schedule-card">
                        <div class="card-header-flex">
                            <h3 class="card-title">Sesi Siang</h3>
                            <div class="status-dot siang"></div>
                        </div>
                        <div class="input-group">
                            <label>JAM MULAI</label>
                            <input type="time" name="jadwal_siang_mulai" class="input-time-modern"
                                value="{{ $schedule->siang_mulai ?? '12:00' }}">
                        </div>
                        <div class="input-group mt-1">
                            <label>JAM SELESAI</label>
                            <input type="time" name="jadwal_siang_selesai" class="input-time-modern"
                                value="{{ $schedule->siang_selesai ?? '12:05' }}">
                        </div>
                    </div>

                    <!-- SESI SORE -->
                    <div class="schedule-card">
                        <div class="card-header-flex">
                            <h3 class="card-title">Sesi Sore</h3>
                            <div class="status-dot sore"></div>
                        </div>
                        <div class="input-group">
                            <label>JAM MULAI</label>
                            <input type="time" name="jadwal_sore_mulai" class="input-time-modern"
                                value="{{ $schedule->sore_mulai ?? '16:00' }}">
                        </div>
                        <div class="input-group mt-1">
                            <label>JAM SELESAI</label>
                            <input type="time" name="jadwal_sore_selesai" class="input-time-modern"
                                value="{{ $schedule->sore_selesai ?? '16:05' }}">
                        </div>
                    </div>
                </div>

                <!-- SMART-BACKUP -->
                <div class="schedule-card smart-backup-card">
                    <div class="smart-backup-info">
                        <div class="smart-backup-header">
                            <h3 class="card-title title-blue">Smart Backup</h3>
                            <div class="status-dot backup"></div>
                        </div>
                        <p class="smart-backup-desc">
                            Sistem cerdas: Pompa akan menyala otomatis jika kelembapan ruangan turun di bawah batas yang
                            ditentukan, meskipun di luar jadwal.
                        </p>
                    </div>

                    <div class="smart-backup-control">
                        <label>Batas Kelembapan Minimal:</label>
                        <div class="smart-backup-input-wrapper">
                            <input type="number" name="batas_kelembapan" class="input-time-modern"
                                value="{{ $schedule->batas_kelembapan ?? 80 }}">
                            <span>%</span>
                        </div>
                    </div>
                </div>

                <!-- SAVE -->
                <div class="action-row">
                    <button type="submit" class="btn-save">Simpan Konfigurasi</button>
                </div>
            </form>

        </main>
    </div>

    <!-- TOAST-NOTIFICATION -->
    @if(session('sukses'))
        <div id="toast-modern" class="toast-wrapper">
            <div class="toast-progress"></div>

            <div class="toast-body">
                <div class="toast-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>

                <div class="toast-text">
                    <h4>Success</h4>
                    <p>{{ session('sukses') }}</p>
                </div>

                <button class="toast-close" onclick="tutupToastModern()">×</button>
            </div>
        </div>
        <script src="{{ asset('js/toast.js') }}"></script>
    @endif
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <!-- BOTTOM NAV FOR MOBILE (M3 Only) -->
    <nav class="bottom-nav">
        @if(auth()->user()->canAccess('panel'))
        <a href="{{ route('panel') }}" class="bottom-nav-link {{ Route::is('panel') ? 'active' : '' }}">
            <div class="bottom-nav-icon-wrapper">
                <i class="fa-solid fa-gauge"></i>
            </div>
            <span>Panel</span>
        </a>
        @endif
        @if(auth()->user()->canAccess('analisis'))
        <a href="{{ route('analisis') }}" class="bottom-nav-link {{ Route::is('analisis') ? 'active' : '' }}">
            <div class="bottom-nav-icon-wrapper">
                <i class="fa-solid fa-chart-simple"></i>
            </div>
            <span>Analisis</span>
        </a>
        @endif
        @if(auth()->user()->canAccess('schedule'))
        <a href="{{ route('schedule') }}" class="bottom-nav-link {{ Route::is('schedule') ? 'active' : '' }}">
            <div class="bottom-nav-icon-wrapper">
                <i class="fa-solid fa-clock"></i>
            </div>
            <span>Schedule</span>
        </a>
        @endif
        @if(auth()->user()->canAccess('admin'))
        <a href="{{ route('admin.users') }}" class="bottom-nav-link {{ Route::is('admin.*') ? 'active' : '' }}">
            <div class="bottom-nav-icon-wrapper">
                <i class="fa-solid fa-users-gear"></i>
            </div>
            <span>Admin</span>
        </a>
        @else
        <a href="{{ route('settings.index') }}" class="bottom-nav-link {{ Route::is('settings.*') ? 'active' : '' }}">
            <div class="bottom-nav-icon-wrapper">
                <i class="fa-solid fa-gear"></i>
            </div>
            <span>Settings</span>
        </a>
        @endif
        @if(auth()->user()->canAccess('view3d'))
        <a href="{{ route('view3d') }}" class="bottom-nav-link {{ Route::is('view3d') ? 'active' : '' }}">
            <div class="bottom-nav-icon-wrapper">
                <i class="fa-solid fa-cube"></i>
            </div>
            <span>3D View</span>
        </a>
        @endif

    </nav>

</body>

</html>
