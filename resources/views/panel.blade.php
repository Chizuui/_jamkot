<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PANEL | JAMKOT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    @vite('resources/js/app.js')
</head>

<body>

    <div class="panel-layout">

        <!-- NAVBAR -->
        <header class="mobile-top-nav">
            <div class="mobile-logo">JAMKOT</div>
            <button class="btn-toggle-sidebar" id="sidebar-toggle">
                <i class="fa-solid fa-bars"></i>
            </button>
        </header>

        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        <!-- SIDEBAR -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2>JAMKOT</h2>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('panel') }}" class="nav-link {{ Route::is('panel') ? 'active' : '' }}">
                    Panel Utama
                </a>
                <a href="{{ route('analisis') }}" class="nav-link {{ Route::is('analisis') ? 'active' : '' }}">
                    Analisis
                </a>
                <a href="{{ route('schedule') }}" class="nav-link {{ Route::is('schedule') ? 'active' : '' }}">
                    Schedules
                </a>
                <a href="{{ route('settings.index') }}" class="nav-link {{ Route::is('settings.*') ? 'active' : '' }}">
                    Settings
                </a>
            </nav>

            <div class="sidebar-footer">
                <div class="theme-switch-wrapper">
                    <label class="theme-switch" for="theme-toggle">
                        <input type="checkbox" id="theme-toggle" />
                        <div class="slider round">
                            <i class="fa-solid fa-sun slider-icon-sun"></i>
                            <i class="fa-solid fa-moon slider-icon-moon"></i>
                        </div>
                    </label>
                </div>
                <span class="user-greeting">Halo, {{ auth()->user()->username ?? 'admin' }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout-sidebar">
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- MAIN-CONTENT -->
        <main class="panel-content">

            <header class="content-header-flex">
                <div>
                    <h1>PANEL UTAMA</h1>
                    <p>Pantau status perangkat dan indikator lingkungan secara real-time.</p>
                </div>

                <!-- JAM -->
                <div class="datetime-widget">
                    <div id="realtime-clock" class="time-display">00:00:00</div>
                    <div id="realtime-date" class="date-display">Memuat...</div>
                </div>
            </header>

            <!-- DATA-REALTIME -->
            <div class="summary-grid">
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

            <!-- APEXCHART -->
            <div class="glow-card chart-wrapper">
                <h3 class="section-title">Tren Suhu & Kelembapan</h3>
                <div id="chart-jamkot"></div>
            </div>

            <!-- LOG-SENSOR -->
            <div class="glow-card table-wrapper">
                <div class="table-header">
                    <h3 class="section-title" style="margin: 0;">Log Sensor Terakhir</h3>
                    <a href="{{ route('panel.export') }}" class="btn-sm"
                        style="text-decoration: none; display: inline-block;">Unduh Laporan</a>
                </div>

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
    <script src="{{ asset('js/theme.js') }}"></script>

</body>

</html>
