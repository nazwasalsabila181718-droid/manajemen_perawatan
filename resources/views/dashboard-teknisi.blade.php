@extends('layouts.app')

@section('title', 'Dashboard Teknisi Maintenance')
@section('page_title', 'Workstation Pemeliharaan Teknisi')
@section('page_subtitle', 'Pusat kendali bengkel: kelola antrean perbaikan, tangani keluhan driver, dan update log servis.')

@section('content')
<div class="container-fluid p-0">

    <!-- Technician Banner -->
    <div class="card-premium mb-4 p-4 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 50%, #2563eb 100%); border: none; border-radius: var(--radius-lg);">
        <div class="position-relative z-1 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
            <div>
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-2" style="background: rgba(255, 255, 255, 0.15); font-size: 12px;">
                    <i class="bi bi-tools"></i> Standby Bengkel & Maintenance
                </div>
                <h2 class="fw-bold mb-1 text-white">Halo, {{ auth()->user()->name ?? 'Teknisi' }}! 🛠️</h2>
                <p class="mb-0 text-white-50" style="font-size: 14px;">Siap melaksanakan inspeksi & perbaikan armada operasional hari ini.</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="text-end text-white">
                    <div class="text-white-50 small">Kesiapan Armada</div>
                    <div class="fw-bold fs-4 text-success">{{ $persentaseSehat }}% Layak</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4 Workstation Stat Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Laporan Kendala Baru -->
        <div class="col-md-6 col-xl-3">
            <div class="stat-card-ultra">
                <div class="stat-icon-wrapper rose">
                    <i class="bi bi-exclamation-octagon-fill"></i>
                </div>
                <div class="stat-meta">
                    <div class="stat-label-text">Kendala Baru</div>
                    <div class="stat-value-text text-danger">{{ $keluhanBaru->count() }} <span class="fs-6 fw-normal text-muted">unit</span></div>
                </div>
            </div>
        </div>

        <!-- 2. Sedang Diperbaiki Bengkel -->
        <div class="col-md-6 col-xl-3">
            <div class="stat-card-ultra">
                <div class="stat-icon-wrapper amber">
                    <i class="bi bi-tools"></i>
                </div>
                <div class="stat-meta">
                    <div class="stat-label-text">Sedang Diproses</div>
                    <div class="stat-value-text text-warning">{{ $keluhanDiproses->count() }} <span class="fs-6 fw-normal text-muted">unit</span></div>
                </div>
            </div>
        </div>

        <!-- 3. Perlu Servis / Overdue -->
        <div class="col-md-6 col-xl-3">
            <div class="stat-card-ultra">
                <div class="stat-icon-wrapper indigo">
                    <div class="bi bi-truck"></div>
                </div>
                <div class="stat-meta">
                    <div class="stat-label-text">Armada Perlu Servis</div>
                    <div class="stat-value-text text-primary">{{ $kendaraanPerluRawat }} <span class="fs-6 fw-normal text-muted">unit</span></div>
                </div>
            </div>
        </div>

        <!-- 4. Servis Selesai Bulan Ini -->
        <div class="col-md-6 col-xl-3">
            <div class="stat-card-ultra">
                <div class="stat-icon-wrapper emerald">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="stat-meta">
                    <div class="stat-label-text">Selesai (Bln Ini)</div>
                    <div class="stat-value-text text-success">{{ $keluhanSelesai }} <span class="fs-6 fw-normal text-muted">kasus</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Action Workstation Toolbar -->
    <div class="card-premium p-4 mb-4" style="background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--accent-subtle) 100%);">
        <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
            <i class="bi bi-lightning-charge-fill text-warning"></i> Quick Action Workstation Teknisi
        </h5>
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <a href="{{ route('jadwal-perawatan.index') }}" class="btn-premium primary w-100 justify-content-center py-2.5">
                    <i class="bi bi-wrench-adjustable me-1"></i> Catat Servis Rutin
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('keluhan-kendaraan.index') }}" class="btn-premium secondary w-100 justify-content-center py-2.5">
                    <i class="bi bi-exclamation-octagon text-warning me-1"></i> Laporan Kendala
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('kendaraan.index') }}" class="btn-premium secondary w-100 justify-content-center py-2.5">
                    <i class="bi bi-speedometer2 text-primary me-1"></i> Update Odometer
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('checklist.create') }}" class="btn-premium secondary w-100 justify-content-center py-2.5">
                    <i class="bi bi-clipboard2-check text-success me-1"></i> Inspeksi Harian
                </a>
            </div>
        </div>
    </div>

    <!-- 2 Action Tables for Technician -->
    <div class="row g-4">
        <!-- Table 1: Antrean Laporan Kendala Driver -->
        <div class="col-lg-7">
            <div class="card-premium p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-triangle-fill text-danger"></i> Antrean Laporan Kendala Driver
                        </h5>
                        <div class="text-secondary small">Keluhan kerusakan fisik/mesin yang perlu penanganan</div>
                    </div>
                    <a href="{{ route('keluhan-kendaraan.index') }}" class="btn-premium secondary btn-sm pill">Lihat Semua</a>
                </div>

                <div class="table-responsive-premium border-0">
                    <table class="table-premium">
                        <thead>
                            <tr>
                                <th>Kendaraan & Pelapor</th>
                                <th>Deskripsi Kendala</th>
                                <th>Urgensi</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($keluhanBaru->concat($keluhanDiproses)->take(5) as $item)
                            <tr>
                                <td>
                                    <div class="fw-bold text-primary">{{ $item->kendaraan->nomor_polisi }}</div>
                                    <div class="text-muted small"><i class="bi bi-person me-1"></i>{{ $item->pelapor->name }}</div>
                                </td>
                                <td>
                                    <div class="small text-secondary text-truncate" style="max-width: 200px;" title="{{ $item->keluhan }}">
                                        {{ $item->keluhan }}
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $urgencyClass = match(strtolower($item->tingkat_urgensi)) {
                                            'berat' => 'danger',
                                            'sedang' => 'warning',
                                            default => 'info'
                                        };
                                    @endphp
                                    <span class="badge-premium {{ $urgencyClass }}">{{ ucfirst($item->tingkat_urgensi) }}</span>
                                </td>
                                <td>
                                    @php
                                        $statusClass = match(strtolower($item->status)) {
                                            'selesai' => 'success',
                                            'diproses' => 'indigo',
                                            default => 'danger'
                                        };
                                    @endphp
                                    <span class="badge-premium {{ $statusClass }}">{{ ucfirst($item->status) }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('keluhan-kendaraan.index') }}" class="btn-premium secondary btn-sm p-1.5 px-2">
                                        <i class="bi bi-tools"></i> Tangani
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="bi bi-check-circle-fill text-success fs-3 d-block mb-1 opacity-50"></i>
                                    Tidak ada antrean laporan kendala saat ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Table 2: Jadwal Perawatan Rutin Mendatang -->
        <div class="col-lg-5">
            <div class="card-premium p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-calendar2-week-fill text-primary"></i> Servis Rutin Mendatang
                        </h5>
                        <div class="text-secondary small">Jadwal penggantian oli, aki, & ban berkala</div>
                    </div>
                    <a href="{{ route('jadwal-perawatan.index') }}" class="btn-premium secondary btn-sm pill">Lihat Semua</a>
                </div>

                <div class="table-responsive-premium border-0">
                    <table class="table-premium">
                        <thead>
                            <tr>
                                <th>Nopol</th>
                                <th>Komponen</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jadwalServisRutin as $item)
                            <tr>
                                <td class="fw-bold text-primary">{{ $item->kendaraan->nomor_polisi }}</td>
                                <td class="small fw-semibold text-secondary">{{ $item->jenis_perawatan }}</td>
                                <td>
                                    <span class="badge-premium {{ str_contains($item->statusBadgeClass(), 'danger') ? 'danger' : (str_contains($item->statusBadgeClass(), 'warning') ? 'warning' : 'success') }}">
                                        {{ $item->statusLabel() }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-3 d-block mb-1 opacity-50"></i>
                                    Belum ada data jadwal servis.
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
@endsection
