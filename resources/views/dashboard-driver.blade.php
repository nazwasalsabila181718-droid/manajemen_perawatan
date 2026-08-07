@extends('layouts.app')

@section('title', 'Dashboard Driver')
@section('page_title', 'Area Operasional Driver')
@section('page_subtitle', 'Akses cepat inspeksi harian, lapor kendala, dan klaim biaya operasional.')

@section('content')
<style>
    /* Custom Styling for Driver Dashboard */
    .driver-welcome-banner {
        background: linear-gradient(135deg, var(--bs-primary) 0%, #3b82f6 100%);
        border-radius: 1rem;
        box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.3);
    }
    .action-card {
        border: 1px solid rgba(0,0,0,0.05);
        border-radius: 1rem;
        background: #fff;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
    }
    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
        border-color: var(--bs-primary);
    }
    .action-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 1.5rem;
    }
    .icon-success { background: #dcfce7; color: #16a34a; }
    .icon-danger { background: #fee2e2; color: #dc2626; }
    .icon-info { background: #e0f2fe; color: #0284c7; }
    
    .list-card {
        border: none;
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
    }
    .vehicle-item {
        transition: background-color 0.2s;
        border-bottom: 1px solid rgba(0,0,0,0.03);
    }
    .vehicle-item:last-child {
        border-bottom: none;
    }
    .vehicle-item:hover {
        background-color: #f8fafc;
    }
    
    [data-theme="dark"] .action-card,
    [data-theme="dark"] .list-card {
        background: #1e293b;
        border-color: #334155;
    }
    [data-theme="dark"] .vehicle-item {
        border-color: #334155;
    }
    [data-theme="dark"] .vehicle-item:hover {
        background-color: #0f172a;
    }
    [data-theme="dark"] .icon-success { background: rgba(22, 163, 74, 0.2); }
    [data-theme="dark"] .icon-danger { background: rgba(220, 38, 38, 0.2); }
    [data-theme="dark"] .icon-info { background: rgba(2, 132, 199, 0.2); }
</style>

<div class="container-fluid p-0">

    @php
        $assignedKendaraan = $semuaKendaraan->firstWhere('nama_driver', auth()->user()->name);
    @endphp

    <!-- Driver Welcome Banner -->
    <div class="driver-welcome-banner p-4 p-md-5 mb-4 text-white position-relative overflow-hidden">
        <div class="position-absolute top-0 end-0 opacity-25" style="transform: translate(20%, -20%); pointer-events: none;">
            <i class="bi bi-car-front-fill" style="font-size: 15rem;"></i>
        </div>
        <div class="position-relative z-1 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4">
            <div>
                <h2 class="fw-bold mb-2 text-white">Selamat Datang, {{ auth()->user()->name ?? 'Driver' }}! 👋</h2>
                <p class="mb-0 text-white-50 fs-6">Semoga operasional dan perjalanan armada Anda hari ini aman, nyaman, dan lancar.</p>
            </div>
            <div class="d-inline-flex align-items-center gap-2 px-4 py-2 rounded-pill" style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2);">
                <i class="bi bi-clock-history fs-5"></i> <span id="live-time-driver" class="fw-medium"></span>
            </div>
        </div>
    </div>

    <!-- Assigned Vehicle Spotlight (If Assigned) -->
    @if($assignedKendaraan)
    <div class="action-card p-4 mb-4 border-start border-4 border-primary">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-4">
            <div class="d-flex align-items-center gap-3">
                <div class="action-icon icon-info mb-0" style="width: 50px; height: 50px; font-size: 20px;">
                    <i class="bi bi-car-front-fill"></i>
                </div>
                <div>
                    <div class="text-uppercase fw-bold text-primary mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">KENDARAAN ANDA</div>
                    <h4 class="fw-bold mb-1">{{ $assignedKendaraan->nomor_polisi }}</h4>
                    <div class="text-secondary small">{{ $assignedKendaraan->merek }} {{ $assignedKendaraan->tipe }} &bull; {{ $assignedKendaraan->pool_lokasi }}</div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-5">
                <div class="text-md-end">
                    <div class="text-muted small text-uppercase fw-semibold mb-1" style="font-size: 0.7rem;">Odometer Terakhir</div>
                    <div class="fw-bold fs-5">{{ number_format($assignedKendaraan->odometer_terakhir, 0, ',', '.') }} <span class="text-muted fs-6">KM</span></div>
                </div>
                <div class="text-md-end">
                    <div class="text-muted small text-uppercase fw-semibold mb-1" style="font-size: 0.7rem;">Masa Pajak</div>
                    <div class="fw-bold fs-5 text-primary">{{ \Carbon\Carbon::parse($assignedKendaraan->pajak_tahunan)->format('d M Y') }}</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Driver Quick Action Cards (3 Pillars) -->
    <div class="row g-4 mb-5">
        <!-- 1. Form Inspeksi Harian -->
        <div class="col-12 col-md-4">
            <div class="action-card h-100 p-4 d-flex flex-column">
                <div class="action-icon icon-success">
                    <i class="bi bi-clipboard2-check-fill"></i>
                </div>
                <h5 class="fw-bold mb-2">Form Inspeksi Harian</h5>
                <p class="text-secondary small mb-4 flex-grow-1">Lakukan pemeriksaan kondisi rutin kendaraan sebelum mengemudi (rem, ban, lampu, oli).</p>
                <a href="{{ route('checklist.create') }}" class="btn btn-outline-success w-100 fw-medium rounded-pill">
                    Isi Checklist <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>

        <!-- 2. Lapor Kendala Kendaraan -->
        <div class="col-12 col-md-4">
            <div class="action-card h-100 p-4 d-flex flex-column">
                <div class="action-icon icon-danger">
                    <i class="bi bi-exclamation-octagon-fill"></i>
                </div>
                <h5 class="fw-bold mb-2">Lapor Kendala Kendaraan</h5>
                <p class="text-secondary small mb-4 flex-grow-1">Laporkan segera kerusakan mesin, bunyi aneh, atau masalah fisik lainnya.</p>
                <a href="{{ route('keluhan-kendaraan.create') }}" class="btn btn-outline-danger w-100 fw-medium rounded-pill">
                    Buat Laporan <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>

        <!-- 3. Klaim Biaya Operasional -->
        <div class="col-12 col-md-4">
            <div class="action-card h-100 p-4 d-flex flex-column">
                <div class="action-icon icon-info">
                    <i class="bi bi-wallet2"></i>
                </div>
                <h5 class="fw-bold mb-2">Klaim Biaya Operasional</h5>
                <p class="text-secondary small mb-4 flex-grow-1">Pengajuan klaim reimburse untuk pengeluaran BBM, e-Toll, parkir, dan lainnya.</p>
                <a href="{{ route('pembayaran.index') }}" class="btn btn-outline-primary w-100 fw-medium rounded-pill">
                    Ajukan Klaim <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Status Ready Fleet & Vehicle Maintenance Status -->
    <div class="row g-4">
        <!-- Ready Fleet -->
        <div class="col-lg-6">
            <div class="list-card h-100 p-0 overflow-hidden">
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-light bg-opacity-50" style="background-color: var(--bs-gray-100);">
                    <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill text-success fs-5"></i>
                        Daftar Armada Siap Jalan
                    </h6>
                    <span class="badge bg-success rounded-pill px-3">{{ $kendaraanSiap->count() }} Unit</span>
                </div>
                <div class="d-flex flex-column">
                    @forelse($kendaraanSiap->take(5) as $kendaraan)
                    <div class="vehicle-item p-3 px-4 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-car-front-fill"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">{{ $kendaraan->nomor_polisi }}</div>
                                <div class="text-secondary small">{{ $kendaraan->merek }} {{ $kendaraan->tipe }}</div>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-dark">{{ number_format($kendaraan->odometer_terakhir, 0, ',', '.') }} <span class="text-muted small fw-normal">KM</span></div>
                        </div>
                    </div>
                    @empty
                    <div class="p-5 text-center text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-3 opacity-25"></i>
                        <p class="mb-0">Tidak ada kendaraan siap jalan saat ini.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Vehicles In Maintenance -->
        <div class="col-lg-6">
            <div class="list-card h-100 p-0 overflow-hidden">
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-light bg-opacity-50" style="background-color: var(--bs-gray-100);">
                    <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-tools text-danger fs-5"></i>
                        Armada Dalam Perawatan
                    </h6>
                    <span class="badge bg-danger rounded-pill px-3">{{ $kendaraanPerluRawat->count() }} Unit</span>
                </div>
                <div class="d-flex flex-column">
                    @forelse($kendaraanPerluRawat->take(5) as $kendaraan)
                    <div class="vehicle-item p-3 px-4 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-wrench-adjustable"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">{{ $kendaraan->nomor_polisi }}</div>
                                <div class="text-secondary small">{{ $kendaraan->merek }} {{ $kendaraan->tipe }}</div>
                            </div>
                        </div>
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-2">Sedang Di Bengkel</span>
                    </div>
                    @empty
                    <div class="p-5 text-center text-muted">
                        <i class="bi bi-check2-circle fs-1 d-block mb-3 text-success opacity-50"></i>
                        <p class="mb-0">Semua armada dalam kondisi prima & siap operasional! 🎉</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function updateDriverClock() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
            const el = document.getElementById('live-time-driver');
            if (el) el.innerText = now.toLocaleDateString('id-ID', options);
        }
        updateDriverClock();
        setInterval(updateDriverClock, 60000);
    });
</script>
@endsection