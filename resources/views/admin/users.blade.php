@extends('layouts.app')

@section('title', 'Kelola User | JAMKOT')
@section('page-title', 'KELOLA USER')
@section('page-sub', 'Atur hak akses halaman untuk setiap pengguna sistem JAMKOT.')

@section('styles')
<style>
    /* ===== PERMISSION TABLE / CARDS ===== */
    .users-table-wrapper {
        overflow-x: auto;
    }

    .users-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
        min-width: 800px; /* Prevent column squishing and text/button clipping */
    }

    .users-table th {
        text-align: left;
        padding: 0.75rem 0.5rem;
        font-size: 0.7rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--text-muted);
        border-bottom: 1px solid var(--border-color);
    }

    .users-table td {
        padding: 0.9rem 0.5rem;
        border-bottom: 1px solid rgba(255,255,255,0.04);
        vertical-align: middle;
    }

    .users-table tr:last-child td { border-bottom: none; }

    .users-table tr:hover td {
        background: rgba(255,255,255,0.02);
    }

    .user-name-cell {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--warna-utama, #10b981), #6c63ff);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 600;
        color: #fff;
        flex-shrink: 0;
        text-transform: uppercase;
    }

    .user-info strong {
        display: block;
        color: var(--text-primary);
        font-weight: 500;
    }

    .user-info small {
        color: var(--text-muted);
        font-size: 0.75rem;
    }

    /* Checkbox Style */
    .custom-checkbox {
        position: relative;
        display: inline-flex;
        align-items: center;
        cursor: pointer;
    }

    .custom-checkbox input[type="checkbox"] {
        opacity: 0;
        width: 0;
        height: 0;
        position: absolute;
    }

    .checkmark {
        width: 20px;
        height: 20px;
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .custom-checkbox:hover .checkmark {
        border-color: rgba(255,255,255,0.4);
    }

    .custom-checkbox input:checked + .checkmark {
        background: var(--warna-utama, #10b981);
        border-color: var(--warna-utama, #10b981);
    }

    .checkmark::after {
        content: "";
        display: none;
        width: 5px;
        height: 10px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
        margin-bottom: 2px;
    }

    .custom-checkbox input:checked + .checkmark::after {
        display: block;
    }

    .perm-col {
        text-align: center !important;
    }

    .save-perm-btn {
        background: var(--warna-utama, #10b981) !important;
        color: #111 !important;
        border-radius: 0.375rem !important; /* Default to Neon Glow 6px rounded */
        font-family: inherit !important;
        font-weight: 600 !important;
        padding: 0.45rem 1.1rem !important;
        font-size: 0.78rem !important;
        border: 1px solid var(--warna-utama, #10b981) !important;
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.1) !important;
        transition: all 0.2s ease-in-out !important;
        cursor: pointer;
    }

    .save-perm-btn:hover {
        background: transparent !important;
        color: var(--warna-utama, #10b981) !important;
        box-shadow: 0 0 15px rgba(16, 185, 129, 0.4) !important;
        transform: translateY(-2px) !important;
        opacity: 1 !important;
    }

    .save-perm-btn:active {
        transform: translateY(0) !important;
    }

    /* Material 3 Theme Overrides */
    html[data-ui-version="v1"] .custom-checkbox input:checked + .checkmark {
        background: var(--m3-primary) !important;
        border-color: var(--m3-primary) !important;
    }

    html[data-ui-version="v1"] .checkmark::after {
        border-color: var(--m3-on-primary) !important;
    }

    html[data-ui-version="v1"] .checkmark {
        border-color: var(--m3-outline) !important;
        border-radius: 6px !important;
    }

    html[data-ui-version="v1"] .users-table th {
        color: var(--m3-on-surface-variant) !important;
        border-bottom: 2px solid var(--m3-outline-variant) !important;
    }

    html[data-ui-version="v1"] .users-table td {
        border-bottom: 1px solid var(--m3-outline-variant) !important;
        color: var(--m3-on-surface) !important;
    }

    html[data-ui-version="v1"] .user-info strong {
        color: var(--m3-on-surface) !important;
    }

    html[data-ui-version="v1"] .user-info small {
        color: var(--m3-on-surface-variant) !important;
    }

    /* Additional card styles */
    .user-cards-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
    }

    .user-accordion-card {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        padding: 1.5rem;
        transition: all 0.3s ease;
    }

    .user-accordion-card:hover {
        transform: translateY(-4px);
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(255, 255, 255, 0.1);
    }

    .btn-delete-user {
        background: transparent !important;
        color: #ef4444 !important;
        border: 1px solid rgba(239, 68, 68, 0.2) !important;
        padding: 0.45rem 1.1rem !important;
        border-radius: 0.375rem !important;
        font-weight: 600 !important;
        font-size: 0.78rem !important;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-delete-user:hover {
        background: #ef4444 !important;
        color: #fff !important;
        box-shadow: 0 0 15px rgba(239, 68, 68, 0.4) !important;
    }

    /* Modal Styles */
    .modal-overlay {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }

    .modal-overlay.active {
        opacity: 1;
        pointer-events: auto;
    }

    .custom-modal {
        background: #1a1a1a;
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 16px;
        padding: 2rem;
        width: 100%;
        max-width: 400px;
        text-align: center;
        transform: scale(0.9);
        transition: transform 0.3s ease;
    }

    .modal-overlay.active .custom-modal {
        transform: scale(1);
    }

    .modal-icon-wrapper {
        color: #ef4444;
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    .modal-title {
        color: #ededed;
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
    }

    .modal-text {
        color: #9ca3af;
        font-size: 0.9rem;
        margin-bottom: 2rem;
    }

    .modal-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
    }

    .btn-modal {
        padding: 0.6rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-modal-cancel {
        background: #262626;
        color: #ededed;
        border: 1px solid rgba(255,255,255,0.1);
    }

    .btn-modal-cancel:hover {
        background: #333;
    }

    .btn-modal-confirm {
        background: #ef4444;
        color: #fff;
        border: none;
    }

    .btn-modal-confirm:hover {
        box-shadow: 0 0 15px rgba(239, 68, 68, 0.4);
    }
</style>
@endsection

@section('content')
@if(session('sukses'))
    <div style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); border-radius: 10px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; color: #34d399; font-size: 0.875rem;">
        <i class="fa-solid fa-circle-check" style="margin-right: 0.5rem;"></i>{{ session('sukses') }}
    </div>
@endif

@if(session('error'))
    <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); border-radius: 10px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; color: #f87171; font-size: 0.875rem;">
        <i class="fa-solid fa-circle-exclamation" style="margin-right: 0.5rem;"></i>{{ session('error') }}
    </div>
@endif

<div class="settings-container">
    <div class="glow-card settings-card" style="padding: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <i class="fa-solid fa-users" style="color: var(--warna-utama, #10b981); font-size: 1.2rem;"></i>
            <h2 class="section-title" style="margin: 0; color: #ededed;">Daftar Pengguna</h2>
        </div>
        <p class="text-muted" style="margin-bottom: 2rem; font-size: 0.85rem;">
            Centang atau hapus centang pada setiap kolom untuk mengatur akses halaman. Klik <strong>Simpan</strong> pada baris yang ingin Anda perbarui.
        </p>

        <div class="user-cards-container">
            @forelse($users as $user)
                <div class="user-accordion-card">
                    <!-- Header Card -->
                    <div class="user-card-header" style="display: flex; justify-content: space-between; align-items: center; cursor: pointer;" onclick="togglePerms('{{ $user->id }}')">
                        <div class="user-info" style="display: flex; flex-direction: column;">
                            <strong style="font-size: 1.1rem; color: #ededed;">{{ $user->username }}</strong>
                            <small style="color: #9ca3af;">{{ $user->email }}</small>
                        </div>
                        <div class="user-card-actions">
                            <button type="button" class="btn-toggle-perms" style="background: transparent; border: 1px solid rgba(255,255,255,0.2); color: #ededed; padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; transition: all 0.3s;">
                                <i class="fa-solid fa-sliders" style="margin-right: 0.5rem;"></i> Kelola Akses
                            </button>
                        </div>
                    </div>

                    <!-- Body Card (Hidden by default) -->
                    <div class="user-card-body" id="perms_{{ $user->id }}" style="display: none; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.1);">
                        <form action="{{ route('admin.users.permissions', $user) }}" method="POST">
                            @csrf
                            <div class="perms-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                                @php 
                                    $perms = [
                                        'panel' => 'Panel Utama',
                                        'analisis' => 'Analisis',
                                        'schedule' => 'Schedules',
                                        'view3d' => '3D View',
                                        'settings' => 'Settings',
                                        'admin' => 'Kelola User'
                                    ]; 
                                @endphp

                                @foreach($perms as $key => $label)
                                    <div class="perm-item" style="display: flex; align-items: center; justify-content: space-between; background: rgba(0,0,0,0.2); padding: 0.75rem; border-radius: 8px;">
                                        <span style="font-size: 0.9rem; color: #ededed;">{{ $label }}</span>
                                        <label class="custom-checkbox">
                                            <input type="checkbox" name="can_{{ $key }}" value="1" {{ $user->{"can_{$key}"} ? 'checked' : '' }}>
                                            <span class="checkmark"></span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            <div class="perms-footer" style="display: flex; justify-content: space-between; align-items: center;">
                                <button type="submit" class="save-perm-btn" style="padding: 0.6rem 1.5rem; border-radius: 8px; font-weight: 500;">
                                    <i class="fa-solid fa-floppy-disk" style="margin-right: 0.5rem;"></i> Simpan Perubahan
                                </button>
                                
                                <button type="button" class="btn-delete-user" title="Hapus User" onclick="confirmDelete('{{ $user->id }}', '{{ $user->username }}')" style="padding: 0.6rem 1rem; border-radius: 8px;">
                                    <i class="fa-solid fa-trash-can"></i> Hapus
                                </button>
                            </div>
                        </form>
                        
                        <form id="delete_form_{{ $user->id }}" action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            @empty
                <div class="empty-state" style="text-align: center; padding: 3rem; color: #9ca3af;">
                    <i class="fa-solid fa-users" style="font-size: 3rem; opacity: 0.5; margin-bottom: 1rem;"></i>
                    <p>Belum ada user terdaftar.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- CUSTOM DELETE MODAL -->
<div id="deleteModal" class="modal-overlay">
    <div class="custom-modal">
        <div class="modal-icon-wrapper">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <h3 class="modal-title">Konfirmasi Hapus</h3>
        <p class="modal-text">Apakah Anda yakin ingin menghapus user <strong id="deleteTargetName"></strong>? <br>Tindakan ini akan menghapus akun secara permanen.</p>
        <div class="modal-actions">
            <button type="button" class="btn-modal btn-modal-cancel" onclick="closeDeleteModal()">Batal</button>
            <button type="button" class="btn-modal btn-modal-confirm" id="confirmDeleteBtn">Hapus Sekarang</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function togglePerms(userId) {
        const body = document.getElementById('perms_' + userId);
        if (body.style.display === 'none' || body.style.display === '') {
            body.style.display = 'block';
        } else {
            body.style.display = 'none';
        }
    }

    let currentDeleteId = null;

    function confirmDelete(userId, username) {
        currentDeleteId = userId;
        document.getElementById('deleteTargetName').innerText = username;
        document.getElementById('deleteModal').classList.add('active');
        
        document.getElementById('confirmDeleteBtn').onclick = function() {
            document.getElementById('delete_form_' + currentDeleteId).submit();
        };
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('active');
    }

    // Close on escape
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeDeleteModal();
    });
    
    // Close on click outside
    document.getElementById('deleteModal').addEventListener('click', (e) => {
        if (e.target === document.getElementById('deleteModal')) closeDeleteModal();
    });
</script>
@endsection
