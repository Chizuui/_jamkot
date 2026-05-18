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
    <title>Analisis Data | JAMKOT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,500,0,0&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">
    <link rel="stylesheet" href="{{ asset('css/analisis.css') }}">
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
                    <h1>ANALISIS DATA</h1>
                    <p>Ringkasan akumulasi dan statistik performa sistem JAMKOT.</p>
                </div>
            </header>

            <!-- STATISTIK UTAMA -->
            <div class="summary-grid">
                <div class="glow-card stat-card meter-card meter-card-temperature" style="--meter-angle: {{ min(max(($stats['avg_suhu'] ?? 0) / 40, 0), 1) * 180 }}deg;">
                    <div class="card-title">RATA-RATA SUHU</div>
                    <div class="card-value">{{ number_format($stats['avg_suhu'], 1) }}°C</div>
                    <div class="card-desc">Dari seluruh rekaman data</div>
                </div>

                <div class="glow-card stat-card meter-card meter-card-humidity" style="--meter-angle: {{ min(max(($stats['avg_kelembapan'] ?? 0) / 100, 0), 1) * 180 }}deg;">
                    <div class="card-title">RATA-RATA KELEMBAPAN</div>
                    <div class="card-value">{{ number_format($stats['avg_kelembapan'], 1) }}%</div>
                    <div class="card-desc">Target ideal: 85%</div>
                </div>

                <div class="glow-card stat-card total-log-card">
                    <div class="card-title">TOTAL LOG SISTEM</div>
                    <div class="total-log-content">
                        <span class="total-log-icon material-symbols-rounded">database</span>
                        <div class="card-value">{{ $stats['total_data'] }}</div>
                        <div class="card-desc">Database: MySQL</div>
                    </div>
                </div>
            </div>

            <!-- DETAIL RECORD -->
            <div class="analysis-row">
                <div class="glow-card record-card high">
                    <div class="record-header">
                        <span class="record-icon material-symbols-rounded">arrow_upward</span>
                        <h3 class="section-title" style="margin: 0;">Rekor Tertinggi</h3>
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

                <div class="glow-card record-card low">
                    <div class="record-header">
                        <span class="record-icon material-symbols-rounded">arrow_downward</span>
                        <h3 class="section-title" style="margin: 0;">Rekor Terendah</h3>
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
                <form action="{{ route('analisis') }}" method="GET" class="filter-form">
                    <div class="filter-group">
                        <label>Filter Tanggal</label>
                        <input type="date" name="date" value="{{ $date }}" class="filter-input">
                    </div>
                    <div class="filter-group">
                        <label>Jumlah Data</label>
                        <select name="limit" class="filter-input">
                            <option value="10" {{ $limit == 10 ? 'selected' : '' }}>10 Data</option>
                            <option value="20" {{ $limit == 20 ? 'selected' : '' }}>20 Data</option>
                            <option value="50" {{ $limit == 50 ? 'selected' : '' }}>50 Data</option>
                            <option value="100" {{ $limit == 100 ? 'selected' : '' }}>100 Data</option>
                        </select>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn-filter">Tampilkan Data</button>
                        @if($date || $limit != 10)
                            <a href="{{ route('analisis') }}" class="btn-reset">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="glow-card table-wrapper" style="margin-top: 1.5rem; margin-bottom: 3rem;">
                <div class="table-header">
                    <div>
                        <h3 class="section-title" style="margin: 0;">Histori Log Sensor</h3>
                        @if($date)
                            <span class="badge info" style="background: rgba(16, 185, 129, 0.1); color: var(--warna-utama, #10b981); border: 1px solid rgba(16, 185, 129, 0.2); margin-top: 0.5rem; display: inline-block;">
                                Data: {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                            </span>
                        @endif
                    </div>
                    <div class="export-actions">
                        <a href="{{ route('analisis.export.csv', ['date' => $date]) }}" class="btn-export csv" title="Unduh CSV">
                            <i class="fa-solid fa-file-csv"></i>
                            <span>CSV</span>
                        </a>
                        <a href="{{ route('analisis.export.pdf', ['date' => $date]) }}" target="_blank" class="btn-export pdf" title="Unduh PDF">
                            <i class="fa-solid fa-file-pdf"></i>
                            <span>PDF</span>
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
                                    <td><span class="badge success" style="background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2);">Tercatat</span></td>
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

            <style>
                /* --- Tabel Responsif --- */
                .table-responsive {
                    display: block !important;
                    width: 100% !important;
                    overflow-x: auto !important;
                    overflow-y: hidden !important;
                    -webkit-overflow-scrolling: touch !important;
                    position: relative !important;
                    padding-bottom: 10px !important; /* Space for scrollbar */
                }

                .data-table {
                    width: 100% !important;
                    min-width: 850px !important; /* Force content width */
                    border-collapse: collapse !important;
                    table-layout: auto !important;
                }

                .data-table th, .data-table td {
                    white-space: nowrap !important; /* ABSOLUTELY NO WRAPPING */
                    padding: 1.25rem 1.5rem !important;
                    text-align: left !important;
                }

                @media (max-width: 768px) {
                    .glow-card.table-wrapper {
                        padding: 1.5rem 0 !important; /* Remove horizontal padding on card to allow full-width scroll */
                        overflow: hidden !important;
                    }
                    
                    .table-header {
                        padding: 0 1.5rem 1rem 1.5rem !important;
                    }

                    .table-responsive {
                        padding: 0 1.5rem !important;
                    }

                    /* Make scrollbar always visible on some mobile browsers */
                    .table-responsive::-webkit-scrollbar {
                        height: 8px !important;
                        display: block !important;
                    }
                    .table-responsive::-webkit-scrollbar-thumb {
                        background: var(--warna-utama, #10b981) !important;
                        border-radius: 10px !important;
                    }
                    .table-responsive::-webkit-scrollbar-track {
                        background: rgba(255,255,255,0.05) !important;
                    }
                }

                /* --- Filter --- */
                .filter-form {
                    display: flex;
                    align-items: flex-end;
                    gap: 1.5rem;
                    flex-wrap: wrap;
                }

                .filter-group {
                    display: flex;
                    flex-direction: column;
                    gap: 0.6rem;
                }

                .filter-group label {
                    font-size: 0.7rem;
                    color: #9ca3af;
                    text-transform: uppercase;
                    letter-spacing: 0.1em;
                    font-weight: 600;
                }

                .filter-input {
                    background: #111;
                    border: 1px solid #262626;
                    color: #ededed;
                    padding: 0.75rem 1rem;
                    border-radius: 0.75rem;
                    font-family: inherit;
                    font-size: 0.875rem;
                    outline: none;
                    transition: all 0.3s ease;
                    min-width: 180px;
                }

                .filter-input:focus {
                    border-color: var(--warna-utama, #10b981);
                    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
                }

                .filter-actions {
                    display: flex;
                    gap: 0.75rem;
                    align-items: center;
                }

                .btn-filter {
                    background: var(--warna-utama, #10b981);
                    color: #050505;
                    border: none;
                    padding: 0.75rem 1.5rem;
                    border-radius: 0.75rem;
                    font-weight: 700;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    font-size: 0.875rem;
                }

                .btn-filter:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 8px 20px rgba(16, 185, 129, 0.25);
                }

                .btn-reset {
                    text-decoration: none;
                    color: #9ca3af;
                    font-size: 0.875rem;
                    padding: 0.75rem 1rem;
                    transition: color 0.2s;
                }

                .btn-reset:hover {
                    color: #ef4444;
                }

                @media (max-width: 600px) {
                    .filter-form {
                        flex-direction: column;
                        align-items: stretch !important; /* Stretch to fill width for a straighter look */
                        gap: 1rem !important;
                    }
                    .filter-group {
                        width: 100% !important;
                        align-items: flex-start !important;
                    }
                    .filter-input {
                        width: 100% !important;
                        text-align: left !important; /* Keep text left for better readability */
                    }
                    .filter-actions {
                        width: 100% !important;
                        flex-direction: column !important;
                        align-items: stretch !important;
                        margin-top: 0.5rem !important;
                    }
                    .btn-filter {
                        width: 100% !important;
                    }
                }

                /* --- Tema M3 --- */
                html[data-ui-version="v1"] .filter-card {
                    background: var(--m3-surface-container) !important;
                    border-radius: 28px !important;
                    border: none !important;
                    box-shadow: none !important;
                }

                html[data-ui-version="v1"] .filter-group label {
                    color: var(--m3-on-surface-variant) !important;
                    font-family: var(--m3-font) !important;
                }

                html[data-ui-version="v1"] .filter-input {
                    background: var(--m3-surface-container-high) !important;
                    border: 1px solid var(--m3-outline-variant) !important;
                    color: var(--m3-on-surface) !important;
                    border-radius: 16px !important;
                }

                html[data-ui-version="v1"] .btn-filter {
                    background: var(--m3-primary) !important;
                    color: var(--m3-on-primary) !important;
                    border-radius: 100px !important;
                    font-family: var(--m3-font) !important;
                }

                html[data-ui-version="v1"] .btn-reset {
                    font-family: var(--m3-font) !important;
                }

                html[data-ui-version="v1"] .section-title {
                    font-family: var(--m3-font) !important;
                    color: var(--m3-on-surface) !important;
                }

                html[data-ui-version="v1"] .data-table tr {
                    border-bottom-color: var(--m3-outline-variant) !important;
                }

                .export-actions {
                    display: flex;
                    gap: 0.75rem;
                }

                .btn-export {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    padding: 0.5rem 1rem;
                    border-radius: 0.75rem;
                    text-decoration: none;
                    font-size: 0.8rem;
                    font-weight: 600;
                    transition: all 0.3s ease;
                    border: 1px solid rgba(255, 255, 255, 0.1);
                    color: #ededed;
                }

                .btn-export i {
                    font-size: 1rem;
                }

                .btn-export.csv {
                    background: rgba(16, 185, 129, 0.1);
                    color: var(--warna-utama, #10b981);
                    border-color: rgba(16, 185, 129, 0.2);
                }

                .btn-export.pdf {
                    background: rgba(239, 68, 68, 0.1);
                    color: #ef4444;
                    border-color: rgba(239, 68, 68, 0.2);
                }

                .btn-export:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
                    border-color: currentColor;
                }

                @media (max-width: 480px) {
                    .table-header {
                        flex-direction: column;
                        align-items: flex-start !important;
                        gap: 1rem;
                    }
                    .export-actions {
                        width: 100%;
                    }
                    .btn-export {
                        flex: 1;
                        justify-content: center;
                    }
                }

                html[data-ui-version="v1"] .login-footer p {
                    color: var(--m3-on-surface-variant) !important;
                }

                html[data-ui-version="v1"] .data-table th {
                    color: var(--m3-on-surface-variant) !important;
                    font-family: var(--m3-font) !important;
                }
            </style>
        </main>
    </div>

    <script src="{{ asset('js/sidebar.js') }}"></script>
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
