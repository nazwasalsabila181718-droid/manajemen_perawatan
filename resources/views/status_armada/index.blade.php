@extends('layouts.app')

@section('title', 'Status Armada')
@section('page_title', 'Status Armada & Kelayakan Dokumen')
@section('page_subtitle', 'Pantau kelayakan jalan, tanggal jatuh tempo pajak/KIR, dan kondisi operasional armada.')

@section('content')
<div class="container-fluid p-0">

    @php
        $semuaKendaraan = $kendaraans;
        $totalArmada = $semuaKendaraan->count();
        $jumlahAman = 0;
        $jumlahMendekati = 0;
        $jumlahJatuhTempo = 0;

        foreach ($semuaKendaraan as $kendaraan) {
            $pajakJatuhTempo = \Carbon\Carbon::parse($kendaraan->pajak_tahunan);
            $kirJatuhTempo = $kendaraan->kir_bengkel ? \Carbon\Carbon::parse($kendaraan->kir_bengkel) : null;

            $isPajakOverdue = $pajakJatuhTempo->isPast();
            $isPajakNear = !$isPajakOverdue && $pajakJatuhTempo->diffInDays(now()) <= 30;

            $isKirOverdue = $kirJatuhTempo ? $kirJatuhTempo->isPast() : false;
            $isKirNear = $kirJatuhTempo && !$isKirOverdue ? $kirJatuhTempo->diffInDays(now()) <= 30 : false;

            $isOdoTinggi = $kendaraan->odometer_terakhir >= 100000;

            if ($isPajakOverdue || $isKirOverdue || $isOdoTinggi) {
                $jumlahJatuhTempo++;
            } elseif ($isPajakNear || $isKirNear) {
                $jumlahMendekati++;
            } else {
                $jumlahAman++;
            }
        }
    @endphp

    <!-- Chart & Status Summary Section -->
    <div class="card-premium p-4 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <div>
                <h5 class="fw-bold mb-1 d-flex align-items-center gap-2">
                    <i class="bi bi-pie-chart-fill text-primary"></i> Distribusi Status Kelayakan Kendaraan
                </h5>
                <p class="text-secondary small mb-0">Sebaran armada berdasarkan indikator kelayakan: Safe, Near Overdue, dan Service/Tax Overdue.</p>
            </div>
            <div>
                <span class="badge-premium indigo py-2 px-3 fs-6"><i class="bi bi-truck me-1"></i> Total: {{ $totalArmada }} Unit</span>
            </div>
        </div>

        <div class="row align-items-center g-4">
            <!-- Doughnut Chart Container -->
            <div class="col-lg-5 col-md-6 position-relative d-flex justify-content-center">
                <div style="position: relative; height: 260px; width: 260px;">
                    <!-- Ambient Glow Behind Ring -->
                    <div style="position: absolute; inset: 0; border-radius: 50%; background: radial-gradient(circle, rgba(37, 99, 235, 0.12) 0%, rgba(13, 148, 136, 0.05) 50%, transparent 75%); filter: blur(12px); pointer-events: none;"></div>
                    
                    @if($totalArmada > 0)
                        <canvas id="statusArmadaChart" style="position: relative; z-index: 2;"></canvas>
                        
                        <!-- Premium Center Glass Ring -->
                        <div class="position-absolute top-50 start-50 translate-middle text-center pointer-events-none d-flex flex-column align-items-center justify-content-center" 
                             style="z-index: 3; width: 148px; height: 148px; border-radius: 50%; background: var(--bg-secondary); border: 2px solid var(--border-color); box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08);">
                            
                            <div class="badge-premium emerald mb-1" style="font-size: 10.5px; padding: 3px 10px;">
                                <i class="bi bi-shield-check me-1"></i>{{ $totalArmada > 0 ? round(($jumlahAman / $totalArmada) * 100) : 0 }}% Layak
                            </div>
                            
                            <div class="fs-1 fw-bold text-primary" style="line-height: 1; font-family: 'Outfit', sans-serif;">{{ $totalArmada }}</div>
                            <div class="text-muted fw-bold mt-1" style="font-size: 10px; letter-spacing: 0.08em; text-transform: uppercase;">Total Armada</div>
                        </div>
                    @else
                        <div class="d-flex justify-content-center align-items-center h-100 text-muted small">
                            Belum ada data armada.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Legend Summary Cards -->
            <div class="col-lg-7 col-md-6 d-flex flex-column gap-3">
                
                <!-- Status Aman -->
                <div class="d-flex align-items-center p-3 rounded-4 border transition-all" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.06) 0%, rgba(16, 185, 129, 0.02) 100%); border-color: rgba(16, 185, 129, 0.25) !important;">
                    <div class="stat-icon-wrapper emerald me-3" style="width: 44px; height: 44px; font-size: 20px;">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold text-success d-flex align-items-center gap-2">
                            Aman & Layak Jalan
                        </div>
                        <div class="text-secondary small">Seluruh dokumen aktif dan odometer dalam batas normal</div>
                    </div>
                    <div class="fw-bold fs-3 text-success px-2">{{ $jumlahAman }}</div>
                </div>

                <!-- Status Mendekati Jatuh Tempo -->
                <div class="d-flex align-items-center p-3 rounded-4 border transition-all" style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.06) 0%, rgba(245, 158, 11, 0.02) 100%); border-color: rgba(245, 158, 11, 0.25) !important;">
                    <div class="stat-icon-wrapper amber me-3" style="width: 44px; height: 44px; font-size: 20px;">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold text-warning d-flex align-items-center gap-2">
                            Mendekati Jatuh Tempo
                        </div>
                        <div class="text-secondary small">Pajak atau KIR akan habis dalam &le;30 hari</div>
                    </div>
                    <div class="fw-bold fs-3 text-warning px-2">{{ $jumlahMendekati }}</div>
                </div>

                <!-- Status Jatuh Tempo / Perlu Perawatan -->
                <div class="d-flex align-items-center p-3 rounded-4 border transition-all" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.06) 0%, rgba(239, 68, 68, 0.02) 100%); border-color: rgba(239, 68, 68, 0.25) !important;">
                    <div class="stat-icon-wrapper rose me-3" style="width: 44px; height: 44px; font-size: 20px;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold text-danger d-flex align-items-center gap-2">
                            Jatuh Tempo / Perlu Perawatan
                        </div>
                        <div class="text-secondary small">Pajak/KIR sudah lewat waktu, atau odometer &ge;100.000 km</div>
                    </div>
                    <div class="fw-bold fs-3 text-danger px-2">{{ $jumlahJatuhTempo }}</div>
                </div>

            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="card-premium p-0 overflow-hidden">
        <div class="p-4 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-3">
            <div>
                <h5 class="fw-bold mb-1 d-flex align-items-center gap-2">
                    <i class="bi bi-speedometer2 text-primary"></i> Monitoring Status Real-time
                </h5>
                <p class="text-secondary small mb-0">Daftar rinci seluruh kendaraan operasional dan indikator kondisinya.</p>
            </div>
            
            <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-3">
                <!-- Filter Pills -->
                <div class="premium-filter-pills">
                    <button class="filter-pill-btn active" data-filter="all">
                        <i class="bi bi-grid-1x2-fill me-1"></i> Semua
                    </button>
                    <button class="filter-pill-btn" data-filter="mobil">
                        <i class="bi bi-car-front-fill me-1"></i> Mobil
                    </button>
                    <button class="filter-pill-btn" data-filter="motor">
                        <i class="bi bi-bicycle me-1"></i> Motor
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
                        $pajakDate = \Carbon\Carbon::parse($kendaraan->pajak_tahunan);
                        $pajak5Date = $kendaraan->pajak_5_tahunan ? \Carbon\Carbon::parse($kendaraan->pajak_5_tahunan) : null;
                        $kirDate = $kendaraan->kir_bengkel ? \Carbon\Carbon::parse($kendaraan->kir_bengkel) : null;

                        // Check status for each document
                        $isStnkOverdue = $stnkDate ? $stnkDate->isPast() : false;
                        $isStnkNear = $stnkDate && !$isStnkOverdue && $stnkDate->diffInDays(now()) <= 30;

                        $isPajakOverdue = $pajakDate->isPast();
                        $isPajakNear = !$isPajakOverdue && $pajakDate->diffInDays(now()) <= 30;

                        $isPajak5Overdue = $pajak5Date ? $pajak5Date->isPast() : false;
                        $isPajak5Near = $pajak5Date && !$isPajak5Overdue && $pajak5Date->diffInDays(now()) <= 30;

                        $isKirOverdue = $kirDate ? $kirDate->isPast() : false;
                        $isKirNear = $kirDate && !$isKirOverdue && $kirDate->diffInDays(now()) <= 30;

                        $isOdoTinggi = $kendaraan->odometer_terakhir >= 100000;

                        // Overall status
                        $isOverdueAny = $isStnkOverdue || $isPajakOverdue || $isPajak5Overdue || $isKirOverdue || $isOdoTinggi;
                        $isNearAny = $isStnkNear || $isPajakNear || $isPajak5Near || $isKirNear;
                    @endphp
                    <tr class="status-row" data-jenis="{{ $kendaraan->jenis_kendaraan }}">
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
                                <div>
                                    <span class="text-secondary" style="display:inline-block; width:80px;">Pajak:</span>
                                    <span class="fw-semibold @if($isPajakOverdue) text-danger @elseif($isPajakNear) text-warning @else text-success @endif">
                                        {{ $pajakDate->format('d M Y') }}
                                        @if($isPajakOverdue) (LEWAT) @elseif($isPajakNear) (H-{{ $pajakDate->diffInDays(now()) }}) @endif
                                    </span>
                                </div>

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

