@extends('layouts.app')

@section('title', 'LDR | JAMKOT')
@section('page-title', 'SENSOR LDR')
@section('page-sub', 'Detail Intensitas Cahaya secara real-time.')

@section('styles')
<style>
    /* Custom styles for ldr page */
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
        <div class="card-title">CAHAYA SEKARANG</div>
        <div class="card-value" id="liveLight" style="font-size: 2rem; font-weight: 700;">{{ $latest->cahaya ?? '--' }} Lux</div>
        <div class="card-desc" id="liveTime">Terakhir update: {{ $latest ? $latest->created_at->format('H:i:s') : '--' }}</div>
    </div>
    <div class="glow-card" style="padding: 1.5rem;">
        <div class="card-title">TOTAL DATA</div>
        <div class="card-value" style="font-size: 2rem; font-weight: 700;" id="statTotal">{{ number_format($total) }}</div>
        <div class="card-desc">Entri tercatat</div>
    </div>
</div>

<!-- Statistik -->
<h3 class="section-title" style="margin-bottom: 1rem; font-size: 1.1rem; color: #9ca3af;">Statistik</h3>
<div class="summary-grid" style="margin-bottom: 2rem;">
    <div class="glow-card" style="padding: 1.25rem;">
        <div class="card-title">RATA-RATA</div>
        <div class="card-value" style="font-size: 1.5rem;" id="statAvg">{{ $avgLight }} Lux</div>
    </div>
    <div class="glow-card" style="padding: 1.25rem;">
        <div class="card-title">TERTINGGI</div>
        <div class="card-value" style="font-size: 1.5rem;" id="statMax">{{ $maxLight }} Lux</div>
    </div>
    <div class="glow-card" style="padding: 1.25rem;">
        <div class="card-title">TERENDAH</div>
        <div class="card-value" style="font-size: 1.5rem;" id="statMin">{{ $minLight }} Lux</div>
    </div>
</div>

<!-- Grafik -->
<div class="glow-card chart-wrapper" style="position: relative; min-height: 350px; margin-bottom: 2rem; padding: 1.5rem;">
    <div class="chart-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <div>
            <div class="chart-title" style="font-size: 1.2rem; font-weight: 600; color: #ededed;">Intensitas Cahaya</div>
            <div class="chart-sub" style="font-size: 0.8rem; color: #737373;">30 data terakhir · auto-refresh setiap 5 detik</div>
        </div>
    </div>
    <div id="ldrChart"></div>
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
                    <th>CAHAYA</th>
                    <th>POMPA</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody id="logTbody">
                @forelse($recentLogs as $log)
                <tr>
                    <td class="text-muted">{{ $log->created_at->format('H:i:s') }}</td>
                    <td>{{ $log->sensor_id }}</td>
                    <td style="color: #fbbf24;">{{ $log->cahaya }} Lux</td>
                    <td>
                        <span class="fw-bold {{ $log->pompa_status == 'ON' ? 'text-blue' : 'text-muted' }}" style="{{ $log->pompa_status == 'ON' ? 'color: #3b82f6;' : '' }}">
                            {{ $log->pompa_status }}
                        </span>
                    </td>
                    <td><span class="badge success" style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem;">Normal</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-muted" style="text-align: center; padding: 2rem;">Belum ada data sensor</td>
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
        var lightData = @json($lightData);

        /* ApexCharts */
        var chartOptions = {
            chart: {
                type: 'area',
                height: 320,
                background: 'transparent',
                toolbar: { show: false },
                animations: { enabled: true, easing: 'easeinout', speed: 600 },
                zoom: { enabled: false },
            },
            theme: { mode: 'dark' },
            series: [
                { name: 'Cahaya (Lux)', data: lightData },
            ],
            colors: ['#fbbf24'],
            xaxis: {
                categories: labels,
                labels: { style: { colors: '#737373', fontSize: '0.7rem' }, rotate: -30 },
                axisBorder: { show: false },
                axisTicks: { show: false },
            },
            yaxis: {
                labels: { style: { colors: '#737373', fontSize: '0.7rem' } },
                min: 0,
            },
            stroke: { curve: 'smooth', width: 2 },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.3,
                    opacityTo: 0.01,
                    stops: [0, 100],
                    colorStops: [
                        { offset: 0, color: '#fbbf24', opacity: 0.3 },
                        { offset: 100, color: '#fbbf24', opacity: 0 },
                    ],
                },
            },
            grid: { borderColor: 'rgba(255,255,255,0.05)', strokeDashArray: 4 },
            dataLabels: { enabled: false },
            tooltip: {
                theme: 'dark',
                y: { formatter: function (v) { return v + ' Lux'; } },
            },
            markers: { size: 0, hover: { size: 5 } },
        };

        var chart = new ApexCharts(document.getElementById('ldrChart'), chartOptions);
        chart.render();

        /* Polling */
        function refresh() {
            fetch('{{ route("api.sensor.ldr") }}')
                .then(function (r) { return r.json(); })
                .then(function (d) {
                    /* Live readings */
                    document.getElementById('liveLight').textContent = d.latest.light_intensity + ' Lux';
                    document.getElementById('liveTime').textContent = 'Terakhir update: ' + d.latest.timestamp;

                    /* Stat tiles */
                    document.getElementById('statAvg').textContent = d.stats.avg + ' Lux';
                    document.getElementById('statMax').textContent = d.stats.max + ' Lux';
                    document.getElementById('statMin').textContent = d.stats.min + ' Lux';
                    document.getElementById('statTotal').textContent = d.stats.total.toLocaleString();

                    /* Chart */
                    chart.updateOptions({ xaxis: { categories: d.labels.map(l => l.substring(0, 5)) } }, false, false);
                    chart.updateSeries([{ name: 'Cahaya (Lux)', data: d.light }], false);

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
                                + '<td style="color: #fbbf24;">' + r.light + ' Lux</td>'
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
