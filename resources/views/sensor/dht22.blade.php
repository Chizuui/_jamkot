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
    <title>DHT22 | JAMKOT</title>
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
                    <a href="{{ route('settings.index') }}" class="btn-mobile-settings" title="Settings">
                        <i class="fa-solid fa-gear"></i>
                    </a>
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
                    <h1>SENSOR DHT22</h1>
                    <p>Detail Suhu dan Kelembapan secara real-time.</p>
                </div>

                <!-- Jam -->
                <div class="datetime-widget">
                    <div id="realtime-clock" class="time-display">00:00:00</div>
                    <div id="realtime-date" class="date-display">Memuat...</div>
                </div>
            </header>

            <!-- Live Badge -->
            <div style="margin-bottom: 1.5rem;">
                <div class="glow-card" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem;">
                    <span class="status-dot online"></span>
                    <span style="font-weight: 600; color: #ededed;">LIVE</span>
                </div>
            </div>

            <!-- Kartu Nilai Sekarang -->
            <div class="summary-grid" style="margin-bottom: 2rem;">
                <div class="glow-card">
                    <div class="card-title">SUHU SEKARANG</div>
                    <div class="card-value" id="liveTemp">{{ number_format($latest->suhu ?? 0, 1) }}°C</div>
                    <div class="card-desc" id="liveTime">Terakhir update: {{ $latest ? $latest->created_at->format('H:i:s') : '--' }}</div>
                </div>
                <div class="glow-card">
                    <div class="card-title">KELEMBAPAN SEKARANG</div>
                    <div class="card-value" id="liveHum">{{ number_format($latest->kelembapan ?? 0, 1) }}%</div>
                    <div class="card-desc">RH Relative Humidity</div>
                </div>
            </div>

            <!-- Statistik -->
            <h3 class="section-title" style="margin-bottom: 1rem; font-size: 1.1rem; color: #9ca3af;">Statistik</h3>
            <div class="summary-grid" style="margin-bottom: 2rem; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
                <div class="glow-card">
                    <div class="card-title">SUHU RATA-RATA</div>
                    <div class="card-value" style="font-size: 1.5rem;" id="statAvgTemp">{{ $avgTemp }}°C</div>
                </div>
                <div class="glow-card">
                    <div class="card-title">SUHU TERTINGGI</div>
                    <div class="card-value" style="font-size: 1.5rem;" id="statMaxTemp">{{ $maxTemp }}°C</div>
                </div>
                <div class="glow-card">
                    <div class="card-title">KELEMBAPAN RATA-RATA</div>
                    <div class="card-value" style="font-size: 1.5rem;" id="statAvgHum">{{ $avgHumidity }}%</div>
                </div>
                <div class="glow-card">
                    <div class="card-title">KELEMBAPAN TERTINGGI</div>
                    <div class="card-value" style="font-size: 1.5rem;" id="statMaxHum">{{ $maxHumidity }}%</div>
                </div>
            </div>

            <!-- Grafik -->
            <div class="glow-card chart-wrapper" style="position: relative; min-height: 350px; margin-bottom: 2rem;">
                <div class="chart-header" style="padding: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div class="chart-title" style="font-size: 1.2rem; font-weight: 600; color: #ededed;">Suhu & Kelembapan</div>
                        <div class="chart-sub" style="font-size: 0.8rem; color: #737373;">30 data terakhir · auto-refresh setiap 5 detik</div>
                    </div>
                </div>
                <div id="dhtChart" style="padding: 0 1rem;"></div>
            </div>

            <!-- Tabel Log -->
            <div class="glow-card" style="padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h3 style="font-size: 1.1rem; font-weight: 600; color: #ededed;">Log Terbaru</h3>
                    <span style="font-size: 0.9rem; color: #737373;" id="record-count">{{ $recentLogs->count() }} entri</span>
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>WAKTU</th>
                                <th>DEVICE</th>
                                <th>SUHU</th>
                                <th>KELEMBAPAN</th>
                                <th>POMPA</th>
                                <th>STATUS</th>
                            </tr>
                        </thead>
                        <tbody id="logTbody">
                            @forelse($recentLogs as $log)
                            <tr>
                                <td class="text-muted">{{ $log->created_at->format('H:i:s') }}</td>
                                <td>{{ $log->sensor_id }}</td>
                                <td style="color: var(--warna-utama, #10b981);">{{ $log->suhu }}°C</td>
                                <td style="color: #22d3ee;">{{ $log->kelembapan }}%</td>
                                <td>
                                    <span class="fw-bold {{ $log->pompa_status == 'ON' ? 'text-blue' : 'text-muted' }}">
                                        {{ $log->pompa_status }}
                                    </span>
                                </td>
                                <td><span class="badge success">Normal</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-muted" style="text-align: center; padding: 2rem;">Belum ada data sensor</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <script src="{{ asset('js/clock.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script>
        (function () {
            'use strict';

            /* Data Awal */
            var labels = @json($chartLabels);
            var tempArr = @json($tempData);
            var humArr = @json($humidityData);

            /* ApexCharts */
            var chartOptions = {
                chart: {
                    type: 'line',
                    height: 320,
                    background: 'transparent',
                    toolbar: { show: false },
                    animations: { enabled: true, easing: 'easeinout', speed: 600 },
                    zoom: { enabled: false },
                },
                theme: { mode: 'dark' },
                series: [
                    { name: 'Suhu (°C)', data: tempArr },
                    { name: 'Humidity (%)', data: humArr },
                ],
                colors: ['#f97316', '#22d3ee'],
                xaxis: {
                    categories: labels,
                    labels: { style: { colors: '#737373', fontSize: '0.7rem' }, rotate: -30 },
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                },
                yaxis: [
                    { labels: { style: { colors: '#737373', fontSize: '0.7rem' } }, min: 0 },
                    { opposite: true, labels: { style: { colors: '#737373', fontSize: '0.7rem' } }, min: 0, max: 100 },
                ],
                stroke: { curve: 'smooth', width: [3, 3] },
                grid: { borderColor: 'rgba(255,255,255,0.05)', strokeDashArray: 4 },
                dataLabels: { enabled: false },
                tooltip: {
                    theme: 'dark',
                    y: [
                        { formatter: function (v) { return v + ' °C'; } },
                        { formatter: function (v) { return v + ' %'; } },
                    ],
                },
                markers: { size: 0, hover: { size: 5 } },
                legend: { labels: { colors: '#9ca3af' } },
            };

            var chart = new ApexCharts(document.getElementById('dhtChart'), chartOptions);
            chart.render();

            /* Polling */
            function refresh() {
                fetch('{{ route("api.sensor.dht22") }}')
                    .then(function (r) { return r.json(); })
                    .then(function (d) {
                        /* Live readings */
                        document.getElementById('liveTemp').textContent = d.latest.temperature + '°C';
                        document.getElementById('liveHum').textContent = d.latest.humidity + '%';
                        document.getElementById('liveTime').textContent = 'Terakhir update: ' + d.latest.timestamp;

                        /* Stat tiles */
                        document.getElementById('statAvgTemp').textContent = d.stats.avgTemp + '°C';
                        document.getElementById('statMaxTemp').textContent = d.stats.maxTemp + '°C';
                        document.getElementById('statAvgHum').textContent = d.stats.avgHumidity + '%';
                        document.getElementById('statMaxHum').textContent = d.stats.maxHumidity + '%';

                        /* Chart */
                        chart.updateOptions({ xaxis: { categories: d.labels } }, false, false);
                        chart.updateSeries([
                            { name: 'Suhu (°C)', data: d.temp },
                            { name: 'Humidity (%)', data: d.humidity },
                        ], false);

                        /* Table rows */
                        var tbody = document.getElementById('logTbody');
                        if (d.rows && d.rows.length) {
                            var html = '';
                            d.rows.forEach(function (r) {
                                var pumpHtml = r.pump === 'ON'
                                    ? '<span class="fw-bold text-blue">ON</span>'
                                    : '<span class="text-muted">OFF</span>';
                                html += '<tr>'
                                    + '<td class="text-muted">' + r.time + '</td>'
                                    + '<td>' + r.device + '</td>'
                                    + '<td style="color: var(--warna-utama, #10b981);">' + r.temp + '°C</td>'
                                    + '<td style="color: #22d3ee;">' + r.humidity + '%</td>'
                                    + '<td>' + pumpHtml + '</td>'
                                    + '<td><span class="badge success">Normal</span></td>'
                                    + '</tr>';
                            });
                            tbody.innerHTML = html;
                        }
                    })
                    .catch(function () { /* silent */ });
            }

            setInterval(refresh, 5000);
        })();
    </script>
</body>

</html>
