<!DOCTYPE html>
<html lang="en">

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
    <title>PANEL | JAMKOT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/material3.css') }}">
    @vite('resources/js/app.js')
</head>

<body>

    <div class="panel-layout">

        <!-- Navigasi -->
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

        <!-- Konten -->
        <main class="panel-content">

            <header class="content-header-flex">
                <div>
                    <h1>PANEL UTAMA</h1>
                    <p>Pantau status perangkat dan indikator lingkungan secara real-time.</p>
                </div>

                <!-- Jam -->
                <div class="datetime-widget">
                    <div id="realtime-clock" class="time-display">00:00:00</div>
                    <div id="realtime-date" class="date-display">Memuat...</div>
                </div>
            </header>

            <!-- SECTION 1: KONDISI LINGKUNGAN (Law of Proximity) -->
            <h3 class="section-title" style="margin-top: 1rem; margin-bottom: 1rem; font-size: 1.1rem; color: #9ca3af;">Indikator Lingkungan</h3>
            <div class="summary-grid" style="margin-bottom: 2rem;">
                <div class="glow-card" id="card-cahaya">
                    <div class="card-title-wrapper">
                        <div class="card-title">INTENSITAS CAHAYA</div>
                        <div class="status-dot status-cahaya {{ $latest ? 'online' : 'offline' }}"></div>
                    </div>
                    <div class="card-value" id="val-cahaya">{{ $latest->cahaya ?? '--' }} Lux</div>
                    <div class="card-desc">Sensor LDR</div>
                </div>
                <div class="glow-card sensor-meter-card sensor-meter-temperature" id="card-suhu" style="--meter-angle: {{ min(max(($latest->suhu ?? 0) / 40, 0), 1) * 180 }}deg;">
                    <div class="card-title">SUHU RUANG</div>
                    <div class="status-dot status-suhu {{ $latest ? 'online' : 'offline' }}"></div>
                    <div class="card-value" id="val-suhu">{{ number_format($latest->suhu ?? 0, 1) }}°C</div>
                    <div class="card-desc">Target: 22°C - 28°C</div>
                </div>

                <div class="glow-card sensor-meter-card sensor-meter-humidity" id="card-kelembapan" style="--meter-angle: {{ min(max(($latest->kelembapan ?? 0) / 100, 0), 1) * 180 }}deg;">
                    <div class="card-title">KELEMBAPAN UDARA</div>
                    <div class="status-dot status-kelembapan {{ $latest ? 'online' : 'offline' }}"></div>
                    <div class="card-value" id="val-kelembapan">{{ number_format($latest->kelembapan ?? 0, 1) }}%</div>
                    <div class="card-desc" id="desc-kelembapan" class="{{ ($latest->kelembapan ?? 0) >= $targetKelembapan ? 'text-positive' : '' }}">
                        Target Minimal: <span id="val-target-kelembapan">{{ $targetKelembapan }}</span>%
                    </div>
                </div>
            </div>

            <!-- SECTION 2: KONTROL AKTUATOR (Law of Proximity & Doherty Threshold) -->
            <h3 class="section-title" style="margin-bottom: 1rem; font-size: 1.1rem; color: #9ca3af;">Kontrol Aktuator</h3>
            <div class="summary-grid" style="margin-bottom: 2.5rem; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
                <div class="glow-card actuator-card" style="display: flex; justify-content: space-between; align-items: center; background: rgba(16, 185, 129, 0.05); border: 1px solid rgba(16, 185, 129, 0.2); padding: 1.5rem;">
                    <div>
                        <div class="card-title" style="color: var(--warna-utama, #10b981); margin-bottom: 0.25rem;">POMPA AIR (MISTING)</div>
                        <div class="card-desc" style="margin: 0;">Kontrol manual kelembapan ruang.</div>
                        <div id="pump-status-text" style="font-weight: 600; margin-top: 0.75rem; color: #ededed; display: flex; align-items: center; gap: 0.5rem;">
                            <span class="status-dot offline" id="pump-indicator-dot"></span> <span id="pump-state-label">OFF</span>
                        </div>
                    </div>
                    <div>
                        <button id="btn-toggle-pump" style="padding: 1rem 1.5rem; border-radius: 100px; background: var(--warna-utama, #10b981); color: #111; font-weight: bold; border: none; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; transition: all 0.2s; min-height: 48px;" onclick="togglePumpOptimistic()">
                            <i class="fa-solid fa-power-off"></i> <span id="pump-btn-text">NYALAKAN</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Grafik -->
            <div class="glow-card chart-wrapper" style="position: relative; min-height: 350px;">
                <h3 class="section-title">Tren Suhu & Kelembapan</h3>
                
                <!-- Skeleton Loader UI -->
                <div id="chart-skeleton" style="position: absolute; top: 60px; left: 1.5rem; right: 1.5rem; bottom: 1.5rem; background: linear-gradient(90deg, rgba(255,255,255,0.03) 25%, rgba(255,255,255,0.08) 50%, rgba(255,255,255,0.03) 75%); background-size: 200% 100%; animation: skeletonLoading 1.5s infinite; border-radius: 8px; z-index: 10;">
                    <style>
                        @keyframes skeletonLoading {
                            0% { background-position: 200% 0; }
                            100% { background-position: -200% 0; }
                        }
                    </style>
                </div>

                <div id="chart-jamkot" style="opacity: 0; transition: opacity 0.5s ease;"></div>
            </div>

            <!-- Log Sensor -->
            <div class="glow-card table-wrapper" id="panel-log-card">
                <div class="table-header">
                    <h3 class="section-title" style="margin: 0;">Log Sensor Terakhir</h3>
                </div>

                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>WAKTU</th>
                                <th>ID DEVICE</th>
                                <th>STATUS</th>
                                <th>POMPA</th>
                                <th class="text-right">NILAI (H | T)</th>
                            </tr>
                        </thead>
                        <tbody id="table-body-log">
                            @forelse($riwayatTabel as $log)
                                <tr>
                                    <td class="text-muted">{{ $log->created_at->diffForHumans() }}</td>
                                    <td>{{ $log->sensor_id }}</td>
                                    <td><span class="badge success">Tercatat</span></td>
                                    <td>
                                        <span class="fw-bold {{ $log->pompa_status == 'ON' ? 'text-blue' : 'text-muted' }}">
                                            {{ $log->pompa_status }}
                                        </span>
                                    </td>
                                    <td class="text-right">{{ $log->kelembapan }}% | {{ $log->suhu }}°C</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-muted" style="text-align: center; padding: 2rem;">Belum ada data
                                        sensor masuk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <style>
                /* --- Tabel Responsif --- */
                .table-responsive {
                    display: block !important;
                    width: 100% !important;
                    overflow-x: auto !important;
                    overflow-y: hidden !important;
                    -webkit-overflow-scrolling: touch !important;
                    position: relative !important;
                    padding-bottom: 10px !important;
                }

                .data-table {
                    width: 100% !important;
                    min-width: 800px !important;
                    border-collapse: collapse !important;
                    table-layout: auto !important;
                }

                .data-table th, .data-table td {
                    white-space: nowrap !important;
                    padding: 1.25rem 1.5rem !important;
                    text-align: left !important;
                }

                @media (max-width: 768px) {
                    #panel-log-card {
                        padding: 1.5rem 0 !important;
                        overflow: hidden !important;
                    }
                    
                    .table-header {
                        padding: 0 1.5rem 1rem 1.5rem !important;
                    }

                    .table-responsive {
                        padding: 0 1.5rem !important;
                    }

                    .table-responsive::-webkit-scrollbar {
                        height: 8px !important;
                        display: block !important;
                    }
                    .table-responsive::-webkit-scrollbar-thumb {
                        background: var(--warna-utama, #10b981) !important;
                        border-radius: 10px !important;
                    }
                    .table-responsive::-webkit-scrollbar-track {
                        background: rgba(255,255,255,0.05) !important;
                    }
                }
            </style>

        </main>
    </div>

    <script src="{{ asset('js/clock.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        window.dataJamkot = @json($riwayatGrafik);
        window.currentSuhu = {{ $latest->suhu ?? 0 }};
        window.currentKelembapan = {{ $latest->kelembapan ?? 0 }};
    </script>
    <script src="{{ asset('js/chart.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/sidebar.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/realtime.js') }}?v={{ time() }}"></script>
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
