<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- PREVENT FOUC & SETUP UI THEME -->
    <script>
        (function() {
            const uiVersion = localStorage.getItem('jamkot-ui-version') || 'v1';
            document.documentElement.setAttribute('data-ui-version', uiVersion);
        })();
    </script>
    <title>Settings | JAMKOT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,500,0,0&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">
    <link rel="stylesheet" href="{{ asset('css/settings.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/material3.css') }}">
    @vite('resources/js/app.js')
</head>

<body>

    <div class="panel-layout">

        <!-- MOBILE NAV -->
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

        <!-- KONTEN UTAMA -->
        <main class="panel-content">
            <header class="content-header-flex">
                <div>
                    <h1>PENGATURAN</h1>
                    <p>Manajemen data dan sistem JAMKOT.</p>
                </div>
            </header>

            <div class="settings-container">
                <div class="glow-card settings-card">
                    <h2 class="section-title" style="margin: 0 0 0.5rem 0; color: #ededed;">Manajemen Data Sensor</h2>
                    <p class="text-muted" style="margin-bottom: 2rem;">Kontrol riwayat pembacaan sensor pada sistem
                        database MariaDB Anda.</p>

                    <div class="danger-zone">
                        <div class="danger-header">
                            <span class="danger-icon material-symbols-rounded">warning</span>
                            <h3>Zona Berbahaya</h3>
                        </div>
                        <p>Tindakan ini akan menghapus permanen seluruh riwayat suhu, kelembapan, dan status pompa dari
                            database. Aksi ini tidak dapat dibatalkan.</p>

                        <form id="resetForm" action="{{ route('settings.reset') }}" method="POST">
                            @csrf
                            <button type="button" class="btn-danger" onclick="bukaModal()">Reset Semua Data
                                Sensor</button>
                        </form>

                        <!-- MODAL -->
                        <div id="modalReset" class="modal-overlay">
                            <div class="modal-box">
                                <div class="modal-icon material-symbols-rounded">warning</div>
                                <h3 class="modal-title">Peringatan Keras!</h3>
                                <p class="modal-text">Apakah Anda yakin ingin menghapus SEMUA data riwayat suhu dan
                                    kelembapan? Tindakan ini tidak bisa dibatalkan!</p>
                                <div class="modal-actions">
                                    <button type="button" class="btn-cancel" onclick="tutupModal()">Batal</button>
                                    <button type="button" class="btn-danger" onclick="gasReset()">Ya, Hapus
                                        Semua!</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PILIHAN DESAIN ANTARMUKA -->
                <div class="glow-card settings-card" style="margin-top: 2rem;">
                    <h2 class="section-title" style="margin: 0 0 0.5rem 0; color: #ededed;">Desain Antarmuka (UI Version)</h2>
                    <p class="text-muted" style="margin-bottom: 2rem;">Pilih gaya visual antarmuka sistem JAMKOT yang paling cocok dengan preferensi Anda.</p>
                    
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

        </main>
    </div>

    <!-- TOAST NOTIFICATION -->
    @if(session('sukses'))
        <div id="toast-modern" class="toast-wrapper">
            <div class="toast-progress"></div>
            <div class="toast-body">
                <div class="toast-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
                <div class="toast-text">
                    <h4>Success</h4>
                    <p>{{ session('sukses') }}</p>
                </div>s
                <button class="toast-close" onclick="tutupToastModern()">×</button>
            </div>
        </div>
        <script src="{{ asset('js/toast.js') }}"></script>
    @endif
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <!-- MODAL SCRIPTS -->
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
