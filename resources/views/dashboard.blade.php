@extends('layouts.app')

@section('title', 'Executive Dashboard')
@section('page_title', 'Ringkasan Eksekutif & Manajerial')
@section('page_subtitle', 'Pusat kendali eksekutif: kontrol seluruh armada, biaya operasional, dan kepatuhan dokumen perusahaan.')

@section('content')
<div class="container-fluid p-0">

    <!-- Executive Control Banner & Quick Actions -->
    <div class="row g-4 mb-4">
        <!-- System Health Widget -->
        <div class="col-lg-8">
            <div class="card-premium h-100 d-flex flex-column justify-content-between p-4" style="background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--accent-subtle) 100%); border-left: 4px solid var(--accent);">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge-premium indigo"><i class="bi bi-shield-check"></i> Panel Kontrol Eksekutif</span>
                        <span class="badge-premium info">Sinkronisasi Real-Time</span>
                    </div>
                    <h2 class="fw-bold mb-2" style="font-size: 1.45rem;">Manajemen Perawatan Armada Terintegrasi</h2>
                    <p class="text-secondary mb-3" style="max-width: 640px; font-size: 13px; line-height: 1.6;">
                        Pantau seluruh kesiapan fisik armada, kontrol pengeluaran biaya servis, serta awasi kepatuhan pajak tahunan & KIR secara transparan dan akurat.
                    </p>
                </div>
                <div class="row align-items-center g-3 pt-3 border-top border-subtle">
                    <div class="col-sm-5">
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-icon-wrapper emerald" style="width: 42px; height: 42px; font-size: 18px;">
                                <i class="bi bi-shield-lock-fill"></i>
                            </div>
                            <div>
                                <div class="text-muted fw-bold" style="font-size: 10px; letter-spacing: 0.05em;">INDEKS KELAYAKAN</div>
                                <div class="fs-4 fw-bold text-success">{{ $persentaseSehat }}% Layak</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div class="d-flex justify-content-between small text-muted mb-1 fw-semibold" style="font-size: 11px;">
                            <span>Kesiapan Armada Perusahaan</span>
                            <span>{{ $persentaseSehat }}%</span>
                        </div>
                        <div class="progress" style="height: 8px; border-radius: 999px; background-color: var(--border-color);">
                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $persentaseSehat }}%" aria-valuenow="{{ $persentaseSehat }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Executive Quick Actions -->
        <div class="col-lg-4">
            <div class="card-premium h-100 d-flex flex-column justify-content-between p-4">
                <div>
                    <h5 class="fw-bold mb-1" style="font-size: 1.1rem;"><i class="bi bi-sliders text-primary me-2"></i>Aksi Manajerial</h5>
                    <p class="text-secondary small mb-3" style="font-size: 11px;">Akses langsung fitur kepemimpinan & pendaftaran armada.</p>
                </div>
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('kendaraan.index') }}" class="btn-premium primary justify-content-start py-2.5" style="font-size: 12.5px;">
                        <i class="bi bi-plus-circle-fill"></i>
                        <span>Daftarkan Armada Baru</span>
                    </a>
                    <a href="{{ route('pembayaran.index') }}" class="btn-premium secondary justify-content-start py-2.5" style="font-size: 12.5px;">
                        <i class="bi bi-wallet2 text-info"></i>
                        <span>Audit Biaya Operasional</span>
                    </a>
                    <a href="{{ route('pembayaran.index', ['tab' => 'status']) }}" class="btn-premium secondary justify-content-start py-2.5" style="font-size: 12.5px;">
                        <i class="bi bi-shield-check text-success"></i>
                        <span>Cek Kepatuhan Pajak & KIR</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Executive Stat Cards (Grid Layout - Gen-Z Tech Theme) -->
    <div class="row g-2.5 mb-4">
        <!-- 1. Total Armada Kendaraan -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card-cream h-100">
                <div class="stat-icon-cream green">
                    <i class="bi bi-truck"></i>
                </div>
                <div>
                    <div class="stat-label-cream">Total Armada</div>
                    <div class="stat-val-cream green">{{ $kendaraanTotal }} <span class="unit">unit</span></div>
                </div>
            </div>
        </div>

        <!-- 2. Armada Perlu Servis -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card-cream h-100">
                <div class="stat-icon-cream red">
                    <i class="bi bi-wrench-adjustable"></i>
                </div>
                <div>
                    <div class="stat-label-cream">Perlu Servis</div>
                    <div class="stat-val-cream red">{{ $kendaraanPerluRawat }} <span class="unit">unit</span></div>
                </div>
            </div>
        </div>

        <!-- 3. Armada Sedang Diservis -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card-cream h-100">
                <div class="stat-icon-cream amber">
                    <i class="bi bi-tools"></i>
                </div>
                <div>
                    <div class="stat-label-cream">Sedang Diservis</div>
                    <div class="stat-val-cream amber">{{ $armadaSedangServis }} <span class="unit">unit</span></div>
                </div>
            </div>
        </div>

        <!-- 4. Armada Selesai Servis -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card-cream h-100">
                <div class="stat-icon-cream emerald">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <div class="stat-label-cream">Selesai Servis</div>
                    <div class="stat-val-cream emerald">{{ $armadaSelesaiServis }} <span class="unit">unit</span></div>
                </div>
            </div>
        </div>

        <!-- 5. Dokumen H-30 -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card-cream h-100">
                <div class="stat-icon-cream teal">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <div class="stat-label-cream">Dokumen (H-30)</div>
                    <div class="stat-val-cream teal">{{ $jumlahMendekatiJatuhTempo }} <span class="unit">unit</span></div>
                </div>
            </div>
        </div>

        <!-- 6. Biaya Servis Bulan Ini -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card-cream h-100">
                <div class="stat-icon-cream green">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div>
                    <div class="stat-label-cream">Biaya (Bln Ini)</div>
                    <div class="stat-val-cream green" style="font-size: 0.98rem;">Rp {{ number_format($biayaBulanIni, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row: Bar Chart & Doughnut Chart (Side by Side) -->
    <div class="row g-4 mb-4">
        @if(auth()->user() && auth()->user()->role !== 'driver')
        <!-- Chart 1: Analisis Biaya (Admin & Teknisi Only) -->
        <div class="col-lg-6">
            <div class="card-premium p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-1" style="font-size: 1.05rem;"><i class="bi bi-bar-chart-fill text-primary me-2"></i>Diagram Analisis Biaya</h5>
                        <p class="text-secondary small mb-0" style="font-size: 11px;">Rincian pengeluaran perbaikan & operasional per unit bulan ini.</p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1 fw-bold" style="font-size: 11px;">
                            Total: Rp {{ number_format($biayaBulanIni, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
                <div style="position: relative; height: 260px; width: 100%;">
                    <!-- Ambient Glow Behind Chart -->
                    <div style="position: absolute; inset: 0; background: radial-gradient(circle, rgba(79, 70, 229, 0.03) 0%, transparent 75%); filter: blur(15px); pointer-events: none; z-index: 1;"></div>
                    @if(count($chartLabels) > 0)
                        <canvas id="biayaChart" style="position: relative; z-index: 2;"></canvas>
                    @else
                        <div class="d-flex justify-content-center align-items-center h-100 text-muted small" style="position: relative; z-index: 2;">
                            Belum ada data pengeluaran disetujui untuk bulan ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Chart 2: Kelayakan Armada -->
        <div class="{{ (auth()->user() && auth()->user()->role === 'driver') ? 'col-12' : 'col-lg-6' }}">
            <div class="card-premium p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-1" style="font-size: 1.05rem;"><i class="bi bi-pie-chart-fill text-success me-2"></i>Distribusi Kelayakan Armada</h5>
                        <p class="text-secondary small mb-0" style="font-size: 11px;">Status kelayakan jalan dan kepatuhan dokumen seluruh armada.</p>
                    </div>
                    <div>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 fw-bold" style="font-size: 11px;">
                            {{ $persentaseSehat }}% Layak
                        </span>
                    </div>
                </div>
                <div class="row align-items-center g-3">
                    <div class="col-sm-6 position-relative d-flex justify-content-center">
                        <div style="position: relative; height: 200px; width: 200px;">
                            <div style="position: absolute; inset: 0; border-radius: 50%; background: radial-gradient(circle, rgba(16, 185, 129, 0.08) 0%, transparent 75%); filter: blur(10px); pointer-events: none;"></div>
                            @if($kendaraanTotal > 0)
                                <canvas id="statusArmadaChart" style="position: relative; z-index: 2;"></canvas>
                                <div class="position-absolute top-50 start-50 translate-middle text-center pointer-events-none d-flex flex-column align-items-center justify-content-center" 
                                     style="z-index: 3; width: 105px; height: 105px; border-radius: 50%; background: var(--bg-secondary); border: 1.5px solid var(--border-color); box-shadow: var(--shadow-sm);">
                                    <div class="fs-3 fw-bold text-primary" style="line-height: 1; font-family: 'Outfit', sans-serif;">{{ $kendaraanTotal }}</div>
                                    <div class="text-muted fw-bold mt-1" style="font-size: 8px; letter-spacing: 0.05em; text-transform: uppercase;">Total Unit</div>
                                </div>
                            @else
                                <div class="d-flex justify-content-center align-items-center h-100 text-muted small">
                                    Belum ada data armada.
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-6 d-flex flex-column gap-2">
                        <div class="d-flex align-items-center justify-content-between p-2 rounded border" style="font-size: 11px; background: rgba(16, 185, 129, 0.03); border-color: rgba(16, 185, 129, 0.15) !important;">
                            <span class="fw-semibold text-success"><i class="bi bi-shield-check me-1"></i>Aman & Layak</span>
                            <span class="fw-bold text-success">{{ $jumlahAman }} unit</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between p-2 rounded border" style="font-size: 11px; background: rgba(245, 158, 11, 0.03); border-color: rgba(245, 158, 11, 0.15) !important;">
                            <span class="fw-semibold text-warning"><i class="bi bi-clock-history me-1"></i>Mendekati</span>
                            <span class="fw-bold text-warning">{{ $jumlahMendekati }} unit</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between p-2 rounded border" style="font-size: 11px; background: rgba(239, 68, 68, 0.03); border-color: rgba(239, 68, 68, 0.15) !important;">
                            <span class="fw-semibold text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>Terlambat</span>
                            <span class="fw-bold text-danger">{{ $jumlahJatuhTempo }} unit</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3-Column Footer Layout: Small and neat data lists -->
    <div class="row g-4">
        <!-- Column 1: Registrasi Armada Terbaru -->
        <div class="col-lg-4">
            <div class="card-premium p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold mb-0 d-flex align-items-center gap-2" style="font-size: 1rem;">
                            <i class="bi bi-truck text-primary"></i> Armada Baru
                        </h6>
                        <p class="text-secondary small mb-0" style="font-size: 10px;">Daftar unit terbaru didaftarkan</p>
                    </div>
                    <a href="{{ route('kendaraan.index') }}" class="btn btn-sm btn-outline-secondary py-0.5 px-2" style="font-size: 10px; border-radius: 8px;">
                        Semua
                    </a>
                </div>

                <div class="table-responsive-premium border-0">
                    <table class="table-premium" style="font-size: 11px;">
                        <thead>
                            <tr>
                                <th style="padding: 6px 10px;">Nopol & Tipe</th>
                                <th style="padding: 6px 10px;">Driver</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentKendaraans as $kendaraan)
                            <tr>
                                <td style="padding: 8px 10px;">
                                    <div class="fw-bold text-primary">{{ $kendaraan->nomor_polisi }}</div>
                                    <div class="text-muted" style="font-size: 9.5px;">{{ $kendaraan->merek }} {{ $kendaraan->tipe }}</div>
                                </td>
                                <td style="padding: 8px 10px;">
                                    <span class="badge bg-light text-dark text-truncate" style="max-width: 90px; font-size: 9.5px;" title="{{ $kendaraan->nama_driver }}">{{ $kendaraan->nama_driver ?? 'Belum Ada' }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-3">Belum ada unit.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Column 2: Transaksi Biaya Terbaru -->
        <div class="col-lg-4">
            <div class="card-premium p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold mb-0 d-flex align-items-center gap-2" style="font-size: 1rem;">
                            <i class="bi bi-receipt-cutoff text-info"></i> Transaksi Terbaru
                        </h6>
                        <p class="text-secondary small mb-0" style="font-size: 10px;">Catatan klaim biaya operasional</p>
                    </div>
                    <a href="{{ route('pembayaran.index') }}" class="btn btn-sm btn-outline-secondary py-0.5 px-2" style="font-size: 10px; border-radius: 8px;">
                        Semua
                    </a>
                </div>

                <div class="table-responsive-premium border-0">
                    <table class="table-premium" style="font-size: 11px;">
                        <thead>
                            <tr>
                                <th style="padding: 6px 10px;">Armada</th>
                                <th style="padding: 6px 10px; text-align: right;">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPembayaran as $bayar)
                            <tr>
                                <td style="padding: 8px 10px;">
                                    <div class="fw-bold text-primary">{{ $bayar->kendaraan->nomor_polisi ?? '-' }}</div>
                                    <div class="text-muted" style="font-size: 9.5px;">{{ $bayar->jenis_biaya }}</div>
                                </td>
                                <td class="fw-bold text-success text-end" style="padding: 8px 10px; text-align: right;">
                                    Rp {{ number_format($bayar->jumlah, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-3">Belum ada biaya.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Column 3: Log Aktivitas Terkini (Unified Activities) -->
        <div class="col-lg-4">
            <div class="card-premium p-4 h-100">
                <h6 class="fw-bold mb-1 d-flex align-items-center gap-2" style="font-size: 1rem;">
                    <i class="bi bi-clock-history text-secondary"></i> Log Aktivitas Terkini
                </h6>
                <p class="text-secondary small mb-3" style="font-size: 10px;">Aktivitas real-time sistem perawatan</p>
                <div class="d-flex flex-column gap-3 overflow-auto" style="max-height: 230px; padding-right: 4px;">
                    @forelse($activities as $act)
                        <div class="d-flex align-items-start gap-2.5">
                            <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center {{ $act['bg'] }}" style="width: 28px; height: 28px; font-size: 12px; min-width: 28px;">
                                <i class="bi {{ $act['icon'] }}"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex align-items-center justify-content-between gap-2">
                                    <span class="fw-bold text-dark text-truncate" style="font-size: 10.5px; max-width: 100px;">{{ $act['title'] }}</span>
                                    <span class="text-muted flex-shrink-0" style="font-size: 8.5px;">{{ \Carbon\Carbon::parse($act['time'])->diffForHumans() }}</span>
                                </div>
                                <p class="text-secondary mb-0 text-truncate" style="font-size: 10px; line-height: 1.25;" title="{{ $act['message'] }}">{{ $act['message'] }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small">
                            Belum ada aktivitas.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if($kendaraanTotal > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- Chart 1: Biaya Chart ---
        const biayaCanvas = document.getElementById('biayaChart');
        if (biayaCanvas) {
            const ctxBiaya = biayaCanvas.getContext('2d');
            const labels = {!! json_encode($chartLabels) !!};
            const data = {!! json_encode($chartData) !!}.map(Number);

            // Create linear gradient for the bars
            const gradient = ctxBiaya.createLinearGradient(0, 0, 0, 240);
            gradient.addColorStop(0, '#1e4d2b');
            gradient.addColorStop(0.5, '#2e6f40');
            gradient.addColorStop(1, '#74c69d');

            const gradientHover = ctxBiaya.createLinearGradient(0, 0, 0, 240);
            gradientHover.addColorStop(0, '#14351d');
            gradientHover.addColorStop(0.5, '#1e4d2b');
            gradientHover.addColorStop(1, '#52b788');

            new Chart(ctxBiaya, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Biaya (Rp)',
                        data: data,
                        backgroundColor: gradient,
                        hoverBackgroundColor: gradientHover,
                        borderWidth: 0,
                        borderRadius: { topLeft: 8, topRight: 8, bottomLeft: 0, bottomRight: 0 },
                        maxBarThickness: 25
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            titleFont: { family: 'Plus Jakarta Sans', size: 12, weight: 'bold' },
                            bodyFont: { family: 'Plus Jakarta Sans', size: 11 },
                            padding: 10,
                            cornerRadius: 10,
                            displayColors: false,
                            callbacks: {
                                label: function (context) {
                                    return ' ' + new Intl.NumberFormat('id-ID', {
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
                                font: { family: 'Plus Jakarta Sans', size: 10.5, weight: '600' },
                                color: 'var(--text-secondary)'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(148, 163, 184, 0.06)' },
                            ticks: {
                                font: { family: 'Plus Jakarta Sans', size: 9.5 },
                                color: 'var(--text-muted)',
                                callback: function (value) {
                                    return new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(value);
                                }
                            }
                        }
                    }
                }
            });
        }

        // --- Chart 2: Kelayakan Chart (Doughnut) ---
        const statusCanvas = document.getElementById('statusArmadaChart');
        if (statusCanvas) {
            const ctxStatus = statusCanvas.getContext('2d');

            const gradAman = ctxStatus.createLinearGradient(0, 0, 0, 200);
            gradAman.addColorStop(0, '#10b981');
            gradAman.addColorStop(1, '#059669');

            const gradMendekati = ctxStatus.createLinearGradient(0, 0, 0, 200);
            gradMendekati.addColorStop(0, '#fbbf24');
            gradMendekati.addColorStop(1, '#d97706');

            const gradJatuhTempo = ctxStatus.createLinearGradient(0, 0, 0, 200);
            gradJatuhTempo.addColorStop(0, '#f87171');
            gradJatuhTempo.addColorStop(1, '#dc2626');

            new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: ['Aman & Layak', 'Mendekati Jatuh Tempo', 'Perlu Perawatan'],
                    datasets: [{
                        data: [{{ $jumlahAman }}, {{ $jumlahMendekati }}, {{ $jumlahJatuhTempo }}],
                        backgroundColor: [gradAman, gradMendekati, gradJatuhTempo],
                        borderWidth: 0,
                        borderRadius: 10,
                        spacing: 4,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '76%',
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
                                label: function (context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const value = context.parsed;
                                    const percent = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return ' ' + context.label + ': ' + value + ' unit (' + percent + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>

<style>
.stat-card-cream {
    background: #FAF7F2;
    border: 1px solid #EAE3D2;
    border-radius: 16px;
    padding: 10px 10px;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.02);
    min-width: 0;
}
.stat-card-cream:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(5, 150, 105, 0.1);
    border-color: #10B981;
}
.stat-icon-cream {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
}
.stat-icon-cream.green {
    background: rgba(5, 150, 105, 0.12);
    color: #059669;
}
.stat-icon-cream.emerald {
    background: rgba(16, 185, 129, 0.12);
    color: #10B981;
}
.stat-icon-cream.red {
    background: rgba(244, 63, 94, 0.12);
    color: #F43F5E;
}
.stat-icon-cream.amber {
    background: rgba(245, 158, 11, 0.12);
    color: #D97706;
}
.stat-icon-cream.teal {
    background: rgba(14, 165, 233, 0.12);
    color: #0EA5E9;
}
.stat-label-cream {
    font-size: 9.5px;
    font-weight: 700;
    color: #78716C;
    line-height: 1.2;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    text-transform: uppercase;
    letter-spacing: 0.02em;
}
.stat-val-cream {
    font-size: 1.05rem;
    font-weight: 800;
    line-height: 1.1;
    font-family: 'Outfit', sans-serif;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.stat-val-cream.green { color: #047857; }
.stat-val-cream.emerald { color: #10B981; }
.stat-val-cream.red { color: #F43F5E; }
.stat-val-cream.amber { color: #D97706; }
.stat-val-cream.teal { color: #0EA5E9; }
.stat-val-cream .unit {
    font-size: 10px;
    font-weight: 600;
    color: #A8A29E;
    margin-left: 2px;
}

[data-theme="dark"] .stat-card-cream {
    background: #131F37;
    border-color: #243456;
}
[data-theme="dark"] .stat-label-cream {
    color: #94A3B8;
}
</style>
@endif
@endsection