@if($totalArmada > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const canvas = document.getElementById('statusArmadaChart');
        const ctx = canvas.getContext('2d');

        // Create Gradient Colors for Slices
        const gradAman = ctx.createLinearGradient(0, 0, 0, 260);
        gradAman.addColorStop(0, '#10b981');
        gradAman.addColorStop(1, '#059669');

        const gradMendekati = ctx.createLinearGradient(0, 0, 0, 260);
        gradMendekati.addColorStop(0, '#fbbf24');
        gradMendekati.addColorStop(1, '#d97706');

        const gradJatuhTempo = ctx.createLinearGradient(0, 0, 0, 260);
        gradJatuhTempo.addColorStop(0, '#f87171');
        gradJatuhTempo.addColorStop(1, '#dc2626');

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Aman & Layak', 'Mendekati Jatuh Tempo', 'Perlu Perawatan'],
                datasets: [{
                    data: [{{ $jumlahAman }}, {{ $jumlahMendekati }}, {{ $jumlahJatuhTempo }}],
                    backgroundColor: [gradAman, gradMendekati, gradJatuhTempo],
                    borderWidth: 0,
                    borderRadius: 14,
                    spacing: 6,
                    hoverOffset: 12
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '78%',
                animation: {
                    animateScale: true,
                    animateRotate: true,
                    duration: 1200,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        padding: 14,
                        cornerRadius: 12,
                        backgroundColor: '#0f172a',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        titleFont: { family: 'Plus Jakarta Sans', size: 13, weight: 'bold' },
                        bodyFont: { family: 'Plus Jakarta Sans', size: 12 },
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
    });

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

            // Tab filter match
            let matchesFilter = false;
            if (currentFilter === 'all') {
                matchesFilter = true;
            } else if (currentFilter === 'mobil') {
                matchesFilter = jenis.includes('mobil') || jenis.includes('boks') || jenis.includes('carry') || jenis.includes('truk') || jenis.includes('dinas');
            } else if (currentFilter === 'motor') {
                matchesFilter = jenis.includes('motor') || jenis.includes('sepeda');
            }

            if (matchesSearch && matchesFilter) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const filterBtns = document.querySelectorAll('.filter-pill-btn');
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
@endif
@endsection