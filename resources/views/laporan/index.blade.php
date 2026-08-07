@extends('layouts.app')

@section('title', 'Laporan Analitik Akhir Bulan')
@section('page_title', 'Evaluasi & Analitik Biaya Bulanan')
@section('page_subtitle', 'Analisis komprehensif pengeluaran operasional bensin (BBM) dan efisiensi biaya perbaikan armada.')

@section('content')
<div class="container-fluid p-0">

    <!-- Overview Insights Row -->
    <div class="row g-4 mb-4">
        <!-- Insight 1: Bahan Bakar Terboros -->
        <div class="col-md-6 col-lg-4">
            <div class="card-premium h-100 p-4 insight-card-red" style="border-left: 4px solid var(--danger);">
                <div class="d-flex align-items-center mb-3">
                    <div class="stat-icon-wrapper rose me-3" style="width: 44px; height: 44px; font-size: 20px;">
                        <i class="bi bi-fuel-pump-fill"></i>
                    </div>
                    <div>
                        <div class="text-secondary fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">KONSUMSI BBM TERBOROS</div>
                        <h6 class="fw-bold mb-0 text-danger">Bensin Paling Boros</h6>
                    </div>
                </div>
                @if($kendaraanTerboros)
                    <div class="fs-4 fw-bold text-primary-custom mb-1">{{ $kendaraanTerboros->kendaraan ? $kendaraanTerboros->kendaraan->nomor_polisi : 'Lainnya' }}</div>
                    <div class="text-secondary small mb-3">Driver: {{ $kendaraanTerboros->kendaraan ? $kendaraanTerboros->kendaraan->nama_driver : '-' }}</div>
                    <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2">
                        <span class="small text-muted">Total Pengeluaran BBM:</span>
                        <span class="fw-bold text-danger fs-6">Rp {{ number_format($kendaraanTerboros->total_bbm, 0, ',', '.') }}</span>
                    </div>
                @else
                    <div class="text-muted py-4 small">Belum ada data pengeluaran BBM.</div>
                @endif
            </div>
        </div>

        <!-- Insight 2: Perbaikan Termahal -->
        <div class="col-md-6 col-lg-4">
            <div class="card-premium h-100 p-4 insight-card-blue" style="border-left: 4px solid var(--accent);">
                <div class="d-flex align-items-center mb-3">
                    <div class="stat-icon-wrapper indigo me-3" style="width: 44px; height: 44px; font-size: 20px;">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div>
                        <div class="text-secondary fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">BIAYA SERVIS TERMAHAL</div>
                        <h6 class="fw-bold mb-0 text-primary">Perbaikan Termahal</h6>
                    </div>
                </div>
                @if($kendaraanTermahal)
                    <div class="fs-4 fw-bold text-primary-custom mb-1">{{ $kendaraanTermahal->kendaraan ? $kendaraanTermahal->kendaraan->nomor_polisi : 'Lainnya' }}</div>
                    <div class="text-secondary small mb-3">Driver: {{ $kendaraanTermahal->kendaraan ? $kendaraanTermahal->kendaraan->nama_driver : '-' }}</div>
                    <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2">
                        <span class="small text-muted">Total Biaya Perbaikan:</span>
                        <span class="fw-bold text-primary fs-6">Rp {{ number_format($kendaraanTermahal->total_perbaikan, 0, ',', '.') }}</span>
                    </div>
                @else
                    <div class="text-muted py-4 small">Belum ada data biaya perbaikan.</div>
                @endif
            </div>
        </div>

        <!-- Insight 3: Paling Sering Masuk Bengkel -->
        <div class="col-md-6 col-lg-4">
            <div class="card-premium h-100 p-4 insight-card-amber" style="border-left: 4px solid var(--warning);">
                <div class="d-flex align-items-center mb-3">
                    <div class="stat-icon-wrapper amber me-3" style="width: 44px; height: 44px; font-size: 20px;">
                        <i class="bi bi-wrench-adjustable-circle"></i>
                    </div>
                    <div>
                        <div class="text-secondary fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">FREKUENSI BENGKEL TERTINGGI</div>
                        <h6 class="fw-bold mb-0 text-warning">Sering Masuk Bengkel</h6>
                    </div>
                </div>
                @if($kendaraanTersering)
                    <div class="fs-4 fw-bold text-primary-custom mb-1">{{ $kendaraanTersering->kendaraan ? $kendaraanTersering->kendaraan->nomor_polisi : 'Lainnya' }}</div>
                    <div class="text-secondary small mb-3">Driver: {{ $kendaraanTersering->kendaraan ? $kendaraanTersering->kendaraan->nama_driver : '-' }}</div>
                    <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2">
                        <span class="small text-muted">Intensitas Kunjungan:</span>
                        <span class="fw-bold text-warning fs-6">{{ $kendaraanTersering->frekuensi_bengkel }} Kali</span>
                    </div>
                @else
                    <div class="text-muted py-4 small">Belum ada data kunjungan bengkel.</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Chart 1: Konsumsi Bensin -->
        <div class="col-lg-6">
            <div class="card-premium p-4">
                <h5 class="fw-bold mb-1"><i class="bi bi-bar-chart-fill text-danger me-2"></i>Pengeluaran Bensin Bulanan</h5>
                <p class="text-secondary small mb-4">Grafik total pembelian bahan bakar per unit kendaraan bulan ini.</p>
                <div style="position: relative; height: 320px; width: 100%;">
                    @if(count($bbmLabels) > 0)
                        <canvas id="bbmChart"></canvas>
                    @else
                        <div class="d-flex justify-content-center align-items-center h-100 text-muted small">
                            Tidak ada data bensin untuk bulan ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Chart 2: Biaya & Frekuensi Perbaikan -->
        <div class="col-lg-6">
            <div class="card-premium p-4">
                <h5 class="fw-bold mb-1"><i class="bi bi-bar-chart-line-fill text-primary me-2"></i>Biaya & Frekuensi Servis</h5>
                <p class="text-secondary small mb-4">Analisis biaya servis (Batang) dan intensitas masuk bengkel (Garis) per kendaraan.</p>
                <div style="position: relative; height: 320px; width: 100%;">
                    @if(count($servisLabels) > 0)
                        <canvas id="servisChart"></canvas>
                    @else
                        <div class="d-flex justify-content-center align-items-center h-100 text-muted small">
                            Tidak ada data biaya servis untuk bulan ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Details Tables -->
    <div class="row g-4">
        <!-- Table 1: Detail Bensin -->
        <div class="col-lg-6">
            <div class="card-premium p-0 overflow-hidden">
                <div class="p-4 border-bottom">
                    <h5 class="fw-bold mb-0">Rincian Konsumsi Bensin</h5>
                </div>
                <div class="table-responsive-premium border-0">
                    <table class="table-premium">
                        <thead>
                            <tr>
                                <th>Kendaraan</th>
                                <th>Driver</th>
                                <th class="text-end">Total Biaya BBM</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bbmPerKendaraan as $item)
                            <tr>
                                <td class="fw-bold text-primary">{{ $item->kendaraan ? $item->kendaraan->nomor_polisi : 'Lainnya' }}</td>
                                <td class="fw-semibold">{{ $item->kendaraan ? $item->kendaraan->nama_driver : '-' }}</td>
                                <td class="fw-bold text-danger text-end">Rp {{ number_format($item->total_bbm, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4 small">Belum ada data pembelian BBM.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Table 2: Detail Servis -->
        <div class="col-lg-6">
            <div class="card-premium p-0 overflow-hidden">
                <div class="p-4 border-bottom">
                    <h5 class="fw-bold mb-0">Rincian Perbaikan & Servis</h5>
                </div>
                <div class="table-responsive-premium border-0">
                    <table class="table-premium">
                        <thead>
                            <tr>
                                <th>Kendaraan</th>
                                <th>Frekuensi Bengkel</th>
                                <th class="text-end">Total Biaya Perbaikan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($servisPerKendaraan as $item)
                            <tr>
                                <td class="fw-bold text-primary">{{ $item->kendaraan ? $item->kendaraan->nomor_polisi : 'Lainnya' }}</td>
                                <td class="fw-semibold text-center">{{ $item->frekuensi_bengkel }} kali</td>
                                <td class="fw-bold text-primary text-end">Rp {{ number_format($item->total_perbaikan, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4 small">Belum ada data perbaikan.</td>
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

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. BBM Chart ---
        @if(count($bbmLabels) > 0)
        const bbmCtx = document.getElementById('bbmChart').getContext('2d');
        new Chart(bbmCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($bbmLabels) !!},
                datasets: [{
                    label: 'Biaya BBM (Rp)',
                    data: {!! json_encode($bbmData) !!},
                    backgroundColor: 'rgba(239, 68, 68, 0.75)',
                    borderColor: 'rgb(239, 68, 68)',
                    borderWidth: 1.5,
                    borderRadius: 8,
                    maxBarThickness: 35
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 12,
                        cornerRadius: 10,
                        callbacks: {
                            label: function(context) {
                                return ' ' + new Intl.NumberFormat('id-ID', {
                                    style: 'currency', currency: 'IDR', maximumFractionDigits: 0
                                }).format(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(148, 163, 184, 0.1)' },
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(value);
                            }
                        }
                    }
                }
            }
        });
        @endif

        // --- 2. Servis Chart (Combo Chart) ---
        @if(count($servisLabels) > 0)
        const servisCtx = document.getElementById('servisChart').getContext('2d');
        new Chart(servisCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($servisLabels) !!},
                datasets: [
                    {
                        type: 'bar',
                        label: 'Total Biaya (Rp)',
                        data: {!! json_encode($servisCostData) !!},
                        backgroundColor: 'rgba(46, 111, 64, 0.75)',
                        borderColor: 'rgb(46, 111, 64)',
                        borderWidth: 1.5,
                        borderRadius: 8,
                        yAxisID: 'yCost',
                        maxBarThickness: 35
                    },
                    {
                        type: 'line',
                        label: 'Frekuensi Bengkel',
                        data: {!! json_encode($servisFreqData) !!},
                        borderColor: 'rgb(245, 158, 11)',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: 'rgb(245, 158, 11)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        tension: 0.3,
                        yAxisID: 'yFreq'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { boxWidth: 12, font: { family: 'Plus Jakarta Sans', size: 11 } }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 12,
                        cornerRadius: 10,
                        callbacks: {
                            label: function(context) {
                                if (context.datasetIndex === 0) {
                                    return ' Biaya: ' + new Intl.NumberFormat('id-ID', {
                                        style: 'currency', currency: 'IDR', maximumFractionDigits: 0
                                    }).format(context.parsed.y);
                                }
                                return ' Masuk Bengkel: ' + context.parsed.y + ' Kali';
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    yCost: {
                        position: 'left',
                        beginAtZero: true,
                        grid: { color: 'rgba(148, 163, 184, 0.1)' },
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(value);
                            }
                        },
                        title: { display: true, text: 'Biaya (Rp)', font: { size: 11, weight: 'bold' } }
                    },
                    yFreq: {
                        position: 'right',
                        beginAtZero: true,
                        grid: { display: false },
                        ticks: {
                            stepSize: 1,
                            precision: 0
                        },
                        title: { display: true, text: 'Frekuensi Kunjungan', font: { size: 11, weight: 'bold' } }
                    }
                }
            }
        });
        @endif
    });
</script>
@endsection