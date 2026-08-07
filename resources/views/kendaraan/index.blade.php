@extends('layouts.app')

@section('title', 'Perawatan Armada')
@section('page_title', 'Manajemen Perawatan Armada')
@section('page_subtitle', 'Pantau odometer, tanggal jatuh tempo KIR, pajak tahunan, serta status operasional kendaraan.')

@section('content')
<div class="container-fluid p-0">

    <!-- Top Action Row -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-3 mb-4">
        <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-3 flex-grow-1">
            <!-- Search bar -->
            <div class="search-box-premium flex-grow-1" style="max-width: 320px;">
                <i class="bi bi-search"></i>
                <input type="text" id="vehicle-search" class="form-control-premium" placeholder="Cari nopol, merek, driver...">
            </div>

            <!-- Filter Pills -->
            <div class="premium-filter-pills flex-wrap">
                <button class="filter-pill-btn active" data-filter="all">
                    <i class="bi bi-grid-1x2-fill me-1"></i> Semua
                </button>
                <button class="filter-pill-btn" data-filter="mobil">
                    <i class="bi bi-car-front-fill me-1"></i> Mobil
                </button>
                <button class="filter-pill-btn" data-filter="motor">
                    <i class="bi bi-bicycle me-1"></i> Motor
                </button>
                <button class="filter-pill-btn" data-filter="warning">
                    <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> Perlu Perhatian
                </button>
                <button class="filter-pill-btn" data-filter="baik">
                    <i class="bi bi-check-circle-fill text-success me-1"></i> Kondisi Baik
                </button>
            </div>
        </div>

        <!-- Scan & Add Buttons -->
        <div class="d-flex align-items-center gap-2 flex-shrink-0">
            <button class="btn-premium secondary" data-bs-toggle="modal" data-bs-target="#scanCameraModal">
                <i class="bi bi-qr-code-scan"></i>
                <span>Scan Barcode</span>
            </button>

            @if(auth()->user() && auth()->user()->role === 'administrator')
            <button class="btn-premium primary" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
                <i class="bi bi-plus-lg"></i>
                <span>Daftarkan Kendaraan Baru</span>
            </button>
            @endif
        </div>
    </div>

    <!-- Stats summary row -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card-ultra">
                <div class="stat-icon-wrapper indigo">
                    <i class="bi bi-truck"></i>
                </div>
                <div class="stat-meta">
                    <div class="stat-label-text">Total Armada</div>
                    <div class="stat-value-text">{{ $total }} <span class="fs-6 fw-normal text-muted">unit</span></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card-ultra">
                <div class="stat-icon-wrapper rose">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div class="stat-meta">
                    <div class="stat-label-text">Perlu Perhatian</div>
                    <div class="stat-value-text text-danger">{{ $perlu_rawat }} <span class="fs-6 fw-normal text-muted">unit</span></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card-ultra">
                <div class="stat-icon-wrapper emerald">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-meta">
                    <div class="stat-label-text">Kondisi Baik</div>
                    <div class="stat-value-text text-success">{{ $selesai }} <span class="fs-6 fw-normal text-muted">unit</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Header Section for Cards -->
    <div class="card-premium mb-4 p-4 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
        <h5 class="fw-bold mb-0">Daftar Kendaraan Operasional</h5>
        <span class="badge-premium indigo" id="row-count">
            Menampilkan {{ $kendaraans->count() }} armada
        </span>
    </div>

    <!-- SECTION 1: ARMADA MOBIL -->
    <div class="card-premium mb-4 p-3 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3" id="mobil-section-header" style="background: var(--bg-secondary); border-left: 4px solid var(--accent);">
        <h5 class="fw-bold mb-0 text-primary d-flex align-items-center gap-2" style="font-size: 16px;">
            <i class="bi bi-car-front-fill"></i> Daftar Armada Mobil
        </h5>
        <span class="badge bg-primary-subtle text-primary px-3 py-1.5 rounded-pill fw-semibold" id="mobil-row-count" style="font-size: 11px;">
            Menampilkan {{ $mobils->count() }} armada
        </span>
    </div>

    <div class="row g-4 mb-5" id="mobil-grid">
        @forelse($mobils as $kendaraan)
        <div class="col-xl-4 col-md-6 vehicle-row" data-jenis="{{ $kendaraan->jenis_kendaraan }}" data-status-badge="{{ $kendaraan->status_badge }}">
            <div class="card-premium h-100 p-0 overflow-hidden d-flex flex-column" style="border: 1px solid var(--border-color); background: var(--bg-secondary); border-radius: 16px; box-shadow: var(--shadow-sm); transition: transform 0.2s, box-shadow 0.2s;">
                <!-- Large Photo -->
                <div class="position-relative" style="height: 200px; background: #e2e8f0; overflow: hidden;">
                    @if($kendaraan->foto_kendaraan)
                        <img src="{{ asset('storage/' . $kendaraan->foto_kendaraan) }}"
                             alt="{{ $kendaraan->nomor_polisi }}"
                             class="w-100 h-100 object-fit-cover vehicle-thumb"
                             data-bs-toggle="modal"
                             data-bs-target="#fotoModal"
                             data-foto="{{ asset('storage/' . $kendaraan->foto_kendaraan) }}"
                             data-nopol="{{ $kendaraan->nomor_polisi }}"
                             style="cursor:pointer; transition: transform 0.3s ease;">
                    @else
                        <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-muted" style="background: var(--border-subtle);">
                            <i class="bi bi-camera" style="font-size:48px; opacity: 0.5;"></i>
                            <span class="small mt-2">Tidak ada foto</span>
                        </div>
                    @endif

                    <!-- Floating Status & Barcode Badge -->
                    <button type="button" 
                            class="position-absolute top-0 start-0 m-3 btn btn-light btn-sm rounded-circle shadow-sm show-qr-btn border-0"
                            data-nopol="{{ $kendaraan->nomor_polisi }}"
                            data-merek="{{ $kendaraan->merek }} {{ $kendaraan->tipe }}"
                            data-driver="{{ $kendaraan->nama_driver }}"
                            data-odo="{{ number_format($kendaraan->odometer_terakhir, 0, ',', '.') }} KM"
                            data-status="{{ $kendaraan->status_perawatan }}"
                            style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; z-index: 5;"
                            title="Lihat / Cetak Barcode Armada">
                        <i class="bi bi-qr-code-scan text-primary" style="font-size: 15px;"></i>
                    </button>

                    <div class="position-absolute top-0 end-0 m-3">
                        <span class="badge bg-{{ $kendaraan->status_badge == 'success' ? 'success' : ($kendaraan->status_badge == 'danger' ? 'danger' : 'warning') }} text-white px-2 py-1 shadow-sm" style="font-size: 10px !important; border-radius: 4px;">
                            <i class="bi {{ $kendaraan->status_badge == 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' }} me-1"></i>
                            {{ $kendaraan->status_perawatan }}
                        </span>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-3 d-flex flex-column flex-grow-1">
                    <!-- Physical Identity -->
                    <div class="mb-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="badge bg-primary-subtle text-primary fw-semibold px-2 py-0.5 rounded mb-1.5" style="font-size:10px;">{{ $kendaraan->jenis_kendaraan }}</span>
                            <span class="text-muted" style="font-size: 11px;">Odometer: <strong class="text-dark">{{ number_format($kendaraan->odometer_terakhir, 0, ',', '.') }} KM</strong></span>
                        </div>
                        <h5 class="fw-bold text-primary mb-0.5 vehicle-nopol" style="cursor: pointer;" title="Klik untuk salin nopol" onclick="navigator.clipboard.writeText('{{ $kendaraan->nomor_polisi }}');">
                            {{ $kendaraan->nomor_polisi }} <i class="bi bi-copy text-muted opacity-50 ms-1" style="font-size: 12px;"></i>
                        </h5>
                        <div class="text-secondary vehicle-detail fw-semibold" style="font-size:12px;">{{ $kendaraan->merek }} {{ $kendaraan->tipe }}</div>
                    </div>

                    <!-- Driver & Location -->
                    <div class="p-2.5 rounded-3 mb-2.5" style="background: var(--bg-primary); border: 1px solid var(--border-color);">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-person-circle text-primary" style="font-size: 14px;"></i>
                            <div class="overflow-hidden">
                                <div class="text-muted" style="font-size:9px; line-height: 1;">DRIVER</div>
                                <div class="fw-bold text-dark text-truncate vehicle-driver" style="font-size:11.5px;">{{ $kendaraan->nama_driver }}</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-geo-alt-fill text-danger" style="font-size: 14px;"></i>
                            <div class="overflow-hidden">
                                <div class="text-muted" style="font-size:9px; line-height: 1;">LOKASI POOL</div>
                                <div class="fw-semibold text-truncate" style="font-size:11px; line-height: 1.1;">
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($kendaraan->pool_lokasi) }}" target="_blank" class="text-decoration-none text-primary vehicle-pool">
                                        {{ $kendaraan->pool_lokasi }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Legal Documents -->
                    <div class="p-2.5 rounded-3 mb-3" style="background: var(--bg-primary); border: 1px solid var(--border-color); flex-grow: 1;">
                        <div class="fw-bold text-secondary mb-2" style="font-size: 9.5px; letter-spacing: 0.05em;"><i class="bi bi-file-earmark-text-fill text-primary"></i> DOKUMEN ARMADA</div>
                        
                        <!-- STNK -->
                        @if($kendaraan->tanggal_stnk)
                            @php
                                $stnk = \Carbon\Carbon::parse($kendaraan->tanggal_stnk);
                                $isStnkExp = $stnk->isPast();
                                $isStnkNear = !$isStnkExp && $stnk->diffInDays(now()) <= 30;
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-1.5">
                                <span class="text-muted" style="font-size: 11px;">STNK:</span>
                                <span class="fw-semibold {{ $isStnkExp ? 'text-danger' : ($isStnkNear ? 'text-warning' : 'text-success') }}" style="font-size: 11px;">
                                    {{ $stnk->format('d M Y') }}
                                    @if($isStnkExp)
                                        <span class="badge bg-danger text-white py-0.5 px-1" style="font-size: 8px; border-radius: 3px;">EXP</span>
                                    @elseif($isStnkNear)
                                        <span class="badge bg-warning text-dark py-0.5 px-1" style="font-size: 8px; border-radius: 3px;">DEKAT</span>
                                    @endif
                                </span>
                            </div>
                        @endif

                        <!-- Pajak -->
                        @php
                            $pajak = \Carbon\Carbon::parse($kendaraan->pajak_tahunan);
                            $isPajakExp = $pajak->isPast();
                            $isPajakNear = !$isPajakExp && $pajak->diffInDays(now()) <= 30;
                        @endphp
                        <div class="d-flex justify-content-between align-items-center mb-1.5">
                            <span class="text-muted" style="font-size: 11px;">Pajak Tahunan:</span>
                            <span class="fw-semibold {{ $isPajakExp ? 'text-danger' : ($isPajakNear ? 'text-warning' : 'text-success') }}" style="font-size: 11px;">
                                {{ $pajak->format('d M Y') }}
                                @if($isPajakExp)
                                    <span class="badge bg-danger text-white py-0.5 px-1" style="font-size: 8px; border-radius: 3px;">EXP</span>
                                @elseif($isPajakNear)
                                    <span class="badge bg-warning text-dark py-0.5 px-1" style="font-size: 8px; border-radius: 3px;">DEKAT</span>
                                @endif
                            </span>
                        </div>

                        <!-- KIR -->
                        @if($kendaraan->kir_bengkel)
                            @php
                                $kir = \Carbon\Carbon::parse($kendaraan->kir_bengkel);
                                $isKirExp = $kir->isPast();
                                $isKirNear = !$isKirExp && $kir->diffInDays(now()) <= 30;
                            @endphp
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted" style="font-size: 11px;">Uji KIR:</span>
                                <span class="fw-semibold {{ $isKirExp ? 'text-danger' : ($isKirNear ? 'text-warning' : 'text-success') }}" style="font-size: 11px;">
                                    {{ $kir->format('d M Y') }}
                                    @if($isKirExp)
                                        <span class="badge bg-danger text-white py-0.5 px-1" style="font-size: 8px; border-radius: 3px;">EXP</span>
                                    @elseif($isKirNear)
                                        <span class="badge bg-warning text-dark py-0.5 px-1" style="font-size: 8px; border-radius: 3px;">DEKAT</span>
                                    @endif
                                </span>
                            </div>
                        @else
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted" style="font-size: 11px;">Uji KIR:</span>
                                <span class="text-muted" style="font-size: 11px;"><i class="bi bi-info-circle"></i> Tanpa KIR</span>
                            </div>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="d-flex gap-1.5 pt-2 border-top mt-auto">
                        @if(auth()->user() && auth()->user()->role === 'administrator')
                        <button class="btn btn-outline-primary btn-sm flex-grow-1 edit-btn py-1.5"
                                data-id="{{ $kendaraan->id }}"
                                data-nomor_polisi="{{ $kendaraan->nomor_polisi }}"
                                data-jenis_kendaraan="{{ $kendaraan->jenis_kendaraan }}"
                                data-merek="{{ $kendaraan->merek }}"
                                data-tipe="{{ $kendaraan->tipe }}"
                                data-nama_driver="{{ $kendaraan->nama_driver }}"
                                data-pool_lokasi="{{ $kendaraan->pool_lokasi }}"
                                data-tanggal_stnk="{{ $kendaraan->tanggal_stnk ? \Carbon\Carbon::parse($kendaraan->tanggal_stnk)->format('Y-m-d') : '' }}"
                                data-pajak_tahunan="{{ \Carbon\Carbon::parse($kendaraan->pajak_tahunan)->format('Y-m-d') }}"
                                data-pajak_5_tahunan="{{ $kendaraan->pajak_5_tahunan ? \Carbon\Carbon::parse($kendaraan->pajak_5_tahunan)->format('Y-m-d') : '' }}"
                                data-kir_bengkel="{{ $kendaraan->kir_bengkel ? \Carbon\Carbon::parse($kendaraan->kir_bengkel)->format('Y-m-d') : '' }}"
                                data-foto="{{ $kendaraan->foto_kendaraan ? asset('storage/' . $kendaraan->foto_kendaraan) : '' }}"
                                style="font-size: 11.5px;"
                                title="Edit Data Kendaraan">
                            <i class="bi bi-pencil-square"></i> Edit
                        </button>
                        @endif

                        <button class="btn btn-outline-info btn-sm flex-grow-1 show-qr-btn py-1.5"
                                data-nopol="{{ $kendaraan->nomor_polisi }}"
                                data-merek="{{ $kendaraan->merek }} {{ $kendaraan->tipe }}"
                                data-driver="{{ $kendaraan->nama_driver }}"
                                data-odo="{{ number_format($kendaraan->odometer_terakhir, 0, ',', '.') }} KM"
                                data-status="{{ $kendaraan->status_perawatan }}"
                                style="font-size: 11.5px;"
                                title="Lihat & Cetak Barcode Armada">
                            <i class="bi bi-qr-code"></i> Barcode
                        </button>

                        <button class="btn btn-outline-secondary btn-sm flex-grow-1 update-odo-btn py-1.5"
                                data-id="{{ $kendaraan->id }}"
                                data-nopol="{{ $kendaraan->nomor_polisi }}"
                                data-odo="{{ $kendaraan->odometer_terakhir }}"
                                style="font-size: 11.5px;"
                                title="Perbarui Odometer KM">
                            <i class="bi bi-speedometer2"></i> Odo
                        </button>

                        @if(auth()->user() && auth()->user()->role === 'administrator')
                        <button class="btn btn-outline-danger btn-sm delete-btn px-2.5 py-1.5"
                                data-id="{{ $kendaraan->id }}"
                                data-nopol="{{ $kendaraan->nomor_polisi }}"
                                style="font-size: 11.5px;"
                                title="Hapus Kendaraan">
                            <i class="bi bi-trash"></i>
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center text-muted py-4" id="mobil-empty-db">
            <div class="card-premium p-4">
                <i class="bi bi-inbox fs-3 d-block mb-2 text-secondary"></i>
                Belum ada data armada mobil.
            </div>
        </div>
        @endforelse
        <div class="col-12 text-center text-muted py-4" id="mobil-empty-msg" style="display: none;">
            <div class="card-premium p-4">
                <i class="bi bi-inbox fs-3 d-block mb-2 text-secondary"></i>
                Tidak ada armada mobil yang cocok dengan pencarian Anda.
            </div>
        </div>
    </div>

    <!-- SECTION 2: ARMADA MOTOR -->
    <div class="card-premium mb-4 p-3 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3" id="motor-section-header" style="background: var(--bg-secondary); border-left: 4px solid #10b981;">
        <h5 class="fw-bold mb-0 text-primary d-flex align-items-center gap-2" style="font-size: 16px;">
            <i class="bi bi-bicycle"></i> Daftar Armada Motor
        </h5>
        <span class="badge bg-success-subtle text-success px-3 py-1.5 rounded-pill fw-semibold" id="motor-row-count" style="font-size: 11px;">
            Menampilkan {{ $motors->count() }} armada
        </span>
    </div>

    <div class="row g-4 mb-5" id="motor-grid">
        @forelse($motors as $kendaraan)
        <div class="col-xl-4 col-md-6 vehicle-row" data-jenis="{{ $kendaraan->jenis_kendaraan }}" data-status-badge="{{ $kendaraan->status_badge }}">
            <div class="card-premium h-100 p-0 overflow-hidden d-flex flex-column" style="border: 1px solid var(--border-color); background: var(--bg-secondary); border-radius: 16px; box-shadow: var(--shadow-sm); transition: transform 0.2s, box-shadow 0.2s;">
                <!-- Large Photo -->
                <div class="position-relative" style="height: 200px; background: #e2e8f0; overflow: hidden;">
                    @if($kendaraan->foto_kendaraan)
                        <img src="{{ asset('storage/' . $kendaraan->foto_kendaraan) }}"
                             alt="{{ $kendaraan->nomor_polisi }}"
                             class="w-100 h-100 object-fit-cover vehicle-thumb"
                             data-bs-toggle="modal"
                             data-bs-target="#fotoModal"
                             data-foto="{{ asset('storage/' . $kendaraan->foto_kendaraan) }}"
                             data-nopol="{{ $kendaraan->nomor_polisi }}"
                             style="cursor:pointer; transition: transform 0.3s ease;">
                    @else
                        <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-muted" style="background: var(--border-subtle);">
                            <i class="bi bi-camera" style="font-size:48px; opacity: 0.5;"></i>
                            <span class="small mt-2">Tidak ada foto</span>
                        </div>
                    @endif

                    <!-- Floating Status & Barcode Badge -->
                    <button type="button" 
                            class="position-absolute top-0 start-0 m-3 btn btn-light btn-sm rounded-circle shadow-sm show-qr-btn border-0"
                            data-nopol="{{ $kendaraan->nomor_polisi }}"
                            data-merek="{{ $kendaraan->merek }} {{ $kendaraan->tipe }}"
                            data-driver="{{ $kendaraan->nama_driver }}"
                            data-odo="{{ number_format($kendaraan->odometer_terakhir, 0, ',', '.') }} KM"
                            data-status="{{ $kendaraan->status_perawatan }}"
                            style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; z-index: 5;"
                            title="Lihat / Cetak Barcode Armada">
                        <i class="bi bi-qr-code-scan text-primary" style="font-size: 15px;"></i>
                    </button>

                    <div class="position-absolute top-0 end-0 m-3">
                        <span class="badge bg-{{ $kendaraan->status_badge == 'success' ? 'success' : ($kendaraan->status_badge == 'danger' ? 'danger' : 'warning') }} text-white px-2 py-1 shadow-sm" style="font-size: 10px !important; border-radius: 4px;">
                            <i class="bi {{ $kendaraan->status_badge == 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' }} me-1"></i>
                            {{ $kendaraan->status_perawatan }}
                        </span>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-3 d-flex flex-column flex-grow-1">
                    <!-- Physical Identity -->
                    <div class="mb-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="badge bg-success-subtle text-success fw-semibold px-2 py-0.5 rounded mb-1.5" style="font-size:10px;">{{ $kendaraan->jenis_kendaraan }}</span>
                            <span class="text-muted" style="font-size: 11px;">Odometer: <strong class="text-dark">{{ number_format($kendaraan->odometer_terakhir, 0, ',', '.') }} KM</strong></span>
                        </div>
                        <h5 class="fw-bold text-primary mb-0.5 vehicle-nopol" style="cursor: pointer;" title="Klik untuk salin nopol" onclick="navigator.clipboard.writeText('{{ $kendaraan->nomor_polisi }}');">
                            {{ $kendaraan->nomor_polisi }} <i class="bi bi-copy text-muted opacity-50 ms-1" style="font-size: 12px;"></i>
                        </h5>
                        <div class="text-secondary vehicle-detail fw-semibold" style="font-size:12px;">{{ $kendaraan->merek }} {{ $kendaraan->tipe }}</div>
                    </div>

                    <!-- Driver & Location -->
                    <div class="p-2.5 rounded-3 mb-2.5" style="background: var(--bg-primary); border: 1px solid var(--border-color);">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-person-circle text-primary" style="font-size: 14px;"></i>
                            <div class="overflow-hidden">
                                <div class="text-muted" style="font-size:9px; line-height: 1;">DRIVER</div>
                                <div class="fw-bold text-dark text-truncate vehicle-driver" style="font-size:11.5px;">{{ $kendaraan->nama_driver }}</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-geo-alt-fill text-danger" style="font-size: 14px;"></i>
                            <div class="overflow-hidden">
                                <div class="text-muted" style="font-size:9px; line-height: 1;">LOKASI POOL</div>
                                <div class="fw-semibold text-truncate" style="font-size:11px; line-height: 1.1;">
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($kendaraan->pool_lokasi) }}" target="_blank" class="text-decoration-none text-primary vehicle-pool">
                                        {{ $kendaraan->pool_lokasi }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Legal Documents -->
                    <div class="p-2.5 rounded-3 mb-3" style="background: var(--bg-primary); border: 1px solid var(--border-color); flex-grow: 1;">
                        <div class="fw-bold text-secondary mb-2" style="font-size: 9.5px; letter-spacing: 0.05em;"><i class="bi bi-file-earmark-text-fill text-primary"></i> DOKUMEN ARMADA</div>
                        
                        <!-- STNK -->
                        @if($kendaraan->tanggal_stnk)
                            @php
                                $stnk = \Carbon\Carbon::parse($kendaraan->tanggal_stnk);
                                $isStnkExp = $stnk->isPast();
                                $isStnkNear = !$isStnkExp && $stnk->diffInDays(now()) <= 30;
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-1.5">
                                <span class="text-muted" style="font-size: 11px;">STNK:</span>
                                <span class="fw-semibold {{ $isStnkExp ? 'text-danger' : ($isStnkNear ? 'text-warning' : 'text-success') }}" style="font-size: 11px;">
                                    {{ $stnk->format('d M Y') }}
                                    @if($isStnkExp)
                                        <span class="badge bg-danger text-white py-0.5 px-1" style="font-size: 8px; border-radius: 3px;">EXP</span>
                                    @elseif($isStnkNear)
                                        <span class="badge bg-warning text-dark py-0.5 px-1" style="font-size: 8px; border-radius: 3px;">DEKAT</span>
                                    @endif
                                </span>
                            </div>
                        @endif

                        <!-- Pajak -->
                        @php
                            $pajak = \Carbon\Carbon::parse($kendaraan->pajak_tahunan);
                            $isPajakExp = $pajak->isPast();
                            $isPajakNear = !$isPajakExp && $pajak->diffInDays(now()) <= 30;
                        @endphp
                        <div class="d-flex justify-content-between align-items-center mb-1.5">
                            <span class="text-muted" style="font-size: 11px;">Pajak Tahunan:</span>
                            <span class="fw-semibold {{ $isPajakExp ? 'text-danger' : ($isPajakNear ? 'text-warning' : 'text-success') }}" style="font-size: 11px;">
                                {{ $pajak->format('d M Y') }}
                                @if($isPajakExp)
                                    <span class="badge bg-danger text-white py-0.5 px-1" style="font-size: 8px; border-radius: 3px;">EXP</span>
                                @elseif($isPajakNear)
                                    <span class="badge bg-warning text-dark py-0.5 px-1" style="font-size: 8px; border-radius: 3px;">DEKAT</span>
                                @endif
                            </span>
                        </div>

                        <!-- KIR -->
                        @if($kendaraan->kir_bengkel)
                            @php
                                $kir = \Carbon\Carbon::parse($kendaraan->kir_bengkel);
                                $isKirExp = $kir->isPast();
                                $isKirNear = !$isKirExp && $kir->diffInDays(now()) <= 30;
                            @endphp
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted" style="font-size: 11px;">Uji KIR:</span>
                                <span class="fw-semibold {{ $isKirExp ? 'text-danger' : ($isKirNear ? 'text-warning' : 'text-success') }}" style="font-size: 11px;">
                                    {{ $kir->format('d M Y') }}
                                    @if($isKirExp)
                                        <span class="badge bg-danger text-white py-0.5 px-1" style="font-size: 8px; border-radius: 3px;">EXP</span>
                                    @elseif($isKirNear)
                                        <span class="badge bg-warning text-dark py-0.5 px-1" style="font-size: 8px; border-radius: 3px;">DEKAT</span>
                                    @endif
                                </span>
                            </div>
                        @else
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted" style="font-size: 11px;">Uji KIR:</span>
                                <span class="text-muted" style="font-size: 11px;"><i class="bi bi-info-circle"></i> Tanpa KIR</span>
                            </div>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="d-flex gap-1.5 pt-2 border-top mt-auto">
                        @if(auth()->user() && auth()->user()->role === 'administrator')
                        <button class="btn btn-outline-primary btn-sm flex-grow-1 edit-btn py-1.5"
                                data-id="{{ $kendaraan->id }}"
                                data-nomor_polisi="{{ $kendaraan->nomor_polisi }}"
                                data-jenis_kendaraan="{{ $kendaraan->jenis_kendaraan }}"
                                data-merek="{{ $kendaraan->merek }}"
                                data-tipe="{{ $kendaraan->tipe }}"
                                data-nama_driver="{{ $kendaraan->nama_driver }}"
                                data-pool_lokasi="{{ $kendaraan->pool_lokasi }}"
                                data-tanggal_stnk="{{ $kendaraan->tanggal_stnk ? \Carbon\Carbon::parse($kendaraan->tanggal_stnk)->format('Y-m-d') : '' }}"
                                data-pajak_tahunan="{{ \Carbon\Carbon::parse($kendaraan->pajak_tahunan)->format('Y-m-d') }}"
                                data-pajak_5_tahunan="{{ $kendaraan->pajak_5_tahunan ? \Carbon\Carbon::parse($kendaraan->pajak_5_tahunan)->format('Y-m-d') : '' }}"
                                data-kir_bengkel="{{ $kendaraan->kir_bengkel ? \Carbon\Carbon::parse($kendaraan->kir_bengkel)->format('Y-m-d') : '' }}"
                                data-foto="{{ $kendaraan->foto_kendaraan ? asset('storage/' . $kendaraan->foto_kendaraan) : '' }}"
                                style="font-size: 11.5px;"
                                title="Edit Data Kendaraan">
                            <i class="bi bi-pencil-square"></i> Edit
                        </button>
                        @endif

                        <button class="btn btn-outline-info btn-sm flex-grow-1 show-qr-btn py-1.5"
                                data-nopol="{{ $kendaraan->nomor_polisi }}"
                                data-merek="{{ $kendaraan->merek }} {{ $kendaraan->tipe }}"
                                data-driver="{{ $kendaraan->nama_driver }}"
                                data-odo="{{ number_format($kendaraan->odometer_terakhir, 0, ',', '.') }} KM"
                                data-status="{{ $kendaraan->status_perawatan }}"
                                style="font-size: 11.5px;"
                                title="Lihat & Cetak Barcode Armada">
                            <i class="bi bi-qr-code"></i> Barcode
                        </button>

                        <button class="btn btn-outline-secondary btn-sm flex-grow-1 update-odo-btn py-1.5"
                                data-id="{{ $kendaraan->id }}"
                                data-nopol="{{ $kendaraan->nomor_polisi }}"
                                data-odo="{{ $kendaraan->odometer_terakhir }}"
                                style="font-size: 11.5px;"
                                title="Perbarui Odometer KM">
                            <i class="bi bi-speedometer2"></i> Odo
                        </button>

                        @if(auth()->user() && auth()->user()->role === 'administrator')
                        <button class="btn btn-outline-danger btn-sm delete-btn px-2.5 py-1.5"
                                data-id="{{ $kendaraan->id }}"
                                data-nopol="{{ $kendaraan->nomor_polisi }}"
                                style="font-size: 11.5px;"
                                title="Hapus Kendaraan">
                            <i class="bi bi-trash"></i>
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center text-muted py-4" id="motor-empty-db">
            <div class="card-premium p-4">
                <i class="bi bi-inbox fs-3 d-block mb-2 text-secondary"></i>
                Belum ada data armada motor.
            </div>
        </div>
        @endforelse
        <div class="col-12 text-center text-muted py-4" id="motor-empty-msg" style="display: none;">
            <div class="card-premium p-4">
                <i class="bi bi-inbox fs-3 d-block mb-2 text-secondary"></i>
                Tidak ada armada motor yang cocok dengan pencarian Anda.
            </div>
        </div>
    </div>
</div>

<!-- Modal: Daftarkan Kendaraan Baru -->
<div class="modal fade" id="addVehicleModal" tabindex="-1" aria-labelledby="addVehicleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="addVehicleModalLabel"><i class="bi bi-truck text-primary me-2"></i> Daftarkan Kendaraan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('kendaraan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">1. IDENTITAS KENDARAAN</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="form-group-premium mb-0">
                                <label class="form-label-premium" for="nomor_polisi">Nomor Polisi (Plat Nomor)</label>
                                <input type="text" name="nomor_polisi" id="nomor_polisi" class="form-control-premium text-uppercase" placeholder="Contoh: B 1234 ABC" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-premium mb-0">
                                <label class="form-label-premium" for="jenis_kendaraan">Jenis Kendaraan</label>
                                <select name="jenis_kendaraan" id="jenis_kendaraan" class="form-control-premium" required>
                                    <option value="Mobil Dinas">Mobil Dinas</option>
                                    <option value="Mobil Boks">Mobil Boks / Barang</option>
                                    <option value="Motor">Motor Operasional</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-premium mb-0">
                                <label class="form-label-premium" for="merek">Merek Kendaraan</label>
                                <input type="text" name="merek" id="merek" class="form-control-premium" placeholder="Contoh: Toyota, Honda, Isuzu" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-premium mb-0">
                                <label class="form-label-premium" for="tipe">Tipe Kendaraan</label>
                                <input type="text" name="tipe" id="tipe" class="form-control-premium" placeholder="Contoh: Avanza 1.3, Traga, PCX" required>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">2. OPERASIONAL & KONDISI AWAL</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="form-group-premium mb-0">
                                <label class="form-label-premium" for="nama_driver">Nama Supir / Penanggung Jawab</label>
                                <input type="text" name="nama_driver" id="nama_driver" class="form-control-premium" placeholder="Nama supir utama" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-premium mb-0">
                                <label class="form-label-premium" for="pool_lokasi">Lokasi Pool / Depo Parkir</label>
                                <input type="text" name="pool_lokasi" id="pool_lokasi" class="form-control-premium" placeholder="Contoh: Pool Jakarta Barat" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group-premium mb-0">
                                <label class="form-label-premium" for="odometer_terakhir">Angka Odometer Terakhir (KM)</label>
                                <input type="number" name="odometer_terakhir" id="odometer_terakhir" class="form-control-premium" min="0" placeholder="Contoh: 45000" required>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">3. LEGALITAS & JATUH TEMPO DOKUMEN</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group-premium mb-0">
                                <label class="form-label-premium" for="tanggal_stnk">Tanggal STNK</label>
                                <input type="date" name="tanggal_stnk" id="tanggal_stnk" class="form-control-premium" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-premium mb-0">
                                <label class="form-label-premium" for="pajak_tahunan">Jatuh Tempo Pajak Tahunan</label>
                                <input type="date" name="pajak_tahunan" id="pajak_tahunan" class="form-control-premium" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-premium mb-0">
                                <label class="form-label-premium" for="pajak_5_tahunan">Jatuh Tempo Pajak 5 Tahunan</label>
                                <input type="date" name="pajak_5_tahunan" id="pajak_5_tahunan" class="form-control-premium" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-premium mb-0">
                                <label class="form-label-premium" for="kir_bengkel">Jatuh Tempo Uji KIR (Opsional)</label>
                                <input type="date" name="kir_bengkel" id="kir_bengkel" class="form-control-premium">
                            </div>
                        </div>
                    </div>
                    <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">4. FOTO KENDARAAN</h6>
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="foto-upload-area" id="add-foto-area" onclick="document.getElementById('foto_kendaraan').click()">
                                <div class="foto-upload-placeholder" id="add-foto-placeholder">
                                    <i class="bi bi-camera-fill fs-2 text-primary opacity-50 d-block mb-2"></i>
                                    <div class="fw-semibold small">Klik atau seret foto kendaraan ke sini</div>
                                    <div class="text-muted" style="font-size:11px;">JPG, PNG, WEBP — Maks. 2MB</div>
                                </div>
                                <img id="add-foto-preview" src="" alt="Preview" style="display:none; width:100%; max-height:200px; object-fit:cover; border-radius:10px;">
                            </div>
                            <input type="file" name="foto_kendaraan" id="foto_kendaraan" accept="image/*" class="d-none" onchange="previewFoto(this, 'add-foto-preview', 'add-foto-placeholder')">
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3">
                    <button type="button" class="btn-premium secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-premium primary">Daftarkan Armada</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit Kendaraan -->
<div class="modal fade" id="editVehicleModal" tabindex="-1" aria-labelledby="editVehicleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="editVehicleModalLabel"><i class="bi bi-pencil-square text-primary me-2"></i> Edit Data Kendaraan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit-vehicle-form" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">1. IDENTITAS KENDARAAN</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="form-group-premium mb-0">
                                <label class="form-label-premium">Nomor Polisi (Plat Nomor)</label>
                                <input type="text" name="nomor_polisi" id="edit_nomor_polisi" class="form-control-premium text-uppercase" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-premium mb-0">
                                <label class="form-label-premium">Jenis Kendaraan</label>
                                <select name="jenis_kendaraan" id="edit_jenis_kendaraan" class="form-control-premium" required>
                                    <option value="Mobil Dinas">Mobil Dinas</option>
                                    <option value="Mobil Boks">Mobil Boks / Barang</option>
                                    <option value="Motor">Motor Operasional</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-premium mb-0">
                                <label class="form-label-premium">Merek Kendaraan</label>
                                <input type="text" name="merek" id="edit_merek" class="form-control-premium" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-premium mb-0">
                                <label class="form-label-premium">Tipe Kendaraan</label>
                                <input type="text" name="tipe" id="edit_tipe" class="form-control-premium" required>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">2. OPERASIONAL</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="form-group-premium mb-0">
                                <label class="form-label-premium">Nama Supir / Penanggung Jawab</label>
                                <input type="text" name="nama_driver" id="edit_nama_driver" class="form-control-premium" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-premium mb-0">
                                <label class="form-label-premium">Lokasi Pool / Depo Parkir</label>
                                <input type="text" name="pool_lokasi" id="edit_pool_lokasi" class="form-control-premium" required>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">3. LEGALITAS & JATUH TEMPO DOKUMEN</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group-premium mb-0">
                                <label class="form-label-premium">Tanggal STNK</label>
                                <input type="date" name="tanggal_stnk" id="edit_tanggal_stnk" class="form-control-premium" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-premium mb-0">
                                <label class="form-label-premium">Jatuh Tempo Pajak Tahunan</label>
                                <input type="date" name="pajak_tahunan" id="edit_pajak_tahunan" class="form-control-premium" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-premium mb-0">
                                <label class="form-label-premium">Jatuh Tempo Pajak 5 Tahunan</label>
                                <input type="date" name="pajak_5_tahunan" id="edit_pajak_5_tahunan" class="form-control-premium" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-premium mb-0">
                                <label class="form-label-premium">Jatuh Tempo Uji KIR (Opsional)</label>
                                <input type="date" name="kir_bengkel" id="edit_kir_bengkel" class="form-control-premium">
                            </div>
                        </div>
                    </div>
                    <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">4. FOTO KENDARAAN</h6>
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="foto-upload-area" id="edit-foto-area" onclick="document.getElementById('edit_foto_kendaraan').click()">
                                <div class="foto-upload-placeholder" id="edit-foto-placeholder">
                                    <i class="bi bi-camera-fill fs-2 text-primary opacity-50 d-block mb-2"></i>
                                    <div class="fw-semibold small">Klik untuk ganti foto kendaraan</div>
                                    <div class="text-muted" style="font-size:11px;">JPG, PNG, WEBP — Maks. 2MB</div>
                                </div>
                                <img id="edit-foto-preview" src="" alt="Preview" style="display:none; width:100%; max-height:200px; object-fit:cover; border-radius:10px;">
                            </div>
                            <input type="file" name="foto_kendaraan" id="edit_foto_kendaraan" accept="image/*" class="d-none" onchange="previewFoto(this, 'edit-foto-preview', 'edit-foto-placeholder')">
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3">
                    <button type="button" class="btn-premium secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-premium primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Perbarui Odometer -->
<div class="modal fade" id="updateOdoModal" tabindex="-1" aria-labelledby="updateOdoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="updateOdoModalLabel"><i class="bi bi-speedometer2 text-primary me-2"></i> Perbarui Odometer KM</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="update-odo-form" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <span class="text-secondary small fw-medium">ARMADA KENDARAAN:</span>
                        <div class="fs-5 fw-bold text-primary mt-1" id="odo-vehicle-nopol">-</div>
                    </div>

                    <div class="form-group-premium mb-0">
                        <label class="form-label-premium" for="new_odometer">Angka Odometer Terbaru (KM)</label>
                        <input type="number" name="odometer_terakhir" id="new_odometer" class="form-control-premium text-primary fw-bold" style="font-size: 1.25rem;" min="0" required>
                        <div class="d-flex flex-wrap align-items-center gap-1.5 mt-2.5">
                            <span class="text-muted small me-1" style="font-size: 11px;"><i class="bi bi-lightning-charge-fill text-warning"></i> Tambah Cepat:</span>
                            <button type="button" class="btn btn-sm btn-outline-primary odo-quick-add py-0.5 px-2" data-add="50" style="font-size: 11px; border-radius: 6px;">+50 KM</button>
                            <button type="button" class="btn btn-sm btn-outline-primary odo-quick-add py-0.5 px-2" data-add="100" style="font-size: 11px; border-radius: 6px;">+100 KM</button>
                            <button type="button" class="btn btn-sm btn-outline-primary odo-quick-add py-0.5 px-2" data-add="500" style="font-size: 11px; border-radius: 6px;">+500 KM</button>
                            <button type="button" class="btn btn-sm btn-outline-primary odo-quick-add py-0.5 px-2" data-add="1000" style="font-size: 11px; border-radius: 6px;">+1.000 KM</button>
                        </div>
                        <div class="text-muted small mt-2" id="odo-current-helper"></div>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3">
                    <button type="button" class="btn-premium secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-premium primary">Simpan Odometer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Konfirmasi Hapus Kendaraan -->
<div class="modal fade" id="deleteVehicleModal" tabindex="-1" aria-labelledby="deleteVehicleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-danger" id="deleteVehicleModalLabel"><i class="bi bi-exclamation-triangle-fill me-2"></i> Hapus Data Armada</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="delete-vehicle-form" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body p-4">
                    <p class="text-secondary mb-2">Apakah Anda yakin ingin menghapus data kendaraan operasional ini?</p>
                    <div class="fs-5 fw-bold text-danger bg-danger-subtle p-3 rounded-3 border border-danger-subtle" id="delete-vehicle-nopol">-</div>
                    <p class="text-muted small mt-3 mb-0">Semua log odometer, KIR, dan info legalitas armada ini akan terhapus secara permanen.</p>
                </div>
                <div class="modal-footer px-4 py-3">
                    <button type="button" class="btn-premium secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-premium danger">Ya, Hapus Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Foto Kendaraan (Lightbox) -->
<div class="modal fade" id="fotoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 bg-transparent shadow-none">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold text-white" id="foto-modal-nopol"></h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-2">
                <img id="foto-modal-img" src="" alt="Foto Kendaraan" style="width:100%; max-height:520px; object-fit:contain; border-radius:12px;">
            </div>
        </div>
    </div>
</div>

<!-- Modal: Barcode / QR Code Armada -->
<div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content border-0 overflow-hidden" style="border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.2);">
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); padding: 20px 24px 16px;">
                <div class="d-flex align-items-center gap-3 w-100">
                    <div style="width:42px;height:42px;border-radius:12px;background:rgba(255,255,255,0.18);display:flex;align-items:center;justify-content:center;font-size:22px;color:#fff;">
                        <i class="bi bi-qr-code-scan"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0" id="qrCodeModalLabel">Barcode / QR Code Armada</h5>
                        <p class="text-white mb-0" style="font-size:11px; opacity:.85;">Identitas digital untuk scan cepat di lokasi pool</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            <div class="modal-body text-center p-4" style="background: var(--bg-secondary);">
                <!-- Nopol Badge -->
                <div class="mb-3">
                    <span class="badge bg-primary fs-6 px-3 py-1.5 rounded-pill shadow-sm" id="qrNopolBadge">B 8686 DV</span>
                    <div class="text-muted small mt-1 fw-semibold" id="qrMerekText">Mitsubishi Pajero Sport 2.4 Dakar</div>
                </div>

                <!-- QR Code Box -->
                <div class="p-3 bg-white rounded-3 d-inline-block border shadow-sm mb-3">
                    <img id="qrCodeImg" src="" alt="QR Code Armada" width="200" height="200" style="display:block; border-radius: 8px;">
                </div>

                <!-- Detail Metadata -->
                <div class="p-2.5 rounded-3 text-start border mb-3" style="background: var(--bg-primary); font-size: 11.5px;">
                    <div class="d-flex justify-content-between mb-1.5">
                        <span class="text-muted">Driver Utama:</span>
                        <strong class="text-dark" id="qrDriverText">-</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1.5">
                        <span class="text-muted">Odometer Terbaru:</span>
                        <strong class="text-dark" id="qrOdoText">-</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Status Kelayakan:</span>
                        <span class="badge bg-success" id="qrStatusBadge">Layak Jalan</span>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm w-100 py-2" onclick="printQrCode()">
                        <i class="bi bi-printer me-1"></i> Cetak Stiker QR
                    </button>
                    <button type="button" class="btn btn-primary btn-sm w-100 py-2" onclick="downloadQrCode()">
                        <i class="bi bi-download me-1"></i> Unduh Barcode
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Scanner Barcode / QR Armada -->
<div class="modal fade" id="scanCameraModal" tabindex="-1" aria-labelledby="scanCameraModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content border-0 overflow-hidden" style="border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.2);">
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 20px 24px 16px;">
                <div class="d-flex align-items-center gap-3 w-100">
                    <div style="width:42px;height:42px;border-radius:12px;background:rgba(255,255,255,0.12);display:flex;align-items:center;justify-content:center;font-size:22px;color:#38bdf8;">
                        <i class="bi bi-camera"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0" id="scanCameraModalLabel">Scan Barcode Kamera</h5>
                        <p class="text-white mb-0" style="font-size:11px; opacity:.8;">Arahkan kamera ke stiker Barcode/QR di mobil</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            <div class="modal-body text-center p-4" style="background: var(--bg-secondary);">
                <!-- Simulated Camera Viewfinder -->
                <div class="position-relative overflow-hidden rounded-3 mb-3 bg-dark d-flex align-items-center justify-content-center" style="height: 200px; border: 2px dashed rgba(56, 189, 248, 0.4);">
                    <div style="position: absolute; width: 130px; height: 130px; border: 2px solid #38bdf8; border-radius: 12px; box-shadow: 0 0 20px rgba(56,189,248,0.5);"></div>
                    <div class="text-white-50 small z-1 d-flex flex-column align-items-center">
                        <i class="bi bi-qr-code-scan fs-1 text-info mb-2"></i>
                        <span>Scanning Kamera Aktif...</span>
                    </div>
                </div>

                <div class="text-start mb-3">
                    <label class="form-label small text-muted">Atau Pilih Nopol Armada untuk Cek Barcode:</label>
                    <select class="form-select form-select-sm" id="scannerSelectNopol">
                        <option value="">-- Pilih Kendaraan --</option>
                        @foreach($mobils->concat($motors) as $vk)
                            <option value="{{ $vk->nomor_polisi }}">{{ $vk->nomor_polisi }} - {{ $vk->merek }} {{ $vk->tipe }} ({{ $vk->nama_driver }})</option>
                        @endforeach
                    </select>
                </div>

                <button type="button" class="btn btn-info text-white w-100 py-2 fw-semibold" onclick="simulateScanResult()">
                    <i class="bi bi-search me-1"></i> Buka Informasi Armada Ini
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
.premium-filter-pills {
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 4px;
    display: inline-flex;
    gap: 4px;
    box-shadow: var(--shadow-xs);
}
.filter-pill-btn {
    border: none;
    background: transparent;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-secondary);
    transition: var(--transition-fast);
    display: inline-flex;
    align-items: center;
}
.filter-pill-btn:hover {
    color: var(--accent);
    background-color: var(--accent-subtle);
}
.filter-pill-btn.active {
    background: var(--accent-gradient);
    color: #ffffff !important;
    box-shadow: 0 4px 10px rgba(79, 70, 229, 0.15);
}
[data-theme="dark"] .filter-pill-btn.active {
    color: #ffffff !important;
}

