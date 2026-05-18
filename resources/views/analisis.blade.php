@extends('layouts.app')

@section('title', 'Analisis Data')
@section('page-title', 'ANALISIS DATA')
@section('page-sub', 'Ringkasan akumulasi dan statistik performa sistem JAMKOT.')

@section('styles')
<style>
    /* Custom styles for analisis page */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .analysis-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .record-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-top: 1rem;
    }
    .record-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    .record-item span { font-size: 0.75rem; color: #9ca3af; }
    .record-item strong { font-size: 1.25rem; color: #ededed; }
</style>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
@endsection

@section('content')
<!-- STATISTIK UTAMA -->
<div class="summary-grid">
    <div class="glow-card stat-card meter-card meter-card-temperature" style="--meter-angle: {{ min(max(($stats['avg_suhu'] ?? 0) / 40, 0), 1) * 180 }}deg; padding: 1.5rem;">
        <div class="card-title">RATA-RATA SUHU</div>
        <div class="card-value" style="font-size: 2rem; font-weight: 700;">{{ number_format($stats['avg_suhu'], 1) }}°C</div>
        <div class="card-desc">Dari seluruh rekaman data</div>
    </div>

    <div class="glow-card stat-card meter-card meter-card-humidity" style="--meter-angle: {{ min(max(($stats['avg_kelembapan'] ?? 0) / 100, 0), 1) * 180 }}deg; padding: 1.5rem;">
        <div class="card-title">RATA-RATA KELEMBAPAN</div>
        <div class="card-value" style="font-size: 2rem; font-weight: 700;">{{ number_format($stats['avg_kelembapan'], 1) }}%</div>
        <div class="card-desc">Target ideal: 85%</div>
    </div>

    <div class="glow-card stat-card total-log-card" style="padding: 1.5rem;">
        <div class="card-title">TOTAL LOG SISTEM</div>
        <div class="total-log-content" style="display: flex; align-items: center; gap: 1rem; margin-top: 0.5rem;">
            <span class="material-symbols-rounded" style="font-size: 2.5rem; color: var(--m3-primary, #80dec5);">database</span>
            <div>
                <div class="card-value" style="font-size: 2rem; font-weight: 700;">{{ $stats['total_data'] }}</div>
                <div class="card-desc">Database: MySQL</div>
            </div>
        </div>
    </div>
</div>

<!-- DETAIL RECORD -->
<div class="analysis-row">
    <div class="glow-card record-card high" style="padding: 1.5rem;">
        <div class="record-header" style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
            <span class="material-symbols-rounded" style="color: #ef4444;">arrow_upward</span>
            <h3 class="section-title" style="margin: 0; font-size: 1.1rem;">Rekor Tertinggi</h3>
        </div>
        <div class="record-grid">
            <div class="record-item">
                <span>Suhu</span>
                <strong>{{ is_null($stats['max_suhu']) ? '--' : $stats['max_suhu'] . '°C' }}</strong>
            </div>
            <div class="record-item">
                <span>Kelembapan</span>
                <strong>{{ is_null($stats['max_kelembapan']) ? '--' : $stats['max_kelembapan'] . '%' }}</strong>
            </div>
        </div>
    </div>

    <div class="glow-card record-card low" style="padding: 1.5rem;">
        <div class="record-header" style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
            <span class="material-symbols-rounded" style="color: #3b82f6;">arrow_downward</span>
            <h3 class="section-title" style="margin: 0; font-size: 1.1rem;">Rekor Terendah</h3>
        </div>
        <div class="record-grid">
            <div class="record-item">
                <span>Suhu</span>
                <strong>{{ is_null($stats['min_suhu']) ? '--' : $stats['min_suhu'] . '°C' }}</strong>
            </div>
            <div class="record-item">
                <span>Kelembapan</span>
                <strong>{{ is_null($stats['min_kelembapan']) ? '--' : $stats['min_kelembapan'] . '%' }}</strong>
            </div>
        </div>
    </div>
</div>

<!-- FILTER & HISTORI LOG -->
<div class="glow-card filter-card" style="margin-top: 2rem; padding: 1.75rem;">
    <form action="{{ route('analisis') }}" method="GET" class="filter-form" style="display: flex; gap: 1.5rem; align-items: flex-end; flex-wrap: wrap;">
        <div class="filter-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
            <label style="font-size: 0.75rem; color: #9ca3af; text-transform: uppercase;">Filter Tanggal</label>
            <input type="date" name="date" value="{{ $date }}" class="filter-input" style="background: #111; border: 1px solid #262626; color: #ededed; padding: 0.5rem; border-radius: 8px;">
        </div>
        <div class="filter-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
            <label style="font-size: 0.75rem; color: #9ca3af; text-transform: uppercase;">Jumlah Data</label>
            <select name="limit" class="filter-input" style="background: #111; border: 1px solid #262626; color: #ededed; padding: 0.5rem; border-radius: 8px;">
                <option value="10" {{ $limit == 10 ? 'selected' : '' }}>10 Data</option>
                <option value="20" {{ $limit == 20 ? 'selected' : '' }}>20 Data</option>
                <option value="50" {{ $limit == 50 ? 'selected' : '' }}>50 Data</option>
                <option value="100" {{ $limit == 100 ? 'selected' : '' }}>100 Data</option>
            </select>
        </div>
        <div class="filter-actions" style="display: flex; gap: 0.5rem;">
            <button type="submit" class="btn-filter" style="background: var(--warna-utama, #10b981); color: #000; border: none; padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; font-weight: 600;">Tampilkan Data</button>
            @if($date || $limit != 10)
                <a href="{{ route('analisis') }}" class="btn-reset" style="color: #9ca3af; text-decoration: none; padding: 0.5rem; display: flex; align-items: center;">Reset</a>
            @endif
        </div>
    </form>
</div>

<div class="glow-card table-wrapper" style="margin-top: 1.5rem; margin-bottom: 3rem; padding: 1.5rem;">
    <div class="table-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <div>
            <h3 class="section-title" style="margin: 0; font-size: 1.1rem;">Histori Log Sensor</h3>
            @if($date)
                <span class="badge info" style="background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); margin-top: 0.5rem; display: inline-block; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem;">
                    Data: {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                </span>
            @endif
        </div>
        <div class="export-actions" style="display: flex; gap: 0.5rem;">
            <a href="{{ route('analisis.export.csv', ['date' => $date]) }}" class="btn-export csv" style="display: flex; align-items: center; gap: 0.25rem; padding: 0.5rem; border-radius: 4px; text-decoration: none; font-size: 0.8rem; background: rgba(16, 185, 129, 0.1); color: #10b981;">
                <i class="fa-solid fa-file-csv"></i> CSV
            </a>
            <a href="{{ route('analisis.export.pdf', ['date' => $date]) }}" target="_blank" class="btn-export pdf" style="display: flex; align-items: center; gap: 0.25rem; padding: 0.5rem; border-radius: 4px; text-decoration: none; font-size: 0.8rem; background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                <i class="fa-solid fa-file-pdf"></i> PDF
            </a>
        </div>
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
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td class="text-muted">
                            <span style="color: #ededed;">{{ $log->created_at->format('H:i:s') }}</span> 
                            <small style="font-size: 0.7rem; margin-left: 0.5rem; opacity: 0.6;">{{ $log->created_at->diffForHumans() }}</small>
                        </td>
                        <td>{{ $log->sensor_id }}</td>
                        <td><span class="badge success" style="background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem;">Tercatat</span></td>
                        <td>
                            <span class="fw-bold {{ $log->pompa_status == 'ON' ? 'text-blue' : 'text-muted' }}" style="{{ $log->pompa_status == 'ON' ? 'color: #3b82f6;' : '' }}">
                                {{ $log->pompa_status }}
                            </span>
                        </td>
                        <td class="text-right fw-bold" style="letter-spacing: 0.05em;">{{ $log->kelembapan }}% | {{ $log->suhu }}°C</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-muted" style="text-align: center; padding: 4rem 2rem;">
                            <i class="fa-solid fa-folder-open" style="display: block; font-size: 2rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                            Tidak ada data ditemukan untuk filter yang dipilih.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
