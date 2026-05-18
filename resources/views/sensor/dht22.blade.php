@extends('layouts.app')

@section('title', 'DHT22 | JAMKOT')
@section('page-title', 'SENSOR DHT22')
@section('page-sub', 'Detail Suhu dan Kelembapan secara real-time.')

@section('styles')
<style>
    /* Custom styles for dht22 page */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    /* Mobile Sensor Select */
    .mobile-sensor-select {
        display: none;
    }
    
    @media (max-width: 768px) {
        .mobile-sensor-select {
            display: inline-block !important;
            background: var(--m3-surface-container, #1b221f);
            color: #ededed;
            border: 1px solid var(--m3-primary, #80dec5);
            border-radius: 12px;
            padding: 0.5rem 2.5rem 0.5rem 1rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='10' height='10' fill='%2380dec5'><polygon points='0,0 10,0 5,5'/></svg>");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            box-shadow: 0 0 10px rgba(128, 222, 197, 0.2);
            transition: all 0.3s ease;
        }
        .mobile-sensor-select:focus {
            outline: none;
            box-shadow: 0 0 15px rgba(128, 222, 197, 0.4);
        }
        
        /* Neon Glow Theme Adaptation */
        html[data-ui-version="v2"] .mobile-sensor-select {
            border-color: #10b981;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='10' height='10' fill='%2310b981'><polygon points='0,0 10,0 5,5'/></svg>");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.2);
        }
        html[data-ui-version="v2"] .mobile-sensor-select:focus {
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.4);
        }
    }
</style>
@endsection

@section('content')
<!-- Live Badge -->
<div style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem;">
    <div class="glow-card" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem;">
        <span class="status-dot online" style="background: #10b981; width: 8px; height: 8px; border-radius: 50%; display: inline-block;"></span>
        <span style="font-weight: 600; color: #ededed;">LIVE</span>
    </div>
    
    <!-- Dropdown for switching sensors on mobile -->
    <select class="mobile-sensor-select" onchange="window.location.href=this.value">
        <option value="{{ route('sensor.ldr') }}" {{ request()->routeIs('sensor.ldr') ? 'selected' : '' }}>Sensor LDR</option>
        <option value="{{ route('sensor.dht22') }}" {{ request()->routeIs('sensor.dht22') ? 'selected' : '' }}>Sensor DHT22</option>
    </select>
</div>

<!-- Kartu Nilai Sekarang -->
<div class="summary-grid" style="margin-bottom: 2rem; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));">
    <div class="glow-card" style="padding: 1.5rem;">
        <div class="card-title">SUHU SEKARANG</div>
        <div class="card-value" id="liveTemp" style="font-size: 2rem; font-weight: 700;">{{ number_format($latest->suhu ?? 0, 1) }}°C</div>
        <div class="card-desc" id="liveTime">Terakhir update: {{ $latest ? $latest->created_at->format('H:i:s') : '--' }}</div>
    </div>
    <div class="glow-card" style="padding: 1.5rem;">
        <div class="card-title">KELEMBAPAN SEKARANG</div>
        <div class="card-value" id="liveHum" style="font-size: 2rem; font-weight: 700;">{{ number_format($latest->kelembapan ?? 0, 1) }}%</div>
        <div class="card-desc">RH Relative Humidity</div>
    </div>
</div>

<!-- Statistik -->
<h3 class="section-title" style="margin-bottom: 1rem; font-size: 1.1rem; color: #9ca3af;">Statistik</h3>
<div class="summary-grid" style="margin-bottom: 2rem;">
    <div class="glow-card" style="padding: 1.25rem;">
        <div class="card-title">SUHU RATA-RATA</div>
        <div class="card-value" style="font-size: 1.5rem;" id="statAvgTemp">{{ $avgTemp }}°C</div>
    </div>
    <div class="glow-card" style="padding: 1.25rem;">
        <div class="card-title">SUHU TERTINGGI</div>
        <div class="card-value" style="font-size: 1.5rem;" id="statMaxTemp">{{ $maxTemp }}°C</div>
    </div>
    <div class="glow-card" style="padding: 1.25rem;">
        <div class="card-title">KELEMBAPAN RATA-RATA</div>
        <div class="card-value" style="font-size: 1.5rem;" id="statAvgHum">{{ $avgHumidity }}%</div>
    </div>
    <div class="glow-card" style="padding: 1.25rem;">
        <div class="card-title">KELEMBAPAN TERTINGGI</div>
        <div class="card-value" style="font-size: 1.5rem;" id="statMaxHum">{{ $maxHumidity }}%</div>
    </div>
</div>

<!-- Grafik -->
<div class="glow-card chart-wrapper" style="position: relative; min-height: 350px; margin-bottom: 2rem; padding: 1.5rem;">
    <div class="chart-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <div>
            <div class="chart-title" style="font-size: 1.2rem; font-weight: 600; color: #ededed;">Suhu & Kelembapan</div>
            <div class="chart-sub" style="font-size: 0.8rem; color: #737373;">30 data terakhir · auto-refresh setiap 5 detik</div>
        </div>
    </div>
    <div id="dhtChart"></div>
</div>

<!-- Tabel Log -->
<div class="glow-card" style="padding: 1.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 style="font-size: 1.1rem; font-weight: 600; color: #ededed; margin: 0;">Log Terbaru</h3>
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
                        <span class="fw-bold {{ $log->pompa_status == 'ON' ? 'text-blue' : 'text-muted' }}" style="{{ $log->pompa_status == 'ON' ? 'color: #3b82f6;' : '' }}">
                            {{ $log->pompa_status }}
                        </span>
                    </td>
                    <td><span class="badge success" style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem;">Normal</span></td>
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
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    (function () {
        'use strict';

        /* Data Awal */
        var labels = @json($chartLabels).map(l => l.substring(0, 5));
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
                    chart.updateOptions({ xaxis: { categories: d.labels.map(l => l.substring(0, 5)) } }, false, false);
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
                                ? '<span class="fw-bold text-blue" style="color: #3b82f6;">ON</span>'
                                : '<span class="text-muted">OFF</span>';
                            html += '<tr>'
                                + '<td class="text-muted">' + r.time + '</td>'
                                + '<td>' + r.device + '</td>'
                                + '<td style="color: var(--warna-utama, #10b981);">' + r.temp + '°C</td>'
                                + '<td style="color: #22d3ee;">' + r.humidity + '%</td>'
                                + '<td>' + pumpHtml + '</td>'
                                + '<td><span class="badge success" style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem;">Normal</span></td>'
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
@endsection
