@extends('layouts.app')

@section('title', 'Laporan Biaya & Pembayaran')
@section('page_title', 'Laporan Biaya & Pembayaran')
@section('page_subtitle', 'Pantau pengeluaran biaya perbaikan, operasional, dan klaim pembayaran kendaraan.')

@section('content')
@php
    $vehiclesJsArray = [];
    foreach($kendaraans as $k) {
        $stnkDate = $k->tanggal_stnk ? \Carbon\Carbon::parse($k->tanggal_stnk) : null;
        $pajakDate = $k->pajak_tahunan ? \Carbon\Carbon::parse($k->pajak_tahunan) : null;
        $pajak5Date = $k->pajak_5_tahunan ? \Carbon\Carbon::parse($k->pajak_5_tahunan) : null;
        $kirDate = $k->kir_bengkel ? \Carbon\Carbon::parse($k->kir_bengkel) : null;

        $isStnkOverdue = $stnkDate ? $stnkDate->isPast() : false;
        $isStnkNear = $stnkDate && !$isStnkOverdue && $stnkDate->diffInDays(now()) <= 30;

        $isPajakOverdue = $pajakDate ? $pajakDate->isPast() : false;
        $isPajakNear = $pajakDate && !$isPajakOverdue && $pajakDate->diffInDays(now()) <= 30;

        $isPajak5Overdue = $pajak5Date ? $pajak5Date->isPast() : false;
        $isPajak5Near = $pajak5Date && !$isPajak5Overdue && $pajak5Date->diffInDays(now()) <= 30;

        $isKirOverdue = $kirDate ? $kirDate->isPast() : false;
        $isKirNear = $kirDate && !$isKirOverdue && $kirDate->diffInDays(now()) <= 30;

        $isOdoTinggi = $k->odometer_terakhir >= 100000;

        $isOverdueAny = $isStnkOverdue || $isPajakOverdue || $isPajak5Overdue || $isKirOverdue || $isOdoTinggi;
        $isNearAny = $isStnkNear || $isPajakNear || $isPajak5Near || $isKirNear;

        $cat = 'aman';
        $badgeText = 'Aman & Layak Jalan';
        $badgeClass = 'bg-success text-white';
        if ($isOverdueAny) {
            $cat = 'terlambat';
            $badgeText = 'Terlambat / Perlu Servis';
            $badgeClass = 'bg-danger text-white';
        } elseif ($isNearAny) {
            $cat = 'mendekati';
            $badgeText = 'Mendekati Jatuh Tempo';
            $badgeClass = 'bg-warning text-dark';
        }

        $vehiclesJsArray[] = [
            'nopol' => $k->nomor_polisi,
            'merek_tipe' => $k->merek . ' ' . $k->tipe,
            'jenis' => $k->jenis_kendaraan,
            'driver' => $k->nama_driver,
            'pool' => $k->pool_lokasi,
            'odometer' => number_format($k->odometer_terakhir, 0, ',', '.') . ' KM',
            'cat' => $cat,
            'badgeText' => $badgeText,
            'badgeClass' => $badgeClass
        ];
    }
@endphp