.foto-upload-area {
    border: 2px dashed #a5b4fc;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: border-color 0.2s, background 0.2s;
    background: var(--bg-secondary, #f8fafc);
    min-height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.foto-upload-area:hover {
    border-color: #6366f1;
    background: #eef2ff;
}
#fotoModal .modal-content {
    background: rgba(0,0,0,0.85) !important;
}
.vehicle-row {
    transition: var(--transition);
}
.vehicle-row:hover {
    transform: translateY(-6px);
}
.vehicle-row:hover .card-premium {
    box-shadow: var(--shadow-lg) !important;
    border-color: var(--accent) !important;
}
.vehicle-thumb:hover {
    transform: scale(1.03);
}
/* Dark Mode Readability Overrides for Cards */
[data-theme="dark"] .card-premium .text-muted {
    color: var(--text-secondary) !important;
}
[data-theme="dark"] .card-premium .text-dark {
    color: var(--text-primary) !important;
}
[data-theme="dark"] .card-premium .text-secondary {
    color: var(--text-secondary) !important;
}
[data-theme="dark"] .card-premium hr {
    opacity: 0.15 !important;
    background-color: var(--text-primary) !important;
}
</style>
<script>
    function previewFoto(input, previewId, placeholderId) {
        const preview = document.getElementById(previewId);
        const placeholder = document.getElementById(placeholderId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                if (placeholder) placeholder.style.display = 'none';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Foto Modal (lightbox)
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.vehicle-thumb').forEach(img => {
            img.addEventListener('click', function() {
                document.getElementById('foto-modal-img').src = this.getAttribute('data-foto');
                document.getElementById('foto-modal-nopol').textContent = this.getAttribute('data-nopol');
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('action') === 'add') {
            const addVehicleModal = new bootstrap.Modal(document.getElementById('addVehicleModal'));
            addVehicleModal.show();
        }

        const searchInput = document.getElementById('vehicle-search');
        const rows = document.querySelectorAll('.vehicle-row');
        const rowCountBadge = document.getElementById('row-count');
        const filterBtns = document.querySelectorAll('.filter-pill-btn');
        let currentFilter = 'all';

        function updateFilters() {
            const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
            let visibleMobil = 0;
            let visibleMotor = 0;

            rows.forEach(row => {
                const nopol = row.querySelector('.vehicle-nopol').textContent.toLowerCase();
                const detail = row.querySelector('.vehicle-detail').textContent.toLowerCase();
                const driver = row.querySelector('.vehicle-driver').textContent.toLowerCase();
                const jenis = row.getAttribute('data-jenis') ? row.getAttribute('data-jenis').toLowerCase() : '';

                // Text search match
                const matchesSearch = nopol.includes(query) || detail.includes(query) || driver.includes(query);

                // Check if it is a motor
                const isMotor = jenis.includes('motor') || jenis.includes('sepeda');

                // Tab filter match
                let matchesFilter = false;
                const statusBadge = row.getAttribute('data-status-badge') || '';
                if (currentFilter === 'all') {
                    matchesFilter = true;
                } else if (currentFilter === 'mobil') {
                    matchesFilter = !isMotor;
                } else if (currentFilter === 'motor') {
                    matchesFilter = isMotor;
                } else if (currentFilter === 'warning') {
                    matchesFilter = statusBadge === 'danger' || statusBadge === 'warning';
                } else if (currentFilter === 'baik') {
                    matchesFilter = statusBadge === 'success';
                }

                if (matchesSearch && matchesFilter) {
                    row.style.display = '';
                    if (isMotor) {
                        visibleMotor++;
                    } else {
                        visibleMobil++;
                    }
                } else {
                    row.style.display = 'none';
                }
            });

            // Update row counts per category
            const mobilRowCount = document.getElementById('mobil-row-count');
            if (mobilRowCount) {
                mobilRowCount.textContent = `Menampilkan ${visibleMobil} armada`;
            }
            const motorRowCount = document.getElementById('motor-row-count');
            if (motorRowCount) {
                motorRowCount.textContent = `Menampilkan ${visibleMotor} armada`;
            }

            // Hide/show the section containers completely based on filter pills selection
            const mobilHeader = document.getElementById('mobil-section-header');
            const mobilGrid = document.getElementById('mobil-grid');
            const motorHeader = document.getElementById('motor-section-header');
            const motorGrid = document.getElementById('motor-grid');

            if (currentFilter === 'motor') {
                if (mobilHeader) mobilHeader.style.display = 'none';
                if (mobilGrid) mobilGrid.style.display = 'none';
            } else {
                if (mobilHeader) mobilHeader.style.display = 'flex';
                if (mobilGrid) mobilGrid.style.display = 'flex';
            }

            if (currentFilter === 'mobil') {
                if (motorHeader) motorHeader.style.display = 'none';
                if (motorGrid) motorGrid.style.display = 'none';
            } else {
                if (motorHeader) motorHeader.style.display = 'flex';
                if (motorGrid) motorGrid.style.display = 'flex';
            }

            // Show/hide empty search message inside grids if search yielded 0
            const mobilEmptyMsg = document.getElementById('mobil-empty-msg');
            const mobilEmptyDb = document.getElementById('mobil-empty-db');
            if (mobilEmptyMsg) {
                mobilEmptyMsg.style.display = (visibleMobil === 0 && !mobilEmptyDb && (currentFilter === 'all' || currentFilter === 'mobil') && query !== '') ? 'block' : 'none';
            }

            const motorEmptyMsg = document.getElementById('motor-empty-msg');
            const motorEmptyDb = document.getElementById('motor-empty-db');
            if (motorEmptyMsg) {
                motorEmptyMsg.style.display = (visibleMotor === 0 && !motorEmptyDb && (currentFilter === 'all' || currentFilter === 'motor') && query !== '') ? 'block' : 'none';
            }

            // Global count badge
            if (rowCountBadge) {
                rowCountBadge.textContent = `Menampilkan ${visibleMobil + visibleMotor} armada`;
            }
        }

        if (searchInput) {
            searchInput.addEventListener('input', updateFilters);
        }

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentFilter = this.getAttribute('data-filter');
                updateFilters();
            });
        });

        const editBtns = document.querySelectorAll('.edit-btn');
        const editVehicleModalEl = document.getElementById('editVehicleModal');
        const editVehicleModal = new bootstrap.Modal(editVehicleModalEl);
        const editForm = document.getElementById('edit-vehicle-form');

        editBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');

                document.getElementById('edit_nomor_polisi').value = this.getAttribute('data-nomor_polisi');
                document.getElementById('edit_jenis_kendaraan').value = this.getAttribute('data-jenis_kendaraan');
                document.getElementById('edit_merek').value = this.getAttribute('data-merek');
                document.getElementById('edit_tipe').value = this.getAttribute('data-tipe');
                document.getElementById('edit_nama_driver').value = this.getAttribute('data-nama_driver');
                document.getElementById('edit_pool_lokasi').value = this.getAttribute('data-pool_lokasi');
                document.getElementById('edit_tanggal_stnk').value = this.getAttribute('data-tanggal_stnk');
                document.getElementById('edit_pajak_tahunan').value = this.getAttribute('data-pajak_tahunan');
                document.getElementById('edit_pajak_5_tahunan').value = this.getAttribute('data-pajak_5_tahunan');
                document.getElementById('edit_kir_bengkel').value = this.getAttribute('data-kir_bengkel');

                editForm.action = `/kendaraan/${id}`;

                editVehicleModal.show();
            });
        });

        const updateOdoBtns = document.querySelectorAll('.update-odo-btn');
        const updateOdoModalEl = document.getElementById('updateOdoModal');
        const updateOdoModal = new bootstrap.Modal(updateOdoModalEl);
        const odoForm = document.getElementById('update-odo-form');
        const odoVehicleNopol = document.getElementById('odo-vehicle-nopol');
        const newOdometerInput = document.getElementById('new_odometer');
        const odoCurrentHelper = document.getElementById('odo-current-helper');

        let baseOdoVal = 0;
        updateOdoBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nopol = this.getAttribute('data-nopol');
                const currentOdo = Number(this.getAttribute('data-odo')) || 0;
                baseOdoVal = currentOdo;

                odoVehicleNopol.textContent = nopol;
                newOdometerInput.value = currentOdo;
                newOdometerInput.min = currentOdo;
                odoCurrentHelper.textContent = `Odometer awal: ${currentOdo.toLocaleString('id-ID')} KM. Gunakan tombol Tambah Cepat atau ubah nilai secara manual.`;

                odoForm.action = `/kendaraan/${id}/odometer`;

                updateOdoModal.show();
            });
        });

        document.querySelectorAll('.odo-quick-add').forEach(qBtn => {
            qBtn.addEventListener('click', function() {
                const addVal = Number(this.getAttribute('data-add')) || 0;
                const currentInputVal = Number(newOdometerInput.value) || baseOdoVal;
                newOdometerInput.value = currentInputVal + addVal;
            });
        });

        const deleteBtns = document.querySelectorAll('.delete-btn');
        const deleteVehicleModalEl = document.getElementById('deleteVehicleModal');
        const deleteVehicleModal = new bootstrap.Modal(deleteVehicleModalEl);
        const deleteForm = document.getElementById('delete-vehicle-form');
        const deleteVehicleNopol = document.getElementById('delete-vehicle-nopol');

        deleteBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nopol = this.getAttribute('data-nopol');

                deleteVehicleNopol.textContent = nopol;

                deleteForm.action = `/kendaraan/${id}`;

                deleteVehicleModal.show();
            });
        });

        // --- Barcode / QR Code Modal Handler ---
        const showQrBtns = document.querySelectorAll('.show-qr-btn');
        const qrCodeModalEl = document.getElementById('qrCodeModal');
        const qrCodeModal = new bootstrap.Modal(qrCodeModalEl);

        showQrBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const nopol = this.getAttribute('data-nopol') || 'ARMADA';
                const merek = this.getAttribute('data-merek') || '-';
                const driver = this.getAttribute('data-driver') || 'Belum Ditentukan';
                const odo = this.getAttribute('data-odo') || '0 KM';
                const status = this.getAttribute('data-status') || 'Aman (Hijau)';

                document.getElementById('qrNopolBadge').textContent = nopol;
                document.getElementById('qrMerekText').textContent = merek;
                document.getElementById('qrDriverText').textContent = driver;
                document.getElementById('qrOdoText').textContent = odo;
                document.getElementById('qrStatusBadge').textContent = status;

                const qrPayload = encodeURIComponent(`MAINTASSET-VEHICLE-${nopol.replace(/\s+/g, '')}`);
                document.getElementById('qrCodeImg').src = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&margin=2&ecc=M&data=${qrPayload}`;

                qrCodeModal.show();
            });
        });
    });

    // --- Global Barcode Helper Functions ---
    function printQrCode() {
        const nopol = document.getElementById('qrNopolBadge').textContent;
        const merek = document.getElementById('qrMerekText').textContent;
        const driver = document.getElementById('qrDriverText').textContent;
        const odo = document.getElementById('qrOdoText').textContent;
        const qrSrc = document.getElementById('qrCodeImg').src;

        const printWin = window.open('', '_blank', 'width=520,height=640');
        printWin.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Stiker Barcode Armada - ${nopol}</title>
                <style>
                    body { font-family: 'Plus Jakarta Sans', sans-serif; text-align: center; padding: 24px; background: #fff; }
                    .sticker { border: 2.5px dashed #2563eb; border-radius: 20px; padding: 24px; max-width: 320px; margin: 0 auto; background: #f8fafc; }
                    .header { font-weight: 800; color: #2563eb; font-size: 16px; margin-bottom: 4px; letter-spacing: -0.02em; }
                    .nopol { font-size: 26px; font-weight: 800; background: #2563eb; color: #fff; padding: 6px 20px; border-radius: 24px; display: inline-block; margin: 12px 0; box-shadow: 0 4px 12px rgba(37,99,235,0.3); }
                    .merek { font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 14px; }
                    img { border-radius: 12px; border: 1px solid #cbd5e1; padding: 10px; background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
                    .footer { font-size: 11px; color: #64748b; margin-top: 14px; border-top: 1px solid #e2e8f0; padding-top: 10px; }
                </style>
            </head>
            <body onload="window.print(); window.close();">
                <div class="sticker">
                    <div class="header">⚡ MAINTASSET FLEET SYSTEM</div>
                    <div class="nopol">${nopol}</div>
                    <div class="merek">${merek}</div>
                    <img src="${qrSrc}" width="190" height="190">
                    <div class="footer">Driver: <strong>${driver}</strong><br>Odometer: ${odo}</div>
                </div>
            </body>
            </html>
        `);
        printWin.document.close();
    }

    function downloadQrCode() {
        const nopol = document.getElementById('qrNopolBadge').textContent.trim();
        const qrSrc = document.getElementById('qrCodeImg').src;
        
        fetch(qrSrc)
            .then(res => res.blob())
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `Barcode_Armada_${nopol.replace(/\s+/g, '_')}.png`;
                document.body.appendChild(a);
                a.click();
                a.remove();
            });
    }

    function simulateScanResult() {
        const select = document.getElementById('scannerSelectNopol');
        const selectedNopol = select ? select.value : '';
        const modalEl = document.getElementById('scanCameraModal');
        
        if (modalEl) {
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        }

        if (selectedNopol) {
            const rows = document.querySelectorAll('.vehicle-row');
            let found = null;
            rows.forEach(r => {
                const text = r.textContent || '';
                if (text.includes(selectedNopol)) {
                    found = r;
                }
            });

            if (found) {
                found.scrollIntoView({ behavior: 'smooth', block: 'center' });
                found.style.transition = 'all 0.5s';
                found.style.transform = 'scale(1.04)';
                found.style.boxShadow = '0 0 0 4px #2563eb';
                setTimeout(() => {
                    found.style.transform = '';
                    found.style.boxShadow = '';
                }, 2500);
            }
        }
    }
</script>
@endsection