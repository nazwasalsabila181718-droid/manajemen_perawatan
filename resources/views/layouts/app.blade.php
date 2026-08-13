<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Manajemen Perawatan') - Sistem Perawatan Armada</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Custom Design CSS System -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    <!-- Theme Initialization to prevent flash of wrong mode -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
</head>
<body>

    <div id="app-layout">
        <!-- Sidebar Navigation -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-logo">
                <div class="sidebar-logo-icon">
                    <i class="bi bi-wrench-adjustable-circle-fill"></i>
                </div>
                <div class="sidebar-logo-text notranslate" translate="no">
                    Perawatan<span>Armada</span>
                </div>
            </div>

            <ul class="sidebar-menu">
                @if(auth()->user() && auth()->user()->role === 'driver')
                    <div class="sidebar-section-label">Menu Driver</div>
                    <li>
                        <a href="{{ route('dashboard') }}" class="sidebar-link {{ Route::is('dashboard') ? 'active' : '' }}">
                            <i class="bi bi-speedometer"></i>
                            <span>Dashboard Driver</span>
                        </a>
                    </li>

                    <div class="sidebar-section-label">Aktivitas Armada</div>
                    <li>
                        <a href="{{ route('checklist.create') }}" class="sidebar-link {{ Route::is('checklist.*') ? 'active' : '' }}">
                            <i class="bi bi-clipboard2-check"></i>
                            <span>Inspeksi Harian (Pre-Trip)</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('keluhan-kendaraan.index') }}" class="sidebar-link {{ Route::is('keluhan-kendaraan.*') ? 'active' : '' }}">
                            <i class="bi bi-exclamation-octagon"></i>
                            <span>Laporan Kendala</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pembayaran.index') }}" class="sidebar-link {{ Route::is('pembayaran.*') ? 'active' : '' }}">
                            <i class="bi bi-wallet2"></i>
                            <span>Klaim Biaya Operasional</span>
                        </a>
                    </li>

                @elseif(auth()->user() && auth()->user()->role === 'teknisi')
                    <div class="sidebar-section-label">Menu Teknisi</div>
                    <li>
                        <a href="{{ route('dashboard') }}" class="sidebar-link {{ Route::is('dashboard') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2"></i>
                            <span>Dashboard Teknisi</span>
                        </a>
                    </li>

                    <div class="sidebar-section-label">Pemeliharaan</div>
                    <li>
                        <a href="{{ route('kendaraan.index') }}" class="sidebar-link {{ Route::is('kendaraan.*') ? 'active' : '' }}">
                            <i class="bi bi-truck"></i>
                            <span>Manajemen Armada</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('jadwal-perawatan.index') }}" class="sidebar-link {{ Route::is('jadwal-perawatan.*') ? 'active' : '' }}">
                            <i class="bi bi-tools"></i>
                            <span>Jadwal Pemeliharaan</span>
                        </a>
                    </li>

                    <div class="sidebar-section-label">Laporan & Cek Fisik</div>
                    <li>
                        <a href="{{ route('checklist.create') }}" class="sidebar-link {{ Route::is('checklist.*') ? 'active' : '' }}">
                            <i class="bi bi-clipboard2-check"></i>
                            <span>Inspeksi Harian</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('keluhan-kendaraan.index') }}" class="sidebar-link {{ Route::is('keluhan-kendaraan.*') ? 'active' : '' }}">
                            <i class="bi bi-exclamation-octagon"></i>
                            <span>Laporan Kendala</span>
                        </a>
                    </li>

                @else
                    <div class="sidebar-section-label">Dashboard & Analitik</div>
                    <li>
                        <a href="{{ route('dashboard') }}" class="sidebar-link {{ Route::is('dashboard') ? 'active' : '' }}">
                            <i class="bi bi-grid-1x2-fill"></i>
                            <span>Dashboard Utama</span>
                        </a>
                    </li>
                    @if(Route::has('laporan.index'))
                    <li>
                        <a href="{{ route('laporan.index') }}" class="sidebar-link {{ Route::is('laporan.*') ? 'active' : '' }}">
                            <i class="bi bi-bar-chart-line-fill"></i>
                            <span>Laporan Analitik</span>
                        </a>
                    </li>
                    @endif

                    <div class="sidebar-section-label">Manajemen Operasional</div>
                    <li>
                        <a href="{{ route('kendaraan.index') }}" class="sidebar-link {{ Route::is('kendaraan.*') ? 'active' : '' }}">
                            <i class="bi bi-truck"></i>
                            <span>Manajemen Armada</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('jadwal-perawatan.index') }}" class="sidebar-link {{ Route::is('jadwal-perawatan.*') ? 'active' : '' }}">
                            <i class="bi bi-calendar2-week"></i>
                            <span>Jadwal Pemeliharaan</span>
                        </a>
                    </li>

                    <div class="sidebar-section-label">Keuangan & Keluhan</div>
                    <li>
                        <a href="{{ route('pembayaran.index') }}" class="sidebar-link {{ Route::is('pembayaran.*') ? 'active' : '' }}">
                            <i class="bi bi-receipt-cutoff"></i>
                            <span>Riwayat Biaya & Servis</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('keluhan-kendaraan.index') }}" class="sidebar-link {{ Route::is('keluhan-kendaraan.*') ? 'active' : '' }}">
                            <i class="bi bi-exclamation-octagon"></i>
                            <span>Laporan Kendala</span>
                        </a>
                    </li>
                    

                @endif
            </ul>

            <!-- Sidebar Footer Profile Card -->
            <div class="sidebar-profile-card">
                <div class="d-flex align-items-center justify-content-between gap-2">
                    <a href="{{ route('profile.show') }}" class="d-flex align-items-center gap-2 overflow-hidden flex-grow-1 text-decoration-none hover-profile" style="color: inherit;">
                        @php
                            $isAdmin = auth()->user() && auth()->user()->role === 'administrator';
                            $avatarSize = $isAdmin ? '48px' : '38px';
                            $avatarFontSize = $isAdmin ? '16px' : '13px';
                        @endphp
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm overflow-hidden" style="width: {{ $avatarSize }}; height: {{ $avatarSize }}; font-size: {{ $avatarFontSize }}; flex-shrink: 0; background: var(--accent-gradient) !important; @if($isAdmin) border: 2.5px solid var(--accent); @endif">
                            @if(auth()->user() && auth()->user()->profile_photo)
                                <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="Avatar" style="width:100%; height:100%; object-fit:cover;">
                            @else
                                {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
                            @endif
                        </div>
                        <div class="overflow-hidden" style="min-width: 0;">
                            <div class="fw-bold text-truncate" style="font-size: 13px; line-height: 1.2;" title="{{ auth()->user()->name ?? 'Administrator' }}">{{ auth()->user()->name ?? 'Administrator' }}</div>
                            <div class="text-muted text-truncate" style="font-size: 11px; line-height: 1.2;">{{ ucfirst(auth()->user()->role ?? 'Admin') }}</div>
                        </div>
                    </a>

                    <div class="d-flex align-items-center gap-1 flex-shrink-0">
                        <!-- Theme Toggle Button -->
                        <button class="theme-toggle-btn" id="theme-toggle" aria-label="Toggle Theme" title="Ubah Mode Tampilan">
                            <i class="bi bi-sun" id="theme-icon"></i>
                        </button>

                        <!-- Logout Button -->
                        <form action="{{ route('logout') }}" method="POST" class="d-inline" id="logout-form">
                            @csrf
                            <button type="button" class="theme-toggle-btn text-danger border-0 bg-transparent" title="Keluar Sistem" onclick="confirmLogout(event)">
                                <i class="bi bi-box-arrow-right fs-5"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Workspace Area -->
        <div class="main-wrapper">
            <!-- Top Navbar Header -->
            <header class="top-header">
                <div class="header-title d-flex align-items-center">
                    <button class="mobile-nav-toggle me-3 d-inline-flex d-lg-none" id="mobile-toggle">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    <div>
                        <h1 class="h3 mb-0 fw-bold">@yield('page_title', 'Dashboard')</h1>
                        <p class="text-secondary mb-0">@yield('page_subtitle', 'Kelola operasional dan pemeliharaan armada')</p>
                    </div>
                </div>

                <div class="header-actions d-flex align-items-center gap-3">
                    <!-- Language Selector Dropdown -->
                    <div class="dropdown me-1">
                        <button class="btn btn-premium secondary rounded-pill px-2.5 py-1 d-flex align-items-center gap-1.5 shadow-sm" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 11px; height: 36px; border: 1px solid var(--border-color); background: var(--bg-secondary); transition: var(--transition-fast);">
                            <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary-subtle" style="width: 22px; height: 22px;">
                                <i class="bi bi-translate text-primary" style="font-size: 11px;"></i>
                            </div>
                            <span id="current-lang-text" class="fw-semibold text-primary-emphasis" style="font-size: 11px;">Bahasa Indonesia</span>
                            <i class="bi bi-chevron-down text-muted ms-0.5" style="font-size: 8px;"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-1.5" aria-labelledby="languageDropdown" style="border-radius: 14px; width: 210px; background: var(--bg-secondary); backdrop-filter: var(--backdrop-blur);">
                            <div class="px-2 py-1 mb-1 border-bottom d-flex align-items-center justify-content-between">
                                <span class="text-uppercase fw-bold text-secondary" style="font-size: 9px; letter-spacing: 0.06em;"><i class="bi bi-globe2 me-1 text-primary"></i> Pilih Bahasa</span>
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-1.5 py-0.5" style="font-size: 8px;">Global</span>
                            </div>
                            <div style="max-height: 260px; overflow-y: auto;" class="custom-scroll">
                                <a class="dropdown-item d-flex align-items-center justify-content-between py-1.5 px-2 rounded-2 mb-0.5 nav-lang-item" href="#" onclick="changeLanguage('id', 'Bahasa Indonesia', event)" data-lang="id" style="font-size: 11px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="https://flagcdn.com/w40/id.png" alt="ID" style="width: 16px; height: 11px; object-fit: cover; border-radius: 2px; box-shadow: 0 1px 2px rgba(0,0,0,0.15);">
                                        <span class="fw-medium">Bahasa Indonesia</span>
                                    </div>
                                    <i class="bi bi-check2 text-primary d-none active-check" style="font-size: 12px;"></i>
                                </a>
                                <a class="dropdown-item d-flex align-items-center justify-content-between py-1.5 px-2 rounded-2 mb-0.5 nav-lang-item" href="#" onclick="changeLanguage('en', 'English', event)" data-lang="en" style="font-size: 11px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="https://flagcdn.com/w40/us.png" alt="US" style="width: 16px; height: 11px; object-fit: cover; border-radius: 2px; box-shadow: 0 1px 2px rgba(0,0,0,0.15);">
                                        <span class="fw-medium">English (US)</span>
                                    </div>
                                    <i class="bi bi-check2 text-primary d-none active-check" style="font-size: 12px;"></i>
                                </a>
                                <a class="dropdown-item d-flex align-items-center justify-content-between py-1.5 px-2 rounded-2 mb-0.5 nav-lang-item" href="#" onclick="changeLanguage('ar', 'العربية', event)" data-lang="ar" style="font-size: 11px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="https://flagcdn.com/w40/sa.png" alt="SA" style="width: 16px; height: 11px; object-fit: cover; border-radius: 2px; box-shadow: 0 1px 2px rgba(0,0,0,0.15);">
                                        <span class="fw-medium">العربية (Arabic)</span>
                                    </div>
                                    <i class="bi bi-check2 text-primary d-none active-check" style="font-size: 12px;"></i>
                                </a>
                                <a class="dropdown-item d-flex align-items-center justify-content-between py-1.5 px-2 rounded-2 mb-0.5 nav-lang-item" href="#" onclick="changeLanguage('zh-CN', '中文 (简体)', event)" data-lang="zh-CN" style="font-size: 11px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="https://flagcdn.com/w40/cn.png" alt="CN" style="width: 16px; height: 11px; object-fit: cover; border-radius: 2px; box-shadow: 0 1px 2px rgba(0,0,0,0.15);">
                                        <span class="fw-medium">中文 (Chinese)</span>
                                    </div>
                                    <i class="bi bi-check2 text-primary d-none active-check" style="font-size: 12px;"></i>
                                </a>
                                <a class="dropdown-item d-flex align-items-center justify-content-between py-1.5 px-2 rounded-2 mb-0.5 nav-lang-item" href="#" onclick="changeLanguage('ja', '日本語', event)" data-lang="ja" style="font-size: 11px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="https://flagcdn.com/w40/jp.png" alt="JP" style="width: 16px; height: 11px; object-fit: cover; border-radius: 2px; box-shadow: 0 1px 2px rgba(0,0,0,0.15);">
                                        <span class="fw-medium">日本語 (Japanese)</span>
                                    </div>
                                    <i class="bi bi-check2 text-primary d-none active-check" style="font-size: 12px;"></i>
                                </a>
                                <a class="dropdown-item d-flex align-items-center justify-content-between py-1.5 px-2 rounded-2 mb-0.5 nav-lang-item" href="#" onclick="changeLanguage('ko', '한국어', event)" data-lang="ko" style="font-size: 11px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="https://flagcdn.com/w40/kr.png" alt="KR" style="width: 16px; height: 11px; object-fit: cover; border-radius: 2px; box-shadow: 0 1px 2px rgba(0,0,0,0.15);">
                                        <span class="fw-medium">한국어 (Korean)</span>
                                    </div>
                                    <i class="bi bi-check2 text-primary d-none active-check" style="font-size: 12px;"></i>
                                </a>
                                <a class="dropdown-item d-flex align-items-center justify-content-between py-1.5 px-2 rounded-2 mb-0.5 nav-lang-item" href="#" onclick="changeLanguage('es', 'Español', event)" data-lang="es" style="font-size: 11px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="https://flagcdn.com/w40/es.png" alt="ES" style="width: 16px; height: 11px; object-fit: cover; border-radius: 2px; box-shadow: 0 1px 2px rgba(0,0,0,0.15);">
                                        <span class="fw-medium">Español</span>
                                    </div>
                                    <i class="bi bi-check2 text-primary d-none active-check" style="font-size: 12px;"></i>
                                </a>
                                <a class="dropdown-item d-flex align-items-center justify-content-between py-1.5 px-2 rounded-2 mb-0.5 nav-lang-item" href="#" onclick="changeLanguage('fr', 'Français', event)" data-lang="fr" style="font-size: 11px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="https://flagcdn.com/w40/fr.png" alt="FR" style="width: 16px; height: 11px; object-fit: cover; border-radius: 2px; box-shadow: 0 1px 2px rgba(0,0,0,0.15);">
                                        <span class="fw-medium">Français</span>
                                    </div>
                                    <i class="bi bi-check2 text-primary d-none active-check" style="font-size: 12px;"></i>
                                </a>
                                <a class="dropdown-item d-flex align-items-center justify-content-between py-1.5 px-2 rounded-2 mb-0.5 nav-lang-item" href="#" onclick="changeLanguage('de', 'Deutsch', event)" data-lang="de" style="font-size: 11px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="https://flagcdn.com/w40/de.png" alt="DE" style="width: 16px; height: 11px; object-fit: cover; border-radius: 2px; box-shadow: 0 1px 2px rgba(0,0,0,0.15);">
                                        <span class="fw-medium">Deutsch</span>
                                    </div>
                                    <i class="bi bi-check2 text-primary d-none active-check" style="font-size: 12px;"></i>
                                </a>
                                <a class="dropdown-item d-flex align-items-center justify-content-between py-1.5 px-2 rounded-2 mb-0.5 nav-lang-item" href="#" onclick="changeLanguage('ru', 'Русский', event)" data-lang="ru" style="font-size: 11px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="https://flagcdn.com/w40/ru.png" alt="RU" style="width: 16px; height: 11px; object-fit: cover; border-radius: 2px; box-shadow: 0 1px 2px rgba(0,0,0,0.15);">
                                        <span class="fw-medium">Русский</span>
                                    </div>
                                    <i class="bi bi-check2 text-primary d-none active-check" style="font-size: 12px;"></i>
                                </a>
                                <a class="dropdown-item d-flex align-items-center justify-content-between py-1.5 px-2 rounded-2 mb-0.5 nav-lang-item" href="#" onclick="changeLanguage('pt', 'Português', event)" data-lang="pt" style="font-size: 11px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="https://flagcdn.com/w40/pt.png" alt="PT" style="width: 16px; height: 11px; object-fit: cover; border-radius: 2px; box-shadow: 0 1px 2px rgba(0,0,0,0.15);">
                                        <span class="fw-medium">Português</span>
                                    </div>
                                    <i class="bi bi-check2 text-primary d-none active-check" style="font-size: 12px;"></i>
                                </a>
                                <a class="dropdown-item d-flex align-items-center justify-content-between py-1.5 px-2 rounded-2 mb-0.5 nav-lang-item" href="#" onclick="changeLanguage('hi', 'हिन्दी', event)" data-lang="hi" style="font-size: 11px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="https://flagcdn.com/w40/in.png" alt="IN" style="width: 16px; height: 11px; object-fit: cover; border-radius: 2px; box-shadow: 0 1px 2px rgba(0,0,0,0.15);">
                                        <span class="fw-medium">हिन्दी (Hindi)</span>
                                    </div>
                                    <i class="bi bi-check2 text-primary d-none active-check" style="font-size: 12px;"></i>
                                </a>
                                <a class="dropdown-item d-flex align-items-center justify-content-between py-1.5 px-2 rounded-2 mb-0.5 nav-lang-item" href="#" onclick="changeLanguage('nl', 'Nederlands', event)" data-lang="nl" style="font-size: 11px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="https://flagcdn.com/w40/nl.png" alt="NL" style="width: 16px; height: 11px; object-fit: cover; border-radius: 2px; box-shadow: 0 1px 2px rgba(0,0,0,0.15);">
                                        <span class="fw-medium">Nederlands</span>
                                    </div>
                                    <i class="bi bi-check2 text-primary d-none active-check" style="font-size: 12px;"></i>
                                </a>
                                <a class="dropdown-item d-flex align-items-center justify-content-between py-1.5 px-2 rounded-2 mb-0.5 nav-lang-item" href="#" onclick="changeLanguage('tr', 'Türkçe', event)" data-lang="tr" style="font-size: 11px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="https://flagcdn.com/w40/tr.png" alt="TR" style="width: 16px; height: 11px; object-fit: cover; border-radius: 2px; box-shadow: 0 1px 2px rgba(0,0,0,0.15);">
                                        <span class="fw-medium">Türkçe</span>
                                    </div>
                                    <i class="bi bi-check2 text-primary d-none active-check" style="font-size: 12px;"></i>
                                </a>
                                <a class="dropdown-item d-flex align-items-center justify-content-between py-1.5 px-2 rounded-2 mb-0.5 nav-lang-item" href="#" onclick="changeLanguage('vi', 'Tiếng Việt', event)" data-lang="vi" style="font-size: 11px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="https://flagcdn.com/w40/vn.png" alt="VN" style="width: 16px; height: 11px; object-fit: cover; border-radius: 2px; box-shadow: 0 1px 2px rgba(0,0,0,0.15);">
                                        <span class="fw-medium">Tiếng Việt</span>
                                    </div>
                                    <i class="bi bi-check2 text-primary d-none active-check" style="font-size: 12px;"></i>
                                </a>
                                <a class="dropdown-item d-flex align-items-center justify-content-between py-1.5 px-2 rounded-2 mb-0.5 nav-lang-item" href="#" onclick="changeLanguage('th', 'ไทย', event)" data-lang="th" style="font-size: 11px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="https://flagcdn.com/w40/th.png" alt="TH" style="width: 16px; height: 11px; object-fit: cover; border-radius: 2px; box-shadow: 0 1px 2px rgba(0,0,0,0.15);">
                                        <span class="fw-medium">ไทย (Thai)</span>
                                    </div>
                                    <i class="bi bi-check2 text-primary d-none active-check" style="font-size: 12px;"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden Google Translate Element -->
                    <div id="google_translate_element" style="display: none;"></div>

                    <!-- Notifications Center -->
                    <div class="dropdown">
                        <button class="btn btn-premium secondary rounded-circle p-0 d-flex align-items-center justify-content-center position-relative" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="width: 42px; height: 42px;" onclick="loadNotifications()">
                            <i class="bi bi-bell fs-5"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" id="notif-badge" style="display: none; font-size: 10px;">
                                0
                            </span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end shadow-lg p-0 border-0" aria-labelledby="notificationDropdown" style="width: 360px; border-radius: 16px; overflow: hidden;">
                            <div class="px-3 py-3 fw-bold d-flex justify-content-between align-items-center" style="background: var(--accent-gradient, linear-gradient(135deg,#6366f1,#8b5cf6)); color:#fff;">
                                <span class="d-flex align-items-center gap-2"><i class="bi bi-bell-fill"></i> Pusat Notifikasi</span>
                                <span class="badge bg-white text-primary rounded-pill" id="notif-header-badge" style="font-size: 11px;">Baru</span>
                            </div>
                            <div id="notif-list" class="list-group list-group-flush" style="max-height: 340px; overflow-y: auto;">
                                <div class="p-3 text-center text-muted small">Memuat...</div>
                            </div>
                            <div class="border-top d-flex justify-content-between align-items-center px-3 py-2" style="background: var(--bg-secondary, #f8fafc);">
                                <button class="btn btn-sm btn-outline-secondary" style="font-size:11px;" onclick="markAllNotifRead(event)"><i class="bi bi-check2-all me-1"></i>Tandai Semua Dibaca</button>
                                <a href="{{ route('notifikasi.index') }}" class="btn btn-sm btn-primary" style="font-size:11px;"><i class="bi bi-list-ul me-1"></i>Lihat Semua</a>
                            </div>
                        </div>
                    </div>

                    <!-- System Live Time -->
                    <div class="text-end d-none d-sm-block bg-secondary px-3 py-2 rounded-3 border notranslate" translate="no" style="background-color: var(--bg-secondary);">
                        <div class="text-muted fw-bold" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.05em;">Waktu Sistem</div>
                        <div class="text-primary fw-bold" style="font-size: 13px;" id="system-time"></div>
                    </div>
                </div>
            </header>

            <!-- Global Alert Messages -->
            @if(session('success'))
                <div class="alert-premium success" role="alert">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <div class="fw-semibold">{{ session('success') }}</div>
                </div>
            @endif

            @if($errors->any())
                <div class="alert-premium danger" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div>
                        <ul class="mb-0 ps-3 fw-medium">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Main Page Content -->
            <main>
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Global App Logic -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Theme Toggle Logic
            const themeToggle = document.getElementById('theme-toggle');
            const themeIcon = document.getElementById('theme-icon');

            function updateThemeUI(theme) {
                if (theme === 'dark') {
                    themeIcon.className = 'bi bi-moon-stars-fill';
                } else {
                    themeIcon.className = 'bi bi-sun-fill';
                }
            }

            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            updateThemeUI(currentTheme);

            if (themeToggle) {
                themeToggle.addEventListener('click', () => {
                    const activeTheme = document.documentElement.getAttribute('data-theme');
                    const newTheme = activeTheme === 'dark' ? 'light' : 'dark';

                    document.documentElement.setAttribute('data-theme', newTheme);
                    localStorage.setItem('theme', newTheme);
                    updateThemeUI(newTheme);
                });
            }

            // Mobile Sidebar Drawer Toggle
            const mobileToggle = document.getElementById('mobile-toggle');
            const sidebar = document.getElementById('sidebar');

            if (mobileToggle && sidebar) {
                mobileToggle.addEventListener('click', (e) => {
                    e.stopPropagation();
                    sidebar.classList.toggle('open');
                });

                document.addEventListener('click', (e) => {
                    if (sidebar.classList.contains('open') && !sidebar.contains(e.target) && e.target !== mobileToggle) {
                        sidebar.classList.remove('open');
                    }
                });
            }

            // Live Clock
            function updateClock() {
                const now = new Date();
                const options = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
                const timeEl = document.getElementById('system-time');
                if (timeEl) {
                    timeEl.innerText = now.toLocaleDateString('id-ID', options);
                }
            }
            updateClock();
            setInterval(updateClock, 1000);

            // Fetch Notification Count
            function fetchNotifCount() {
                fetch('{{ route("notifikasi.count") }}')
                    .then(res => res.json())
                    .then(data => updateBadgeCount(data.count || 0))
                    .catch(err => console.error(err));
            }
            fetchNotifCount();
            setInterval(fetchNotifCount, 60000);
        });

        // Load notifications in dropdown
        function loadNotifications() {
            const list = document.getElementById('notif-list');
            if (!list) return;
            list.innerHTML = '<div class="p-3 text-center text-muted small"><span class="spinner-border spinner-border-sm me-2"></span>Memuat notifikasi...</div>';

            fetch('{{ route("notifikasi.list") }}')
                .then(res => res.json())
                .then(data => {
                    if (!data.items || data.items.length === 0) {
                        list.innerHTML = `
                            <div class="p-4 text-center text-muted">
                                <i class="bi bi-check-circle-fill fs-2 d-block mb-2 text-success"></i>
                                <div class="fw-semibold small">Semua notifikasi sudah dibaca</div>
                                <div style="font-size:11px;">Tidak ada pesan baru</div>
                            </div>`;
                        updateBadgeCount(0);
                        return;
                    }

                    let html = '';
                    data.items.forEach(item => {
                        const isSchedule = item.source === 'jadwal_perawatan';
                        const linkAttr  = item.link ? `href="${item.link}"` : 'href="#"';
                        const readAttr  = (!isSchedule && item.id) ? `data-notif-id="${item.id}"` : '';
                        const iconClass = item.icon || 'bi-bell text-secondary';

                        // color bar on left based on icon keywords
                        let barColor = '#6366f1';
                        if (iconClass.includes('danger'))  barColor = '#ef4444';
                        else if (iconClass.includes('warning')) barColor = '#f59e0b';
                        else if (iconClass.includes('success')) barColor = '#22c55e';
                        else if (iconClass.includes('info'))    barColor = '#3b82f6';

                        html += `
                            <a ${linkAttr} class="list-group-item list-group-item-action p-0 border-bottom text-decoration-none notif-item"
                               ${readAttr} style="cursor:pointer;" onclick="handleNotifClick(event, this)">
                                <div class="d-flex align-items-stretch">
                                    <div style="width:4px; flex-shrink:0; background:${barColor}; border-radius:0;"></div>
                                    <div class="d-flex align-items-start gap-2 p-3 w-100">
                                        <div class="mt-1 fs-5 flex-shrink-0"><i class="bi ${iconClass}"></i></div>
                                        <div class="w-100">
                                            <div class="fw-semibold small" style="line-height:1.3; color: var(--text-primary,#1e293b);">${item.title || 'Notifikasi'}</div>
                                            <div class="text-muted" style="font-size:11px; line-height:1.3; margin-top:2px;">${item.message || ''}</div>
                                        </div>
                                        ${(!isSchedule && item.id) ? '<div class="flex-shrink-0 ms-1 mt-1"><span class="badge rounded-pill bg-primary" style="font-size:9px; width:8px; height:8px; padding:0;">&nbsp;</span></div>' : ''}
                                    </div>
                                </div>
                            </a>
                        `;
                    });
                    list.innerHTML = html;
                    updateBadgeCount(data.items.length);
                })
                .catch(() => {
                    list.innerHTML = '<div class="p-3 text-center text-danger small"><i class="bi bi-wifi-off me-1"></i>Gagal memuat notifikasi.</div>';
                });
        }

        // Handle click on a notification item — mark as read then navigate
        function handleNotifClick(event, el) {
            const notifId = el.getAttribute('data-notif-id');
            const href    = el.getAttribute('href');

            if (notifId) {
                event.preventDefault();
                // Remove the unread dot indicator immediately
                const dot = el.querySelector('.badge.bg-primary');
                if (dot) dot.remove();

                fetch(`/notifikasi/${notifId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                })
                .then(() => {
                    // Refresh badge count
                    fetchNotifCount();
                    // Navigate if there's a real link
                    if (href && href !== '#') {
                        window.location.href = href;
                    }
                })
                .catch(() => {
                    if (href && href !== '#') window.location.href = href;
                });
            }
        }

        // Mark all app-notifications as read
        function markAllNotifRead(event) {
            event.stopPropagation();
            fetch('{{ route("notifikasi.read-all") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(() => {
                loadNotifications();
                fetchNotifCount();
            });
        }

        // Update the badge UI
        function updateBadgeCount(count) {
            const badge = document.getElementById('notif-badge');
            if (!badge) return;
            if (count > 0) {
                badge.innerText = count > 99 ? '99+' : count;
                badge.style.display = 'block';
            } else {
                badge.style.display = 'none';
            }
        }
    </script>

    <!-- Google Translate Script & Handler -->
    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'id',
                includedLanguages: 'id,en,ar,zh-CN,ja,ko,es,fr,de,ru,pt,hi,nl,tr,vi,th',
                autoDisplay: false
            }, 'google_translate_element');
        }

        function changeLanguage(langCode, langName, event) {
            if (event) event.preventDefault();
            
            // Update UI dropdown text
            const currentLangEl = document.getElementById('current-lang-text');
            if (currentLangEl) currentLangEl.innerText = langName;

            updateActiveCheckmark(langCode);
            
            const domain = window.location.hostname;

            if (langCode === 'id') {
                // Clear translate cookie to restore original Bahasa Indonesia
                document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=" + domain;
                document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";
                document.cookie = "googtrans=/id/id; path=/; domain=" + domain;
                document.cookie = "googtrans=/id/id; path=/";
                window.location.reload();
                return;
            }

            // Set googtrans cookie for persistent translation across pages
            document.cookie = "googtrans=/id/" + langCode + "; path=/; domain=" + domain;
            document.cookie = "googtrans=/id/" + langCode + "; path=/";
            
            // Trigger Google Translate iframe element change if loaded
            const selectEl = document.querySelector('.goog-te-combo');
            if (selectEl) {
                selectEl.value = langCode;
                selectEl.dispatchEvent(new Event('change'));
            } else {
                window.location.reload();
            }
        }

        function updateActiveCheckmark(activeLang) {
            document.querySelectorAll('.nav-lang-item').forEach(item => {
                const check = item.querySelector('.active-check');
                if (item.getAttribute('data-lang') === activeLang) {
                    item.classList.add('bg-primary-subtle', 'fw-bold');
                    if (check) check.classList.remove('d-none');
                } else {
                    item.classList.remove('bg-primary-subtle', 'fw-bold');
                    if (check) check.classList.add('d-none');
                }
            });
        }

        // Maintain active language text on page load
        document.addEventListener('DOMContentLoaded', function() {
            let currentLang = 'id';
            const match = document.cookie.match(/(?:^|; )googtrans=([^;]*)/);
            if (match) {
                const langPair = decodeURIComponent(match[1]);
                currentLang = langPair.split('/')[2] || 'id';
            }

            const langMap = {
                'id': 'Bahasa Indonesia',
                'en': 'English',
                'ar': 'العربية',
                'zh-CN': '中文 (简体)',
                'ja': '日本語',
                'ko': '한국어',
                'es': 'Español',
                'fr': 'Français',
                'de': 'Deutsch',
                'ru': 'Русский',
                'pt': 'Português',
                'hi': 'हिन्दी',
                'nl': 'Nederlands',
                'tr': 'Türkçe',
                'vi': 'Tiếng Việt',
                'th': 'ไทย'
            };

            if (langMap[currentLang]) {
                const currentLangEl = document.getElementById('current-lang-text');
                if (currentLangEl) currentLangEl.innerText = langMap[currentLang];
            }
            updateActiveCheckmark(currentLang);
        });
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmLogout(event) {
            if (event) event.preventDefault();
            Swal.fire({
                html: `
                    <div class="logout-card-body text-center">
                        <div class="mb-2">
                            <span class="logout-badge-tag">
                                <span class="pulse-dot"></span> NOTIFIKASI KELUAR
                            </span>
                        </div>
                        
                        <div class="logout-icon-wrapper mx-auto mb-2">
                            <div class="logout-icon-ring"></div>
                            <div class="logout-icon-inner">
                                <i class="bi bi-box-arrow-right"></i>
                            </div>
                        </div>

                        <h6 class="logout-card-title fw-bold mb-1">Konfirmasi Sesi Keluar</h6>
                        
                        @if(auth()->user())
                        <div class="logout-user-badge d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-2">
                            <i class="bi bi-person-circle text-danger" style="font-size: 13px;"></i>
                            <span class="fw-semibold text-dark" style="font-size: 11.5px;">{{ auth()->user()->name }}</span>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle text-capitalize font-monospace" style="font-size: 9.5px; padding: 2px 6px;">{{ auth()->user()->role ?? 'User' }}</span>
                        </div>
                        @endif

                        <p class="logout-card-desc mb-0">
                            Apakah Anda yakin ingin keluar dari sistem <strong>Perawatan Armada</strong>?
                        </p>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: '<i class="bi bi-box-arrow-right me-1"></i> Ya, Keluar',
                cancelButtonText: 'Batal',
                buttonsStyling: false,
                customClass: {
                    container: 'swal-backdrop-blur',
                    popup: 'swal-modal-compact logout-modal-card',
                    confirmButton: 'btn-swal-danger logout-confirm-btn',
                    cancelButton: 'btn-swal-cancel logout-cancel-btn'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }

        // Global confirmation handler for forms with data-confirm or onsubmit confirm
        document.addEventListener('submit', function(e) {
            const form = e.target;
            const confirmMsg = form.getAttribute('data-confirm');
            if (confirmMsg && !form.dataset.confirmed) {
                e.preventDefault();
                Swal.fire({
                    html: `
                        <div class="text-center pt-1">
                            <div class="swal-icon-badge warning mx-auto">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                            </div>
                            <h6 class="fw-bold mb-1" style="font-size: 1.1rem; color: var(--text-primary);">Konfirmasi Tindakan</h6>
                            <p class="text-secondary mb-0" style="font-size: 12.5px; line-height: 1.45;">${confirmMsg}</p>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Lanjutkan',
                    cancelButtonText: 'Batal',
                    buttonsStyling: false,
                    customClass: {
                        popup: 'swal-modal-compact',
                        confirmButton: 'btn-swal-primary',
                        cancelButton: 'btn-swal-cancel'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.dataset.confirmed = "true";
                        form.submit();
                    }
                });
            }
        });
    </script>

    @yield('scripts')
</body>
</html>