<div class="container-fluid p-0">

    {{-- Notifikasi sukses / error --}}
    @if(session('success'))
        <div class="alert-premium success mb-4" role="alert">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="alert-premium danger mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <!-- Form Klaim Biaya Operasional (Reimburse) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-premium p-3.5 position-relative overflow-hidden" style="background: rgba(37, 99, 235, 0.04); border: 1px solid rgba(37, 99, 235, 0.18); border-radius: 16px;">
                <div class="d-flex align-items-center mb-3 gap-2.5">
                    <div class="stat-icon-wrapper indigo" style="width: 38px; height: 38px; font-size: 18px;">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-primary mb-0" style="font-size: 1.05rem;">Ajukan Klaim Biaya Operasional</h5>
                        <p class="text-secondary small mb-0" style="font-size: 11.5px;">Isi formulir di bawah untuk pengajuan klaim BBM, e-Toll, parkir, atau servis armada.</p>
                    </div>
                </div>

                {{-- Info Banner: Semua klaim perlu persetujuan admin --}}
                <div class="d-flex align-items-center gap-2.5 p-2.5 rounded-3 mb-3" style="background: rgba(37, 99, 235, 0.08); border: 1px solid rgba(37, 99, 235, 0.25);">
                    <i class="bi bi-info-circle-fill text-primary fs-5 flex-shrink-0"></i>
                    <div class="small" style="font-size: 12px; color: var(--text-primary);">
                        <strong class="text-primary me-1">Perlu Persetujuan Admin:</strong> Semua pengajuan klaim akan masuk status <span class="badge bg-warning text-dark px-2 py-0.5" style="font-size:10px;">Pending</span> dan wajib disetujui oleh Administrator sebelum dicatat sebagai pengeluaran resmi.
                    </div>
                </div>

                <form action="{{ route('pembayaran.store') }}" method="POST" class="p-3 rounded-3 text-dark border" style="background-color: var(--bg-secondary); border-color: var(--border-color) !important;">
                    @csrf

                    @if($errors->any())
                        <div class="alert-premium danger mb-3" role="alert">
                            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                            <ul class="mb-0 ps-3 small">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group-premium mb-0">
                                <label class="form-label-premium">Armada Kendaraan</label>
                                <select name="kendaraan_id" class="form-control-premium" required>
                                    <option value="">-- Pilih Kendaraan --</option>
                                    @foreach($kendaraans as $k)
                                        <option value="{{ $k->id }}">{{ $k->nomor_polisi }} - {{ $k->merek }} {{ $k->tipe }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-premium mb-0">
                                <label class="form-label-premium">Jenis Biaya</label>
                                <select name="jenis_biaya" class="form-control-premium" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="Bahan Bakar">Bahan Bakar (BBM)</option>
                                    <option value="Tol & Parkir">Tol & Parkir</option>
                                    <option value="Servis Ringan">Servis Ringan (Tambal Ban, Cuci, dll)</option>
                                    <option value="Biaya Servis Rutin">Biaya Servis Rutin</option>
                                    <option value="Biaya Pajak & Surat">Biaya Pajak & Surat</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group-premium mb-0">
                                <label class="form-label-premium">Tanggal Transaksi</label>
                                <input type="date" name="tanggal_pembayaran" class="form-control-premium" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group-premium mb-0">
                                <label class="form-label-premium">Jumlah Nominal (Rp)</label>
                                <input type="number" name="jumlah" class="form-control-premium" min="0" placeholder="Misal: 50000" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group-premium mb-0">
                                <label class="form-label-premium">Metode Pembayaran</label>
                                <select name="metode_pembayaran" id="metodePembayaran" class="form-control-premium" required>
                                    <option value="">-- Pilih Metode --</option>
                                    <option value="transfer">🏦 Transfer Bank</option>
                                    <option value="qris">📱 QRIS</option>
                                    <option value="tunai">💵 Tunai</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group-premium mb-0">
                                <label class="form-label-premium">Keterangan / Bukti Nota</label>
                                <input type="text" name="keterangan" class="form-control-premium" placeholder="Tuliskan keterangan singkat">
                            </div>
                        </div>

                        {{-- Info QRIS & DANA dinamis --}}
                        <div class="col-12" id="infoQris" style="display:none;">
                            <div class="d-flex align-items-center gap-2 p-2 rounded-3" style="background: rgba(16, 185, 129, 0.08); border: 1px dashed rgba(16, 185, 129, 0.35); font-size: 12.5px;">
                                <i class="bi bi-wallet2 text-success fs-5"></i>
                                <span class="text-secondary">Pembayaran dapat discan via <strong>QRIS</strong> atau transfer <strong>DANA: 085840951519 (a.n. Nazwa Salsabila)</strong> setelah pengajuan disetujui Admin.</span>
                            </div>
                        </div>

                        <div class="col-12 text-end mt-2">
                            <button type="submit" class="btn-premium primary px-4 py-2">
                                <i class="bi bi-send-check me-1"></i> Kirim Pengajuan Klaim
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Charts Row: Side-by-Side integration of Cost and Roadworthiness charts -->
    <div class="row g-4 mb-4">
        @if(auth()->user() && auth()->user()->role !== 'driver')
        <!-- Chart 1: Analisis Biaya (Admin & Teknisi Only) -->
        <div class="col-lg-6">
            <div class="card-premium p-4 h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="fw-bold mb-1" style="font-size: 1.05rem;"><i class="bi bi-graph-up-arrow text-primary me-2"></i>Diagram Analisis Biaya</h5>
                        <p class="text-secondary small mb-0" style="font-size: 11px;">Rincian tren pengeluaran perbaikan & operasional per unit armada.</p>
                    </div>
                    @if(count($chartLabels) > 0)
                    <div class="text-end">
                        <span class="badge-premium indigo">Total Pengeluaran</span>
                        <div class="fw-bold text-primary fs-5 mt-1" id="totalBiayaLabel">Rp 0</div>
                    </div>
                    @endif
                </div>

                <div style="position: relative; height: 280px; width: 100%;">
                    <!-- Ambient Glow Behind Chart -->
                    <div style="position: absolute; inset: 0; background: radial-gradient(circle, rgba(37, 99, 235, 0.08) 0%, transparent 75%); filter: blur(15px); pointer-events: none; z-index: 1;"></div>
                    @if(count($chartLabels) > 0)
                        <canvas id="biayaChart" style="position: relative; z-index: 2;"></canvas>
                    @else
                        <div class="d-flex justify-content-center align-items-center h-100 text-muted small" style="position: relative; z-index: 2;">
                            Belum ada data klaim biaya terdaftar.
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Chart 2: Kelayakan Armada -->
        <div class="{{ (auth()->user() && auth()->user()->role === 'driver') ? 'col-12' : 'col-lg-6' }}">
            <div class="card-premium p-4 h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="fw-bold mb-1" style="font-size: 1.05rem;"><i class="bi bi-pie-chart-fill text-success me-2"></i>Distribusi Kelayakan Armada</h5>
                        <p class="text-secondary small mb-0" style="font-size: 11px;">Klik grafik atau status di bawah untuk melihat rincian mobil.</p>
                    </div>
                    <div>
                        <span class="badge-premium emerald"><i class="bi bi-truck me-1"></i> {{ $totalArmada }} Unit</span>
                    </div>
                </div>

                <div class="row align-items-center g-3">
                    <div class="col-sm-5 position-relative d-flex justify-content-center">
                        <div style="position: relative; height: 180px; width: 180px; cursor: pointer;" title="Klik bagian lingkaran untuk filter mobil">
                            <div style="position: absolute; inset: 0; border-radius: 50%; background: radial-gradient(circle, rgba(16, 185, 129, 0.08) 0%, transparent 75%); filter: blur(10px); pointer-events: none;"></div>
                            @if($totalArmada > 0)
                                <canvas id="statusArmadaChart" style="position: relative; z-index: 2;"></canvas>
                                <div class="position-absolute top-50 start-50 translate-middle text-center pointer-events-none d-flex flex-column align-items-center justify-content-center" 
                                     style="z-index: 3; width: 95px; height: 95px; border-radius: 50%; background: var(--bg-secondary); border: 1.5px solid var(--border-color); box-shadow: var(--shadow-sm);">
                                    <div class="fs-4 fw-bold text-primary" style="line-height: 1; font-family: 'Outfit', sans-serif;">{{ $totalArmada }}</div>
                                    <div class="text-muted fw-bold mt-0.5" style="font-size: 7.5px; letter-spacing: 0.05em; text-transform: uppercase;">Total Unit</div>
                                </div>
                            @else
                                <div class="d-flex justify-content-center align-items-center h-100 text-muted small">
                                    Belum ada data armada.
                                </div>
                            @endif
                        </div>
                    </div>
                                      <div class="col-sm-7 d-flex flex-column gap-2" style="user-select: none; -webkit-user-select: none;">
                        <div class="d-flex align-items-center justify-content-between p-2.5 rounded-3 border status-click-card" 
                             onclick="showVehicleCategoryModal('aman')" 
                             style="font-size: 11px; background: rgba(16, 185, 129, 0.06); border-color: rgba(16, 185, 129, 0.25) !important; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;"
                             title="Klik untuk lihat daftar mobil Aman & Layak">
                            <span class="fw-bold text-success"><i class="bi bi-shield-check me-1.5"></i>Aman & Layak</span>
                            <span class="badge bg-success text-white px-2.5 py-1 rounded-pill fw-bold" style="font-size: 10px;">{{ $jumlahAman }} unit <i class="bi bi-chevron-right ms-0.5"></i></span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between p-2.5 rounded-3 border status-click-card" 
                             onclick="showVehicleCategoryModal('mendekati')" 
                             style="font-size: 11px; background: rgba(245, 158, 11, 0.06); border-color: rgba(245, 158, 11, 0.25) !important; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;"
                             title="Klik untuk lihat daftar mobil Mendekati Jatuh Tempo">
                            <span class="fw-bold text-warning"><i class="bi bi-clock-history me-1.5"></i>Mendekati</span>
                            <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill fw-bold" style="font-size: 10px;">{{ $jumlahMendekati }} unit <i class="bi bi-chevron-right ms-0.5"></i></span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between p-2.5 rounded-3 border status-click-card" 
                             onclick="showVehicleCategoryModal('terlambat')" 
                             style="font-size: 11px; background: rgba(239, 68, 68, 0.06); border-color: rgba(239, 68, 68, 0.25) !important; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;"
                             title="Klik untuk lihat daftar mobil Terlambat / Perlu Servis">
                            <span class="fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill me-1.5"></i>Terlambat / Perlu Servis</span>
                            <span class="badge bg-danger text-white px-2.5 py-1 rounded-pill fw-bold" style="font-size: 10px;">{{ $jumlahJatuhTempo }} unit <i class="bi bi-chevron-right ms-0.5"></i></span>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>

    <!-- Tabs Navigation for Tables -->
    <ul class="nav nav-pills gap-2 mb-4" id="pembayaranTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="filter-pill-btn active" id="biaya-tab" data-bs-toggle="pill" data-bs-target="#biaya-pane" type="button" role="tab" aria-controls="biaya-pane" aria-selected="true">
                <i class="bi bi-receipt-cutoff me-1.5"></i> Riwayat Transaksi & Reimburse
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="filter-pill-btn" id="status-tab" data-bs-toggle="pill" data-bs-target="#status-pane" type="button" role="tab" aria-controls="status-pane" aria-selected="false">
                <i class="bi bi-shield-check me-1.5"></i> Kelayakan & Status Dokumen
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="pembayaranTabContent">
        <!-- Tab 1: Riwayat Transaksi -->
        <div class="tab-pane fade show active" id="biaya-pane" role="tabpanel" aria-labelledby="biaya-tab" tabindex="0">
            <div class="card-premium p-0 overflow-hidden">
                <div class="p-4 border-bottom">
                    <h5 class="fw-bold mb-0">Daftar Rincian Laporan Biaya & Reimburse</h5>
                </div>
                <div class="table-responsive-premium border-0">
                    <table class="table-premium" id="tabelPembayaran">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kendaraan (Nopol)</th>
                                <th>Jenis Biaya</th>
                                <th>Metode Bayar</th>
                                <th>Keterangan</th>
                                <th>Jumlah Nominal</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pembayarans as $pembayaran)
                            @php
                                $k = $pembayaran->kendaraan;
                                $statusBadgeHtml = '';
                                if ($k) {
                                    $stnkDate = $k->tanggal_stnk ? \Carbon\Carbon::parse($k->tanggal_stnk) : null;
                                    $pajakDate = $k->pajak_tahunan ? \Carbon\Carbon::parse($k->pajak_tahunan) : null;
                                    $pajak5Date = $k->pajak_5_tahunan ? \Carbon\Carbon::parse($k->pajak_5_tahunan) : null;
                                    $kirDate = $k->kir_bengkel ? \Carbon\Carbon::parse($k->kir_bengkel) : null;

                                    $isStnkOverdue = $stnkDate ? $stnkDate->isPast() : false;
                                    $isStnkNear = $stnkDate && !$isStnkOverdue && $stnkDate->diffInDays(now()) <= 30;
                                    $isPajakOverdue = $pajakDate ? $pajakDate->isPast() : false;
                                    $isPajakNear = $pajakDate && !$isPajakOverdue && $pajakDate->diffInDays(now()) <= 30;
                                    $isPajak5Overdue = $pajak5Date ? $pajak5Date->isPast() : false;
                                    $isPajak5Near = $pajak5Date && !$isPajak5Overdue && $pajak5Date->diffInDays(now()) <= 30;
                                    $isKirOverdue = $kirDate ? $kirDate->isPast() : false;
                                    $isKirNear = $kirDate && !$isKirOverdue && $kirDate->diffInDays(now()) <= 30;
                                    $isOdoTinggi = $k->odometer_terakhir >= 100000;
                                    $isOverdue = $isStnkOverdue || $isPajakOverdue || $isPajak5Overdue || $isKirOverdue || $isOdoTinggi;
                                    $isNear = $isStnkNear || $isPajakNear || $isPajak5Near || $isKirNear;

                                    if ($isOverdue) {
                                        $statusBadgeHtml = '<span class="badge bg-danger text-white rounded-pill px-2" style="font-size: 9px; font-weight: 700; display: inline-block; margin-top: 3px;"><i class="bi bi-exclamation-triangle-fill"></i> Perlu Servis</span>';
                                    } elseif ($isNear) {
                                        $statusBadgeHtml = '<span class="badge bg-warning text-dark rounded-pill px-2" style="font-size: 9px; font-weight: 700; display: inline-block; margin-top: 3px;"><i class="bi bi-clock-history"></i> Jatuh Tempo</span>';
                                    } else {
                                        $statusBadgeHtml = '<span class="badge bg-success text-white rounded-pill px-2" style="font-size: 9px; font-weight: 700; display: inline-block; margin-top: 3px;"><i class="bi bi-check-circle-fill"></i> Layak</span>';
                                    }
                                }
                                $metodeIcon = match($pembayaran->metode_pembayaran ?? 'transfer') {
                                    'qris'     => ['icon' => 'bi-qr-code',           'label' => 'QRIS',     'class' => 'indigo'],
                                    'tunai'    => ['icon' => 'bi-cash-coin',         'label' => 'Tunai',    'class' => 'emerald'],
                                    default    => ['icon' => 'bi-bank2',             'label' => 'Transfer', 'class' => 'info'],
                                };
                            @endphp
                            <tr>
                                <td class="small text-muted">{{ \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d M Y') }}</td>
                                <td>
                                    <div class="fw-bold text-primary">{{ $k ? $k->nomor_polisi : '-' }}</div>
                                    {!! $statusBadgeHtml !!}
                                </td>
                                <td>
                                    <span class="badge-premium indigo">{{ $pembayaran->jenis_biaya }}</span>
                                </td>
                                <td>
                                    @if(($pembayaran->metode_pembayaran ?? '') === 'qris')
                                        <button type="button" class="btn-premium indigo border-0 py-1 px-2.5" style="font-size: 11px; cursor: pointer;"
                                                onclick="openQrisModal('{{ number_format($pembayaran->jumlah, 0, ',', '.') }}', '{{ $k ? $k->nomor_polisi : '-' }}', '{{ $pembayaran->jenis_biaya }}', {{ $pembayaran->jumlah }})"
                                                title="Klik untuk tampilkan QR Code QRIS">
                                            <i class="bi bi-qr-code me-1"></i>QRIS <i class="bi bi-box-arrow-up-right ms-1 opacity-75" style="font-size: 9px;"></i>
                                        </button>
                                    @else
                                        <span class="badge-premium {{ $metodeIcon['class'] }}" style="font-size: 11px;">
                                            <i class="bi {{ $metodeIcon['icon'] }} me-1"></i>{{ $metodeIcon['label'] }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-secondary small">{{ $pembayaran->keterangan ?? '-' }}</td>
                                <td class="fw-bold text-dark">Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</td>
                                <td>
                                    @if($pembayaran->status === 'disetujui')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 fw-bold" style="font-size:11px;"><i class="bi bi-check-circle me-1"></i>Disetujui</span>
                                    @elseif($pembayaran->status === 'ditolak')
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1 fw-bold" style="font-size:11px;"><i class="bi bi-x-circle me-1"></i>Ditolak</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1 fw-bold" style="font-size:11px;"><i class="bi bi-hourglass-split me-1"></i>Pending</span>
                                    @endif
                                </td>
                                <td style="width: 180px;">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        @if(auth()->user() && auth()->user()->role === 'administrator' && $pembayaran->status === 'pending')
                                            {{-- Tombol Setujui --}}
                                            <form action="{{ route('pembayaran.approve', $pembayaran->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn-premium success btn-sm px-2 py-1"
                                                    title="Setujui Klaim"
                                                    style="font-size: 11px; display:inline-flex; align-items:center; gap:4px;">
                                                    <i class="bi bi-check-lg"></i> Setuju
                                                </button>
                                            </form>
                                            {{-- Tombol Tolak --}}
                                            <form action="{{ route('pembayaran.reject', $pembayaran->id) }}" method="POST" class="d-inline"
                                                  data-confirm="Yakin ingin menolak klaim ini?">
                                                @csrf
                                                <button type="submit" class="btn-premium danger btn-sm px-2 py-1"
                                                    title="Tolak Klaim"
                                                    style="font-size: 11px; display:inline-flex; align-items:center; gap:4px;">
                                                    <i class="bi bi-x-lg"></i> Tolak
                                                </button>
                                            </form>
                                        @endif

                                        @if($pembayaran->status === 'pending' || (auth()->user() && auth()->user()->role === 'administrator'))
                                            <a href="{{ route('pembayaran.edit', $pembayaran->id) }}" class="btn-premium secondary btn-sm p-1" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="{{ route('pembayaran.destroy', $pembayaran->id) }}" method="POST" class="d-inline"
                                                  data-confirm="Yakin ingin menghapus data ini?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-premium danger btn-sm p-1" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                                    Belum ada data laporan biaya pembayaran.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tab 2: Status Kelayakan Dokumen (Status Armada) -->
        <div class="tab-pane fade" id="status-pane" role="tabpanel" aria-labelledby="status-tab" tabindex="0">
            <div class="card-premium p-0 overflow-hidden">
                <div class="p-4 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-3">
                    <div>
                        <h5 class="fw-bold mb-1 d-flex align-items-center gap-2">
                            <i class="bi bi-speedometer2 text-primary"></i> Monitoring Status Real-time
                        </h5>
                        <p class="text-secondary small mb-0">Daftar rinci seluruh kendaraan operasional dan kepatuhan dokumen.</p>
                    </div>
                    
                    <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-3">
                        <!-- Filter Pills -->
                        <div class="premium-filter-pills flex-wrap">
                            <button class="filter-pill-btn active filter-type-btn" data-filter="all">
                                <i class="bi bi-grid-1x2-fill me-1"></i> Semua
                            </button>
                            <button class="filter-pill-btn filter-type-btn" data-filter="mobil">
                                <i class="bi bi-car-front-fill me-1"></i> Mobil
                            </button>
                            <button class="filter-pill-btn filter-type-btn" data-filter="motor">
                                <i class="bi bi-bicycle me-1"></i> Motor
                            </button>
                            <button class="filter-pill-btn filter-type-btn text-success" data-filter="aman">
                                <i class="bi bi-shield-check me-1"></i> Layak
                            </button>
                            <button class="filter-pill-btn filter-type-btn text-warning" data-filter="mendekati">
                                <i class="bi bi-clock-history me-1"></i> Mendekati
                            </button>
                            <button class="filter-pill-btn filter-type-btn text-danger" data-filter="terlambat">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Terlambat
                            </button>
                        </div>

                        <!-- Quick Search Input -->
                        <div class="search-box-premium" style="min-width: 260px;">
                            <i class="bi bi-search"></i>
                            <input type="text" id="tableSearch" class="form-control-premium" placeholder="Cari Nopol / Merek / Driver..." onkeyup="filterStatusTable()">
                        </div>
                    </div>
                </div>

                <div class="table-responsive-premium border-0">
                    <table class="table-premium" id="statusTable">
                        <thead>
                            <tr>
                                <th>Kendaraan & Nopol</th>
                                <th>Driver Penanggung Jawab</th>
                                <th>Odometer</th>
                                <th>Jatuh Tempo Dokumen & Legalitas</th>
                                <th>Status Kelayakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kendaraans as $kendaraan)
                            @php
                                $stnkDate = $kendaraan->tanggal_stnk ? \Carbon\Carbon::parse($kendaraan->tanggal_stnk) : null;
                                $pajakDate = $kendaraan->pajak_tahunan ? \Carbon\Carbon::parse($kendaraan->pajak_tahunan) : null;
                                $pajak5Date = $kendaraan->pajak_5_tahunan ? \Carbon\Carbon::parse($kendaraan->pajak_5_tahunan) : null;
                                $kirDate = $kendaraan->kir_bengkel ? \Carbon\Carbon::parse($kendaraan->kir_bengkel) : null;

                                // Check status for each document
                                $isStnkOverdue = $stnkDate ? $stnkDate->isPast() : false;
                                $isStnkNear = $stnkDate && !$isStnkOverdue && $stnkDate->diffInDays(now()) <= 30;

                                $isPajakOverdue = $pajakDate ? $pajakDate->isPast() : false;
                                $isPajakNear = $pajakDate && !$isPajakOverdue && $pajakDate->diffInDays(now()) <= 30;

                                $isPajak5Overdue = $pajak5Date ? $pajak5Date->isPast() : false;
                                $isPajak5Near = $pajak5Date && !$isPajak5Overdue && $pajak5Date->diffInDays(now()) <= 30;

                                $isKirOverdue = $kirDate ? $kirDate->isPast() : false;
                                $isKirNear = $kirDate && !$isKirOverdue && $kirDate->diffInDays(now()) <= 30;

                                $isOdoTinggi = $kendaraan->odometer_terakhir >= 100000;

                                // Overall status
                                $isOverdueAny = $isStnkOverdue || $isPajakOverdue || $isPajak5Overdue || $isKirOverdue || $isOdoTinggi;
                                $isNearAny = $isStnkNear || $isPajakNear || $isPajak5Near || $isKirNear;
                            @endphp
                            <tr class="status-row" data-jenis="{{ $kendaraan->jenis_kendaraan }}" data-status-category="{{ $isOverdueAny ? 'terlambat' : ($isNearAny ? 'mendekati' : 'aman') }}">
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="stat-icon-wrapper indigo" style="width: 40px; height: 40px; font-size: 18px;">
                                            <i class="bi bi-car-front-fill"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-primary fs-6">{{ $kendaraan->nomor_polisi }}</div>
                                            <div class="text-secondary small">{{ $kendaraan->merek }} {{ $kendaraan->tipe }} ({{ $kendaraan->jenis_kendaraan }})</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge-premium info"><i class="bi bi-person-circle"></i> {{ $kendaraan->nama_driver ?? 'Belum Ditentukan' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark fs-6">{{ number_format($kendaraan->odometer_terakhir, 0, ',', '.') }} <span class="small text-muted fw-normal">KM</span></div>
                                    @if($isOdoTinggi)
                                        <span class="badge bg-danger-subtle text-danger" style="font-size: 10px;">Odo Tinggi (&ge;100k)</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1 small">
                                        <!-- STNK -->
                                        @if($stnkDate)
                                            <div>
                                                <span class="text-secondary" style="display:inline-block; width:80px;">STNK:</span>
                                                <span class="fw-semibold @if($isStnkOverdue) text-danger @elseif($isStnkNear) text-warning @else text-success @endif">
                                                    {{ $stnkDate->format('d M Y') }}
                                                    @if($isStnkOverdue) (LEWAT) @elseif($isStnkNear) (H-{{ $stnkDate->diffInDays(now()) }}) @endif
                                                </span>
                                            </div>
                                        @endif

                                        <!-- Pajak Tahunan -->
                                        @if($pajakDate)
                                        <div>
                                            <span class="text-secondary" style="display:inline-block; width:80px;">Pajak:</span>
                                            <span class="fw-semibold @if($isPajakOverdue) text-danger @elseif($isPajakNear) text-warning @else text-success @endif">
                                                {{ $pajakDate->format('d M Y') }}
                                                @if($isPajakOverdue) (LEWAT) @elseif($isPajakNear) (H-{{ $pajakDate->diffInDays(now()) }}) @endif
                                            </span>
                                        </div>
                                        @endif

                                        <!-- Pajak 5 Tahunan -->
                                        @if($pajak5Date)
                                            <div>
                                                <span class="text-secondary" style="display:inline-block; width:80px;">Pajak 5Th:</span>
                                                <span class="fw-semibold @if($isPajak5Overdue) text-danger @elseif($isPajak5Near) text-warning @else text-success @endif">
                                                    {{ $pajak5Date->format('d M Y') }}
                                                    @if($isPajak5Overdue) (LEWAT) @elseif($isPajak5Near) (H-{{ $pajak5Date->diffInDays(now()) }}) @endif
                                                </span>
                                            </div>
                                        @endif

                                        <!-- KIR -->
                                        @if($kendaraan->jenis_kendaraan === 'Mobil Boks' || $kirDate)
                                            <div>
                                                <span class="text-secondary" style="display:inline-block; width:80px;">KIR:</span>
                                                @if($kirDate)
                                                    <span class="fw-semibold @if($isKirOverdue) text-danger @elseif($isKirNear) text-warning @else text-success @endif">
                                                        {{ $kirDate->format('d M Y') }}
                                                        @if($isKirOverdue) (LEWAT) @elseif($isKirNear) (H-{{ $kirDate->diffInDays(now()) }}) @endif
                                                    </span>
                                                @else
                                                    <span class="text-danger fw-semibold">Belum Diisi (Wajib KIR)</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($isOverdueAny)
                                        <span class="badge-premium danger py-2 px-3"><i class="bi bi-exclamation-triangle-fill"></i> Perlu Perawatan</span>
                                    @elseif($isNearAny)
                                        <span class="badge-premium warning py-2 px-3"><i class="bi bi-clock-history"></i> Mendekati Jatuh Tempo</span>
                                    @else
                                        <span class="badge-premium success py-2 px-3"><i class="bi bi-check-circle-fill"></i> Layak Jalan</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                                    Belum ada data armada / kendaraan terdaftar.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===== MODAL QRIS =={{-- ===== MODAL QRIS ===== --}}
<div class="modal fade" id="modalQris" tabindex="-1" aria-labelledby="modalQrisLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content border-0 overflow-hidden" style="border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.2);">

            {{-- Header --}}
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%); padding: 20px 24px 16px;">
                <div class="d-flex align-items-center gap-3 w-100">
                    <div style="width:44px;height:44px;border-radius:12px;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;font-size:22px;color:#fff;">
                        <i class="bi bi-qr-code"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0" id="modalQrisLabel">Pembayaran QRIS</h5>
                        <p class="text-white mb-0" style="font-size:11px;opacity:.8;">Scan QR Code atau gunakan Virtual Account</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            {{-- Body --}}
            <div class="modal-body text-center px-4 py-4" style="background: var(--bg-secondary);">

                {{-- Nominal Badge --}}
                <div class="mb-3 p-3 rounded-3" style="background: linear-gradient(135deg,rgba(79,70,229,0.08),rgba(124,58,237,0.05)); border:1px dashed rgba(79,70,229,0.25);">
                    <div class="text-secondary small mb-1">Total Tagihan Klaim</div>
                    <div class="fw-bold text-primary" style="font-size:1.6rem;font-family:'Outfit',sans-serif;" id="qrisNominal">Rp 0</div>
                    <div class="text-muted small mt-1" id="qrisInfo">—</div>
                </div>

                {{-- QR Code Image --}}
                <div class="position-relative d-inline-block mb-3">
                    <div style="position:absolute;inset:-8px;border-radius:18px;background:linear-gradient(135deg,#4f46e5,#7c3aed);z-index:0;"></div>
                    <div style="position:relative;z-index:1;background:#fff;border-radius:12px;padding:12px;">
                        <img id="qrisImage"
                             src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=MaintAsset-QRIS-Payment"
                             alt="QRIS QR Code"
                             width="200" height="200"
                             style="display:block;border-radius:6px;">
                    </div>
                    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);z-index:3;width:38px;height:38px;border-radius:8px;background:#fff;border:2px solid rgba(79,70,229,0.3);display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-lightning-charge-fill" style="color:#4f46e5;font-size:16px;"></i>
                    </div>
                </div>

                {{-- Alert Info Rekening DANA & VA --}}
                <div class="p-3 rounded-3 mb-3 text-start" style="background: rgba(16, 185, 129, 0.05); border: 1px dashed rgba(16, 185, 129, 0.3); font-size: 12px;">
                    <div class="fw-bold text-success mb-1 d-flex align-items-center gap-1.5">
                        <i class="bi bi-wallet2 fs-6"></i> Transfer DANA / e-Wallet Resmi
                    </div>
                    <div class="d-flex align-items-center justify-content-between p-2 rounded bg-white border mt-1">
                        <div>
                            <div class="fw-bold text-dark font-monospace" style="font-size:14px;">085840951519</div>
                            <div class="text-secondary small" style="font-size:11px;">a.n. <strong>Nazwa Salsabila</strong> (DANA)</div>
                        </div>
                        <button type="button" class="btn btn-sm btn-success px-2 py-1" style="font-size:11px;"
                                onclick="navigator.clipboard.writeText('085840951519'); alert('Nomor DANA 085840951519 berhasil disalin!')">
                            <i class="bi bi-copy me-1"></i>Salin DANA
                        </button>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center small">
                    <span class="text-muted">Status QRIS & DANA:</span>
                    <span class="fw-bold text-success"><i class="bi bi-record-fill me-1"></i>Siap Menerima Pembayaran</span>
                </div>
            </div>

            {{-- Footer --}}
            <div class="modal-footer border-0 pt-0 px-4 pb-4" style="background: var(--bg-secondary);">
                <button type="button" class="btn-premium primary w-100" data-bs-dismiss="modal" style="border-radius:12px;padding:12px;">
                    <i class="bi bi-check-circle-fill me-2"></i> Konfirmasi Pembayaran Selesai
                </button>
            </div>
        </div>
    </div>
