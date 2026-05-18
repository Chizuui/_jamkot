@extends('layouts.app')

@section('title', 'Panel Utama')
@section('page-title', 'PANEL UTAMA')
@section('page-sub', 'Pantau status perangkat dan indikator lingkungan secara real-time.')

@section('styles')
<style>
    /* Custom styles for panel page if needed */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
    }
</style>
@endsection

@section('content')
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
<div class="glow-card chart-wrapper" style="position: relative; min-height: 350px; margin-bottom: 2rem;">
    <h3 class="section-title">Tren Suhu & Kelembapan</h3>
    
    <!-- Skeleton Loader UI -->
    <div id="chart-skeleton" style="position: absolute; top: 60px; left: 1.5rem; right: 1.5rem; bottom: 1.5rem; background: linear-gradient(90deg, rgba(255,255,255,0.03) 25%, rgba(255,255,255,0.08) 50%, rgba(255,255,255,0.03) 75%); background-size: 200% 100%; animation: skeletonLoading 1.5s infinite; border-radius: 8px; z-index: 10;">
    </div>

    <div id="chart-jamkot" style="opacity: 0; transition: opacity 0.5s ease;"></div>
</div>

<!-- Log Sensor -->
<div class="glow-card table-wrapper" id="panel-log-card" style="padding: 1.5rem;">
    <div class="table-header" style="margin-bottom: 1rem;">
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
                        <td colspan="5" class="text-muted" style="text-align: center; padding: 2rem;">Belum ada data sensor masuk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    window.dataJamkot = @json($riwayatGrafik);
    window.currentSuhu = {{ $latest->suhu ?? 0 }};
    window.currentKelembapan = {{ $latest->kelembapan ?? 0 }};
</script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="{{ asset('js/chart.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/realtime.js') }}?v={{ time() }}"></script>
<script>
    function togglePumpOptimistic() {
        // Fungsi toggle pump yang ada di panel.blade.php asli
        // Pastikan fungsi ini atau panggilannya ada di app.js atau inline di sini jika diperlukan
        if (typeof window.togglePump === 'function') {
            window.togglePump();
        } else {
            console.error('togglePump function not found');
        }
    }
</script>
@endsection
