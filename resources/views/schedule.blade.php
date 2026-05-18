@extends('layouts.app')

@section('title', 'Schedules')
@section('page-title', 'SCHEDULES')
@section('page-sub', 'Atur jadwal pompa air dan misting untuk menjaga kelembapan kumbung.')

@section('styles')
<style>
    /* Custom styles for schedule page */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .schedule-card {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.05);
        border-radius: 16px;
        padding: 1.5rem;
        transition: all 0.3s ease;
    }
    .schedule-card:hover {
        background: rgba(255,255,255,0.05);
        border-color: rgba(255,255,255,0.1);
    }
    .card-header-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    .card-title { font-size: 1.1rem; font-weight: 600; margin: 0; }
    .status-dot { width: 8px; height: 8px; border-radius: 50%; }
    .status-dot.pagi { background: #fbbf24; box-shadow: 0 0 10px #fbbf24; }
    .status-dot.siang { background: #f97316; box-shadow: 0 0 10px #f97316; }
    .status-dot.sore { background: #38bdf8; box-shadow: 0 0 10px #38bdf8; }
    .status-dot.backup { background: #10b981; box-shadow: 0 0 10px #10b981; }
    
    .input-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .input-group label { font-size: 0.75rem; color: #9ca3af; text-transform: uppercase; }
    .input-time-modern {
        background: #111;
        border: 1px solid #262626;
        color: #ededed;
        padding: 0.75rem;
        border-radius: 8px;
        font-family: inherit;
        font-size: 1rem;
        outline: none;
        transition: all 0.3s ease;
    }
    .input-time-modern:focus {
        border-color: var(--warna-utama, #10b981);
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }
    
    .smart-backup-card {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 2rem;
        margin-bottom: 2rem;
        background: rgba(16, 185, 129, 0.05);
        border-color: rgba(16, 185, 129, 0.2);
    }
    .smart-backup-info { flex: 1; }
    .smart-backup-header { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; }
    .title-blue { color: #10b981; }
    .smart-backup-desc { font-size: 0.85rem; color: #9ca3af; margin: 0; }
    
    .smart-backup-control {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        min-width: 200px;
    }
    .smart-backup-control label { font-size: 0.75rem; color: #9ca3af; }
    .smart-backup-input-wrapper { display: flex; align-items: center; gap: 0.5rem; }
    .smart-backup-input-wrapper .input-time-modern { width: 100px; text-align: center; }
    
    .action-row { display: flex; justify-content: flex-end; }
    .btn-save {
        background: var(--warna-utama, #10b981);
        color: #000;
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 8px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.25);
    }
    
    /* Toast */
    .toast-wrapper {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        background: #111;
        border: 1px solid #262626;
        border-radius: 12px;
        padding: 1rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        z-index: 1000;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        min-width: 300px;
    }
    .toast-body { display: flex; align-items: center; gap: 1rem; }
    .toast-icon { width: 24px; height: 24px; background: rgba(16, 185, 129, 0.1); color: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
    .toast-text h4 { margin: 0; font-size: 0.9rem; }
    .toast-text p { margin: 0; font-size: 0.8rem; color: #9ca3af; }
    .toast-close { background: none; border: none; color: #9ca3af; cursor: pointer; font-size: 1.25rem; margin-left: auto; }
</style>
@endsection

@section('content')
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
            <div class="input-group" style="margin-top: 1rem;">
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
            <div class="input-group" style="margin-top: 1rem;">
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
            <div class="input-group" style="margin-top: 1rem;">
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

<!-- TOAST-NOTIFICATION -->
@if(session('sukses'))
    <div id="toast-modern" class="toast-wrapper">
        <div class="toast-body">
            <div class="toast-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                    stroke-linejoin="round" width="14" height="14">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>

            <div class="toast-text">
                <h4>Success</h4>
                <p>{{ session('sukses') }}</p>
            </div>

            <button class="toast-close" onclick="this.parentElement.parentElement.style.display='none'">×</button>
        </div>
    </div>
@endif
@endsection
