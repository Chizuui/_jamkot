@extends('layouts.app')

@section('title', 'Pengaturan')
@section('page-title', 'PENGATURAN')
@section('page-sub', 'Manajemen data dan sistem JAMKOT.')

@section('styles')
<style>
    /* Custom styles for settings page */
    .settings-container {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }
    .settings-card {
        padding: 2rem;
    }
    .danger-zone {
        border: 1px solid #ef4444;
        border-radius: 12px;
        padding: 1.5rem;
        background: rgba(239, 68, 68, 0.05);
        margin-top: 1rem;
    }
    .danger-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #ef4444;
        margin-bottom: 0.5rem;
    }
    .danger-header h3 { margin: 0; font-size: 1.1rem; }
    
    /* Reset button - isolated class to avoid external CSS conflicts */
    .btn-reset {
        display: inline-flex;
        align-items: center;
        background-color: #ef4444;
        color: #ffffff;
        border: none;
        padding: 0.85rem 2.5rem;
        border-radius: 100px;
        font-family: 'Outfit', sans-serif;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        letter-spacing: 0;
        text-transform: none;
        width: auto;
    }
    .btn-reset:hover {
        background-color: #dc2626;
        box-shadow: 0 4px 16px rgba(239, 68, 68, 0.4);
        transform: translateY(-1px);
    }
    html[data-ui-version="v1"] .btn-danger,
    html[data-ui-version="v2"] .btn-danger,
    .btn-danger {
        background: #ef4444 !important;
        color: #fff !important;
        border: none !important;
        padding: 0.75rem 1.5rem !important;
        border-radius: 50px !important; /* Pill shape for Material 3 */
        font-weight: 700 !important;
        font-family: inherit !important;
        cursor: pointer !important;
        transition: all 0.3s ease !important;
    }
    .btn-danger:hover {
        background: #dc2626 !important;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3) !important;
    }
    
    /* Neon Glow Theme Adaptation for Danger Button */
    html[data-ui-version="v2"] .btn-danger {
        background: rgba(239, 68, 68, 0.1) !important;
        color: #ef4444 !important;
        border: 1px solid #ef4444 !important;
        box-shadow: 0 0 10px rgba(239, 68, 68, 0.2) !important;
    }
    html[data-ui-version="v2"] .btn-danger:hover {
        background: #ef4444 !important;
        color: #fff !important;
        box-shadow: 0 0 15px rgba(239, 68, 68, 0.4) !important;
    }
    
    /* UI Version Selector */
    .ui-version-selector-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-top: 1rem;
    }
    .ui-version-card {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.05);
        border-radius: 16px;
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }
    .ui-version-card:hover {
        background: rgba(255,255,255,0.05);
        border-color: rgba(255,255,255,0.1);
    }
    .ui-version-card.active {
        border-color: var(--m3-primary, #80dec5);
        background: rgba(128, 222, 197, 0.05);
    }
    
    /* Neon Glow Theme Adaptation for Active Card */
    html[data-ui-version="v2"] .ui-version-card.active {
        border-color: #10b981;
        background: rgba(16, 185, 129, 0.05);
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.2);
    }
    .ui-preview-icon {
        width: 48px; height: 48px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
    }
    .ui-preview-icon.glow-v2 { background: #111; color: #10b981; box-shadow: 0 0 10px rgba(16,185,129,0.3); }
    .ui-preview-icon.m3-v1 { background: var(--m3-surface-container-high, #242c29); color: var(--m3-primary, #80dec5); }
    
    .ui-version-info { flex: 1; }
    .ui-version-info h3 { margin: 0; font-size: 1rem; }
    .ui-version-info p { margin: 0.25rem 0 0; font-size: 0.8rem; color: #9ca3af; }
    
    .ui-select-indicator {
        position: absolute;
        top: 1rem; right: 1rem;
        color: var(--m3-primary, #80dec5);
        font-size: 1.25rem;
        display: none;
    }
    
    html[data-ui-version="v2"] .ui-select-indicator {
        color: #10b981;
        text-shadow: 0 0 5px rgba(16,185,129,0.5);
    }
    .ui-version-card.active .ui-select-indicator { display: block; }
    
    /* Modal */
    .modal-overlay {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.8);
        backdrop-filter: blur(5px);
        display: flex; align-items: center; justify-content: center;
        opacity: 0; visibility: hidden;
        transition: all 0.3s ease;
        z-index: 1000;
    }
    .modal-overlay.active { opacity: 1; visibility: visible; }
    .modal-box {
        background: #1a1a1a;
        border: 1px solid #262626;
        border-radius: 16px;
        padding: 2rem;
        max-width: 400px;
        text-align: center;
        transform: scale(0.9);
        transition: all 0.3s ease;
    }
    .modal-overlay.active .modal-box { transform: scale(1); }
    .modal-icon { font-size: 3rem; color: #ef4444; margin-bottom: 1rem; }
    .modal-title { margin: 0 0 0.5rem 0; font-size: 1.5rem; }
    .modal-text { color: #9ca3af; font-size: 0.9rem; margin-bottom: 2rem; }
    .modal-actions { display: flex; gap: 1rem; justify-content: center; }
    .btn-cancel { background: #262626; color: #ededed; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; cursor: pointer; }
</style>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
@endsection

@section('content')
<div class="settings-container">
    <div class="glow-card settings-card">
        <h2 class="section-title" style="margin: 0 0 0.5rem 0; color: #ededed;">Manajemen Data Sensor</h2>
        <p class="text-muted" style="margin-bottom: 2rem; color: #9ca3af;">Kontrol riwayat pembacaan sensor pada sistem database MariaDB Anda.</p>

        <div class="danger-zone">
            <div class="danger-header">
                <span class="material-symbols-rounded">warning</span>
                <h3>Zona Berbahaya</h3>
            </div>
            <p style="font-size: 0.9rem; color: #9ca3af;">Tindakan ini akan menghapus permanen seluruh riwayat suhu, kelembapan, dan status pompa dari database. Aksi ini tidak dapat dibatalkan.</p>

            <form id="resetForm" action="{{ route('settings.reset') }}" method="POST">
                @csrf
                <button type="button" class="btn-reset" onclick="bukaModal()">Reset Semua Data Sensor</button>
            </form>

            <!-- MODAL -->
            <div id="modalReset" class="modal-overlay">
                <div class="modal-box">
                    <div class="modal-icon material-symbols-rounded">warning</div>
                    <h3 class="modal-title">Peringatan Keras!</h3>
                    <p class="modal-text">Apakah Anda yakin ingin menghapus SEMUA data riwayat suhu dan kelembapan? Tindakan ini tidak bisa dibatalkan!</p>
                    <div class="modal-actions">
                        <button type="button" class="btn-cancel" onclick="tutupModal()">Batal</button>
                        <button type="button" class="btn-danger" onclick="gasReset()">Ya, Hapus Semua!</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PILIHAN DESAIN ANTARMUKA -->
    <div class="glow-card settings-card">
        <h2 class="section-title" style="margin: 0 0 0.5rem 0; color: #ededed;">Desain Antarmuka (UI Version)</h2>
        <p class="text-muted" style="margin-bottom: 2rem; color: #9ca3af;">Pilih gaya visual antarmuka sistem JAMKOT yang paling cocok dengan preferensi Anda.</p>
        
        <div class="ui-version-selector-grid">
            <!-- Card UI V2 (Neon Glow Dark) -->
            <div class="ui-version-card" id="ui-card-v2" onclick="setUiVersion('v2')">
                <div class="ui-preview-icon glow-v2">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                </div>
                <div class="ui-version-info">
                    <h3>UI V2: Neon Glow Dark</h3>
                    <p>Tema default gelap dengan pendaran neon futuristik yang modern.</p>
                </div>
                <div class="ui-select-indicator">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>

            <!-- Card UI V1 (Material 3 Expressive) -->
            <div class="ui-version-card" id="ui-card-v1" onclick="setUiVersion('v1')">
                <div class="ui-preview-icon m3-v1">
                    <i class="fa-solid fa-palette"></i>
                </div>
                <div class="ui-version-info">
                    <h3>UI V1: Material 3 Expressive</h3>
                    <p>Desain premium berbasis Google Material Design 3 dengan lekukan ekspresif, warna tonal pastel, dan tata letak dinamis.</p>
                </div>
                <div class="ui-select-indicator">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function bukaModal() {
        document.getElementById('modalReset').classList.add('active');
    }
    function tutupModal() {
        document.getElementById('modalReset').classList.remove('active');
    }
    function gasReset() {
        document.getElementById('resetForm').submit();
    }

    // --- UI VERSION CONTROLLERS ---
    function setUiVersion(version) {
        if (localStorage.getItem('jamkot-ui-version') === version) return;
        
        const overlay = document.getElementById('page-transition-overlay');
        const panelContent = document.querySelector('.panel-content');
        
        // 1. Smoothly fade out the page content and reveal the blurred transition overlay
        if (panelContent) {
            panelContent.classList.remove('loaded');
        }
        if (overlay) {
            overlay.classList.remove('hidden');
        }
        
        // 2. Wait for exit animations to finish, then hot-swap variables instantly
        setTimeout(() => {
            localStorage.setItem('jamkot-ui-version', version);
            document.documentElement.setAttribute('data-ui-version', version);
            updateUiCards(version);
            
            // Dispatch custom event to let chart.js dynamically repaint graphs on-the-fly
            window.dispatchEvent(new CustomEvent('ui-theme-changed', { detail: { version } }));
            
            // 3. Keep the gorgeous Liquid Blob spinning for a brief moment, then fade back in
            setTimeout(() => {
                if (panelContent) {
                    panelContent.classList.add('loaded');
                }
                if (overlay) {
                    overlay.classList.add('hidden');
                }
            }, 400); // Perfect timing for satisfying organic liquid visual feedback
        }, 300);
    }

    function updateUiCards(activeVersion) {
        const cardV1 = document.getElementById('ui-card-v1');
        const cardV2 = document.getElementById('ui-card-v2');
        
        if (cardV1 && cardV2) {
            if (activeVersion === 'v1') {
                cardV1.classList.add('active');
                cardV2.classList.remove('active');
            } else {
                cardV2.classList.add('active');
                cardV1.classList.remove('active');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const currentUi = localStorage.getItem('jamkot-ui-version') || 'v1';
        updateUiCards(currentUi);
    });
</script>
@endsection