<!-- Modal: Rincian Kelayakan Armada -->
<div class="modal fade" id="detailKelayakanModal" tabindex="-1" aria-labelledby="detailKelayakanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 18px; border: 1px solid var(--border-color); background: var(--bg-secondary);">
            <div class="modal-header border-bottom px-4 py-3">
                <h5 class="modal-title fw-bold" id="detailKelayakanModalTitle">
                    <i class="bi bi-truck text-primary me-2"></i> Rincian Status Kelayakan Armada
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="detailKelayakanTable" style="font-size: 13px;">
                        <thead class="table-light">
                            <tr>
                                <th>Kendaraan / Nopol</th>
                                <th>Jenis</th>
                                <th>Driver Utama</th>
                                <th>Odometer</th>
                                <th>Lokasi Pool</th>
                                <th class="text-center">Status Dokumen</th>
                            </tr>
                        </thead>
                        <tbody id="detailKelayakanTbody">
                            <!-- Populated dynamically via Javascript -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer px-4 py-3 border-top">
                <button type="button" class="btn-premium secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="{{ route('kendaraan.index') }}" class="btn-premium primary">
                    <i class="bi bi-gear-fill me-1"></i> Kelola di Manajemen Armada
                </a>
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
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Helper untuk generate QRIS payload string berformat EMVCo QRIS Statis/Dinamis
    function buildQrisPayload(nominal, nopol) {
        // String EMVCo QRIS Merchant standar Indonesia (NMI ID: ID1020000000000)
        // Format: 00 (Payload Format), 01 (Point of Initiation Method), 51 (Merchant Account Info QRIS), 52 (Merchant Category), 53 (Currency IDR=360), 54 (Amount), 58 (Country ID), 59 (Merchant Name), 60 (Merchant City), 63 (CRC16 Checksum)
        const merchantName = "MAINTASSET ARMADA";
        const merchantCity = "JAKARTA";
        const amountStr = parseFloat(nominal).toFixed(0);

        // Jika scanner e-wallet mendukung, kita sertakan EMVCo string standar
        // Format EMVCo 000201010212...
        const rawPayload = `00020101021226580016ID.CO.QRIS.WWW01189360091400000000000215ID10200000000000303UMI52045812530336054${amountStr.length.toString().padStart(2, '0')}${amountStr}5802ID59${merchantName.length.toString().padStart(2, '0')}${merchantName}60${merchantCity.length.toString().padStart(2, '0')}${merchantCity}6304A1B2`;
        return rawPayload;
    }

    // ===== Fungsi untuk membuka modal QRIS dari tombol tabel =====
    function openQrisModal(nominalFormatted, nopol, jenis, rawAmount) {
        const nominalEl = document.getElementById('qrisNominal');
        const infoEl    = document.getElementById('qrisInfo');
        const imgEl     = document.getElementById('qrisImage');

        if (nominalEl) {
            nominalEl.textContent = 'Rp ' + nominalFormatted;
        }
        if (infoEl) {
            infoEl.textContent = nopol + ' • ' + jenis;
        }
        if (imgEl) {
            const qrisPayload = buildQrisPayload(rawAmount, nopol);
            const qrData = encodeURIComponent(qrisPayload);
            imgEl.src = 'https://api.qrserver.com/v1/create-qr-code/?size=240x240&margin=2&ecc=M&data=' + qrData;
        }

        const modalEl = document.getElementById('modalQris');
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    }

    // ===== Auto-open modal QRIS jika session qris_show aktif =====
    @if(session('qris_show'))
    document.addEventListener('DOMContentLoaded', function() {
        const nominalRaw = {{ session('qris_jumlah', 0) }};
        const kendaraan  = @json(session('qris_kendaraan', '-'));
        const jenis      = @json(session('qris_jenis', '-'));

        openQrisModal(new Intl.NumberFormat('id-ID').format(nominalRaw), kendaraan, jenis, nominalRaw);
    });
    @endif

    document.addEventListener('DOMContentLoaded', function() {
        // Toggle info QRIS di form
        const metodeSelect = document.getElementById('metodePembayaran');
        const infoQris     = document.getElementById('infoQris');
        if (metodeSelect && infoQris) {
            metodeSelect.addEventListener('change', function() {
                infoQris.style.display = this.value === 'qris' ? 'block' : 'none';
            });
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        // --- Tab routing from URL parameter ---
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab');
        if (tabParam === 'status') {
            const tabEl = document.getElementById('status-tab');
            if (tabEl) {
                const tab = new bootstrap.Tab(tabEl);
                tab.show();
            }
        }

        // --- Chart 1: Biaya Chart (Smooth Wave Area Chart) ---
        @if(count($chartLabels) > 0)
        const canvasBiaya = document.getElementById('biayaChart');
        if (canvasBiaya) {
            const ctxBiaya = canvasBiaya.getContext('2d');
            const labels = {!! json_encode($chartLabels) !!};
            const data = {!! json_encode($chartData) !!}.map(Number);

            const total = data.reduce((sum, v) => sum + v, 0);
            const totalLabel = document.getElementById('totalBiayaLabel');
            if (totalLabel) {
                totalLabel.textContent = new Intl.NumberFormat('id-ID', {
                    style: 'currency', currency: 'IDR', maximumFractionDigits: 0
                }).format(total);
            }

            const fillGradient = ctxBiaya.createLinearGradient(0, 0, 0, 280);
            fillGradient.addColorStop(0, 'rgba(37, 99, 235, 0.4)');
            fillGradient.addColorStop(0.5, 'rgba(37, 99, 235, 0.12)');
            fillGradient.addColorStop(1, 'rgba(37, 99, 235, 0.0)');

            new Chart(ctxBiaya, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Biaya (Rp)',
                    data: data,
                    tension: 0.42, // Kurva gelombang halus naik-turun
                    fill: true,
                    backgroundColor: fillGradient,
                    borderColor: '#2563eb',
                    borderWidth: 3.5,
                    pointBackgroundColor: '#2563eb',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2.5,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: '#1d4ed8',
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        titleFont: { family: 'Plus Jakarta Sans', size: 12, weight: 'bold' },
                        bodyFont: { family: 'Plus Jakarta Sans', size: 11 },
                        padding: 12,
                        cornerRadius: 10,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return ' Biaya: ' + new Intl.NumberFormat('id-ID', {
                                    style: 'currency', currency: 'IDR', maximumFractionDigits: 0
                                }).format(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { family: 'Plus Jakarta Sans', size: 10, weight: '700' },
                            color: 'var(--text-primary)',
                            maxRotation: 45,
                            minRotation: 0
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(148, 163, 184, 0.1)', borderDash: [4, 4] },
                        ticks: {
                            font: { family: 'Plus Jakarta Sans', size: 9.5 },
                            color: 'var(--text-muted)',
                            callback: function(value) {
                                return new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(value);
                            }
                        }
                    }
                }
            }
        });
        }
        @endif

        // --- Chart 2: Kelayakan Chart (Doughnut - Clickable Slices) ---
        @if($totalArmada > 0)
        const ctxStatus = document.getElementById('statusArmadaChart').getContext('2d');

        const gradAman = ctxStatus.createLinearGradient(0, 0, 0, 200);
        gradAman.addColorStop(0, '#10b981');
        gradAman.addColorStop(1, '#059669');

        const gradMendekati = ctxStatus.createLinearGradient(0, 0, 0, 200);
        gradMendekati.addColorStop(0, '#fbbf24');
        gradMendekati.addColorStop(1, '#d97706');

        const gradJatuhTempo = ctxStatus.createLinearGradient(0, 0, 0, 200);
        gradJatuhTempo.addColorStop(0, '#f87171');
        gradJatuhTempo.addColorStop(1, '#dc2626');

        const chartStatusInst = new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['Aman & Layak', 'Mendekati Jatuh Tempo', 'Perlu Perawatan / Terlambat'],
                datasets: [{
                    data: [{{ $jumlahAman }}, {{ $jumlahMendekati }}, {{ $jumlahJatuhTempo }}],
                    backgroundColor: [gradAman, gradMendekati, gradJatuhTempo],
                    borderWidth: 0,
                    borderRadius: 10,
                    spacing: 4,
                    hoverOffset: 12
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '76%',
                onClick: (event, activeElements) => {
                    if (activeElements && activeElements.length > 0) {
                        const index = activeElements[0].index;
                        const categories = ['aman', 'mendekati', 'terlambat'];
                        if (categories[index]) {
                            showVehicleCategoryModal(categories[index]);
                        }
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true,
                    duration: 1000
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        padding: 10,
                        cornerRadius: 10,
                        backgroundColor: '#0f172a',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        titleFont: { family: 'Plus Jakarta Sans', size: 11, weight: 'bold' },
                        bodyFont: { family: 'Plus Jakarta Sans', size: 10.5 },
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const value = context.parsed;
                                const percent = total > 0 ? Math.round((value / total) * 100) : 0;
                                return ' ' + context.label + ': ' + value + ' unit (' + percent + '%) • Klik untuk lihat';
                            }
                        }
                    }
                }
            }
        });
        @endif
    });

    // ===== Dataset & Interactive Filter untuk Rincian Kelayakan Armada =====
    const vehiclesData = {!! json_encode($vehiclesJsArray) !!};

    function showVehicleCategoryModal(category) {
        // 1. Pindah ke Tab 2 (Kelayakan & Status Dokumen)
        const tabEl = document.getElementById('status-tab');
        if (tabEl) {
            const tab = bootstrap.Tab.getOrCreateInstance(tabEl);
            tab.show();
        }

        // 2. Set filter aktif sesuai kategori (aman, mendekati, terlambat)
        currentFilter = category;

        // 3. Update style tombol filter pill di tabel
        const filterBtns = document.querySelectorAll('.filter-type-btn');
        filterBtns.forEach(b => {
            if (b.getAttribute('data-filter') === category) {
                b.classList.add('active');
            } else {
                b.classList.remove('active');
            }
        });

        // 4. Jalankan filter baris tabel
        filterStatusTable();

        // 5. Scroll mulus langsung ke tabel kendaraan
        const targetSection = document.getElementById('status-pane');
        if (targetSection) {
            targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    // --- Table Filtering Logic ---
    let currentFilter = 'all';

    function filterStatusTable() {
        const input = document.getElementById('tableSearch');
        const query = input ? input.value.toLowerCase().trim() : '';
        const table = document.getElementById('statusTable');
        if (!table) return;
        const rows = table.querySelectorAll('.status-row');

        rows.forEach(row => {
            const textContent = row.textContent || row.innerText;
            const matchesSearch = textContent.toLowerCase().indexOf(query) > -1;
            const jenis = row.getAttribute('data-jenis') ? row.getAttribute('data-jenis').toLowerCase() : '';
            const statusCat = row.getAttribute('data-status-category') || '';

            let matchesFilter = false;
            if (currentFilter === 'all') {
                matchesFilter = true;
            } else if (currentFilter === 'mobil') {
                matchesFilter = jenis.includes('mobil') || jenis.includes('boks') || jenis.includes('carry') || jenis.includes('truk') || jenis.includes('dinas');
            } else if (currentFilter === 'motor') {
                matchesFilter = jenis.includes('motor') || jenis.includes('sepeda');
            } else if (currentFilter === 'aman' || currentFilter === 'mendekati' || currentFilter === 'terlambat') {
                matchesFilter = statusCat === currentFilter;
            }

            if (matchesSearch && matchesFilter) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const filterBtns = document.querySelectorAll('.filter-type-btn');
        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentFilter = this.getAttribute('data-filter');
                filterStatusTable();
            });
        });
    });
</script>
@endsection