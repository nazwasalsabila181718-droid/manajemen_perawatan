@extends('layouts.app')

@section('title', 'Jadwal Perawatan')
@section('page_title', 'Jadwal Perawatan Armada')
@section('page_subtitle', 'Monitoring & kalender berkala penggantian oli, aki, ban, dan komponen armada.')

@section('content')
<div class="container-fluid p-0">

    <!-- 1. RINGKASAN STATISTIK DOKUMEN & PERAWATAN -->
    @php
        $countTerlambat = $jadwal->filter(fn($i) => $i->status() === 'terlambat')->count();
        $countSegera    = $jadwal->filter(fn($i) => $i->status() === 'segera')->count();
        $countAman      = $jadwal->filter(fn($i) => $i->status() === 'aman')->count();
        $countTotal     = $jadwal->count();
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-semibold small d-block mb-1">Total Jadwal</span>
                        <h3 class="fw-extrabold text-dark mb-0">{{ $countTotal }}</h3>
                    </div>
                    <div class="rounded-3 p-2.5 bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-calendar-week fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, #fff5f5 0%, #fef2f2 100%);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-danger fw-semibold small d-block mb-1">Terlambat</span>
                        <h3 class="fw-extrabold text-danger mb-0">{{ $countTerlambat }}</h3>
                    </div>
                    <div class="rounded-3 p-2.5 bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-exclamation-octagon fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-warning fw-semibold small d-block mb-1">Segera Ganti</span>
                        <h3 class="fw-extrabold text-warning mb-0" style="color: #d97706 !important;">{{ $countSegera }}</h3>
                    </div>
                    <div class="rounded-3 p-2.5 bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-success fw-semibold small d-block mb-1">Status Aman</span>
                        <h3 class="fw-extrabold text-success mb-0">{{ $countAman }}</h3>
                    </div>
                    <div class="rounded-3 p-2.5 bg-success bg-opacity-10 text-success">
                        <i class="bi bi-check-circle fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. WIDGET KALENDER PEMELIHARAAN INTERAKTIF (DESIGN KEKINIAN) -->
    <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden" style="background: var(--bg-primary, #ffffff);">
        <div class="p-3 px-4 border-bottom d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #ffffff;">
            
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(8px);">
                    <i class="bi bi-calendar-event text-warning fs-5"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-white" style="letter-spacing: -0.2px;">Kalender Pemeliharaan Armada</h6>
                    <small class="text-white-50" style="font-size: 11.5px;">Klik item tanggal untuk detail atau catat perawatan</small>
                </div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-light border-0 shadow-sm rounded-circle" onclick="prevMonth()" title="Bulan Sebelumnya" style="width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.9);">
                    <i class="bi bi-chevron-left text-dark"></i>
                </button>
                <h6 class="fw-bold mb-0 text-white px-2" id="calendarMonthTitle" style="min-width: 150px; text-align: center; font-size: 15px;">Agustus 2026</h6>
                <button type="button" class="btn btn-sm btn-light border-0 shadow-sm rounded-circle" onclick="nextMonth()" title="Bulan Berikutnya" style="width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.9);">
                    <i class="bi bi-chevron-right text-dark"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-light ms-2 px-3 rounded-3" onclick="goToToday()" style="font-size: 12px; border-color: rgba(255,255,255,0.3);">
                    <i class="bi bi-arrow-clockwise me-1"></i> Hari Ini
                </button>
            </div>
        </div>

        <!-- Filter & Legend Bar -->
        <div class="p-3 border-bottom d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3" style="background: var(--bg-secondary, #f8fafc);">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <div class="d-flex align-items-center gap-1.5 small fw-semibold">
                    <span class="badge rounded-circle p-1 bg-danger d-inline-block"></span> Terlambat
                </div>
                <div class="d-flex align-items-center gap-1.5 small fw-semibold">
                    <span class="badge rounded-circle p-1 bg-warning d-inline-block"></span> Segera Ganti
                </div>
                <div class="d-flex align-items-center gap-1.5 small fw-semibold">
                    <span class="badge rounded-circle p-1 bg-success d-inline-block"></span> Safe / Aman
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <select id="calendarStatusFilter" class="form-select form-select-sm rounded-3 border-secondary-subtle" onchange="renderCalendar()" style="min-width: 140px; font-size: 12px;">
                    <option value="all">Semua Status</option>
                    <option value="terlambat">🔴 Terlambat</option>
                    <option value="segera">🟡 Segera Ganti</option>
                    <option value="aman">🟢 Aman</option>
                </select>
            </div>
        </div>

        <div class="p-3">
            <!-- Calendar Grid Header (Hari) -->
            <div class="calendar-grid-header border-bottom pb-2 mb-2">
                <div class="cal-day-name text-danger">Minggu</div>
                <div class="cal-day-name">Senin</div>
                <div class="cal-day-name">Selasa</div>
                <div class="cal-day-name">Rabu</div>
                <div class="cal-day-name">Kamis</div>
                <div class="cal-day-name">Jumat</div>
                <div class="cal-day-name text-primary">Sabtu</div>
            </div>

            <!-- Calendar Grid Body (Tanggal) -->
            <div class="calendar-grid-body" id="calendarGrid">
                <!-- Dynamically populated via JS -->
            </div>
        </div>
    </div>

    <!-- 3. TABEL DAFTAR JADWAL PEMELIHARAAN INTEGRASI FOTO ARMADA -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: var(--bg-primary, #ffffff);">
        <div class="p-4 border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
            <div>
                <h6 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <i class="bi bi-list-stars text-primary"></i> Daftar Detail Jadwal Pemeliharaan
                </h6>
                <p class="text-muted small mb-0">Seluruh komponen berkala dan status odometer armada aktif.</p>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <form method="GET" class="m-0">
                    <select name="kendaraan_id" class="form-select form-select-sm rounded-3 py-1.5" onchange="this.form.submit()" style="max-width: 240px;">
                        <option value="">-- Semua Kendaraan --</option>
                        @foreach ($kendaraans as $k)
                            <option value="{{ $k->id }}" {{ request('kendaraan_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nomor_polisi }} ({{ $k->merek }})
                            </option>
                        @endforeach
                    </select>
                </form>

                @if(auth()->user() && in_array(auth()->user()->role, ['administrator', 'teknisi']))
                <a href="{{ route('jadwal-perawatan.create') }}" class="btn btn-primary btn-sm rounded-3 px-3">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Jadwal
                </a>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tabelJadwal">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Armada / Kendaraan</th>
                        <th>Jenis Perawatan</th>
                        <th>Estimasi Odometer</th>
                        <th>Estimasi Tanggal</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jadwal as $item)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                @if(!empty($item->kendaraan->foto_kendaraan))
                                    <img src="{{ asset('storage/' . $item->kendaraan->foto_kendaraan) }}" 
                                         alt="Foto {{ $item->kendaraan->nomor_polisi }}" 
                                         class="rounded-3 shadow-sm border object-fit-cover" 
                                         style="width: 48px; height: 38px;">
                                @else
                                    <div class="rounded-3 shadow-sm d-flex align-items-center justify-content-center bg-light border text-secondary" style="width: 48px; height: 38px;">
                                        <i class="bi {{ strtolower($item->kendaraan->jenis_kendaraan ?? '') == 'motor' ? 'bi-scooter' : 'bi-car-front-fill' }} fs-5"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-bold text-dark">{{ $item->kendaraan->nomor_polisi ?? '-' }}</div>
                                    <div class="text-muted small" style="font-size: 11px;">
                                        {{ $item->kendaraan->merek ?? '' }} {{ $item->kendaraan->tipe ?? '' }} 
                                        <span class="badge bg-light text-dark border ms-1 py-0.5" style="font-size: 10px;">{{ $item->kendaraan->jenis_kendaraan ?? 'Armada' }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="fw-semibold text-dark">
                            <i class="bi bi-wrench text-primary me-1"></i> {{ $item->jenis_perawatan }}
                        </td>
                        <!-- Estimasi Odometer -->
                        <td>
                            @if($item->km_terakhir && $item->interval_km)
                                @php 
                                    $nextKm = $item->km_terakhir + $item->interval_km; 
                                    $sisaKm = $item->sisaKm();
                                @endphp
                                <div class="fw-bold text-dark">{{ number_format($nextKm, 0, ',', '.') }} KM</div>
                                <div class="text-muted small" style="font-size: 11px;">
                                    Terakhir: {{ number_format($item->km_terakhir, 0, ',', '.') }} KM (Tiap {{ number_format($item->interval_km, 0, ',', '.') }} KM)
                                </div>
                                @if($sisaKm !== null)
                                    @if($sisaKm < 0)
                                        <span class="badge bg-danger-subtle text-danger fw-bold py-1 px-2 mt-1" style="font-size: 10.5px; border-radius: 6px;">Terlewat {{ number_format(abs($sisaKm), 0, ',', '.') }} KM</span>
                                    @else
                                        <span class="badge bg-success-subtle text-success fw-bold py-1 px-2 mt-1" style="font-size: 10.5px; border-radius: 6px;">Sisa {{ number_format($sisaKm, 0, ',', '.') }} KM</span>
                                    @endif
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <!-- Estimasi Tanggal -->
                        <td>
                            @if($item->tanggal_terakhir && $item->interval_bulan)
                                @php 
                                    $nextDate = $item->tanggal_terakhir->copy()->addMonths($item->interval_bulan); 
                                    $sisaHari = $item->sisaHari();
                                @endphp
                                <div class="fw-bold text-dark">{{ $nextDate->format('d M Y') }}</div>
                                <div class="text-muted small" style="font-size: 11px;">
                                    Terakhir: {{ $item->tanggal_terakhir->format('d M Y') }} (Tiap {{ $item->interval_bulan }} Bulan)
                                </div>
                                @if($sisaHari !== null)
                                    @if($sisaHari < 0)
                                        <span class="badge bg-danger-subtle text-danger fw-bold py-1 px-2 mt-1" style="font-size: 10.5px; border-radius: 6px;">Terlewat {{ abs($sisaHari) }} Hari</span>
                                    @else
                                        <span class="badge bg-success-subtle text-success fw-bold py-1 px-2 mt-1" style="font-size: 10.5px; border-radius: 6px;">Sisa {{ $sisaHari }} Hari</span>
                                    @endif
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $status = $item->status();
                            @endphp
                            @if($status == 'terlambat')
                                <span class="badge bg-danger text-white rounded-pill px-3 py-1.5"><i class="bi bi-exclamation-triangle-fill me-1"></i> Terlambat</span>
                            @elseif($status == 'segera')
                                <span class="badge bg-warning text-dark rounded-pill px-3 py-1.5" style="background-color: #f59e0b !important; color: white !important;"><i class="bi bi-clock-fill me-1"></i> Segera Ganti</span>
                            @else
                                <span class="badge bg-success text-white rounded-pill px-3 py-1.5"><i class="bi bi-check-circle-fill me-1"></i> Aman</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-3"
                                    data-bs-toggle="modal" data-bs-target="#modalUpdate{{ $item->id }}">
                                <i class="bi bi-check2-circle me-1"></i> Catat Ganti
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-calendar-x fs-2 d-block mb-2 opacity-50"></i>
                            Belum ada jadwal perawatan terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modals Catat Penggantian dengan Foto Kendaraan -->
@foreach ($jadwal as $item)
<div class="modal fade" id="modalUpdate{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form method="POST" action="{{ route('jadwal-perawatan.update', $item) }}">
                @csrf
                @method('PATCH')
                <div class="modal-header bg-dark text-white p-4">
                    <h5 class="modal-title fw-bold text-white fs-6 d-flex align-items-center gap-2">
                        <i class="bi bi-tools text-warning fs-5"></i> Catat Penggantian Component
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-3 border" style="background: #f8fafc;">
                        @if(!empty($item->kendaraan->foto_kendaraan))
                            <img src="{{ asset('storage/' . $item->kendaraan->foto_kendaraan) }}" 
                                 alt="Foto {{ $item->kendaraan->nomor_polisi }}" 
                                 class="rounded-3 shadow-sm border object-fit-cover" 
                                 style="width: 70px; height: 55px;">
                        @else
                            <div class="rounded-3 shadow-sm d-flex align-items-center justify-content-center bg-white border text-secondary" style="width: 70px; height: 55px;">
                                <i class="bi {{ strtolower($item->kendaraan->jenis_kendaraan ?? '') == 'motor' ? 'bi-scooter' : 'bi-car-front-fill' }} fs-3"></i>
                            </div>
                        @endif
                        <div>
                            <div class="fw-extrabold text-primary fs-5 mb-0">{{ $item->kendaraan->nomor_polisi ?? '-' }}</div>
                            <div class="text-dark fw-semibold small">{{ $item->kendaraan->merek ?? '' }} {{ $item->kendaraan->tipe ?? '' }}</div>
                            <div class="text-muted small">Komponen: <span class="badge bg-primary-subtle text-primary fw-bold">{{ $item->jenis_perawatan }}</span></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Angka Odometer / KM Saat Ini</label>
                        <input type="number" name="km_terakhir" class="form-control rounded-3 py-2"
                               value="{{ $item->kendaraan->odometer_terakhir ?? $item->km_terakhir }}" required>
                        <small class="text-muted" style="font-size: 11px;">Masukkan angka tanpa titik/koma (contoh: 8500)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Tanggal Penggantian</label>
                        <input type="date" name="tanggal_terakhir" class="form-control rounded-3 py-2"
                               value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small text-dark">Catatan Tambahan (Opsional)</label>
                        <textarea name="catatan" class="form-control rounded-3" rows="2" placeholder="Nama bengkel, garansi, atau merk suku cadang"></textarea>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3 bg-light">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold">Simpan Penggantian</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<style>
.calendar-grid-header {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    text-align: center;
    font-weight: 700;
    font-size: 12px;
    background: var(--bg-secondary, #f8fafc);
    border-radius: 8px;
}
.cal-day-name {
    padding: 8px 2px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
.calendar-grid-body {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    grid-auto-rows: minmax(105px, auto);
    background: #e2e8f0;
    gap: 1.5px;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: inset 0 0 0 1px #e2e8f0;
}
.cal-day-cell {
    background: var(--bg-primary, #ffffff);
    padding: 6px;
    display: flex;
    flex-direction: column;
    position: relative;
    transition: all 0.2s ease;
    min-height: 105px;
}
.cal-day-cell:hover {
    background: rgba(37, 99, 235, 0.03);
    z-index: 2;
}
.cal-day-cell.other-month {
    background: #f8fafc;
    opacity: 0.45;
}
.cal-day-cell.today {
    background: rgba(37, 99, 235, 0.06);
    border: 1.5px solid #2563eb;
    border-radius: 6px;
}
.cal-day-num {
    font-size: 12px;
    font-weight: 700;
    margin-bottom: 4px;
    align-self: flex-end;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    color: var(--text-primary, #1e293b);
}
.cal-day-cell.today .cal-day-num {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(37, 99, 235, 0.4);
}
.cal-event-pill {
    font-size: 10px;
    padding: 3.5px 6px;
    border-radius: 6px;
    margin-bottom: 3px;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    cursor: pointer;
    transition: all 0.18s ease;
    border: 1px solid transparent;
    display: flex;
    align-items: center;
    gap: 4px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
}
.cal-event-pill:hover {
    transform: translateY(-1.5px) scale(1.02);
    box-shadow: 0 4px 10px rgba(0,0,0,0.12);
}
.cal-event-pill.danger {
    background: #fef2f2;
    color: #dc2626;
    border-color: #fecaca;
}
.cal-event-pill.warning {
    background: #fffbeb;
    color: #d97706;
    border-color: #fde68a;
}
.cal-event-pill.success {
    background: #f0fdf4;
    color: #16a34a;
    border-color: #bbf7d0;
}
.cal-event-img {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
    border: 1px solid rgba(0,0,0,0.1);
}
.cal-event-icon {
    font-size: 11px;
    flex-shrink: 0;
}
</style>
@endsection

@section('scripts')
<script>
    // Prepare Schedule Events Data from PHP including vehicle photos
    const scheduleEvents = [
        @foreach ($jadwal as $item)
            @if($item->tanggal_terakhir && $item->interval_bulan)
                @php 
                    $nextDate = $item->tanggal_terakhir->copy()->addMonths($item->interval_bulan); 
                    $badgeClass = str_contains($item->statusBadgeClass(), 'danger') ? 'danger' : (str_contains($item->statusBadgeClass(), 'warning') ? 'warning' : 'success');
                    $statusKey = $item->status();
                @endphp
                {
                    id: {{ $item->id }},
                    nopol: "{{ $item->kendaraan->nomor_polisi ?? 'Armada' }}",
                    merek: "{{ $item->kendaraan->merek ?? '' }}",
                    jenisKendaraan: "{{ strtolower($item->kendaraan->jenis_kendaraan ?? 'mobil') }}",
                    foto: "{{ !empty($item->kendaraan->foto_kendaraan) ? asset('storage/' . $item->kendaraan->foto_kendaraan) : '' }}",
                    jenis: "{{ $item->jenis_perawatan }}",
                    date: "{{ $nextDate->format('Y-m-d') }}",
                    badgeClass: "{{ $badgeClass }}",
                    statusKey: "{{ $statusKey }}",
                    statusLabel: "{{ $item->statusLabel() }}"
                },
            @endif
        @endforeach
    ];

    let currentCalDate = new Date();
    // Default start at current calendar date or first event date if present
    if (scheduleEvents.length > 0) {
        const firstEventDate = new Date(scheduleEvents[0].date);
        if (!isNaN(firstEventDate.getTime())) {
            currentCalDate = firstEventDate;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        renderCalendar();
    });

    function renderCalendar() {
        const grid = document.getElementById('calendarGrid');
        const monthTitle = document.getElementById('calendarMonthTitle');
        const statusFilter = document.getElementById('calendarStatusFilter')?.value || 'all';

        if (!grid || !monthTitle) return;

        grid.innerHTML = '';

        const year = currentCalDate.getFullYear();
        const month = currentCalDate.getMonth();

        const monthNames = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        monthTitle.textContent = `${monthNames[month]} ${year}`;

        const firstDayOfMonth = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const daysInPrevMonth = new Date(year, month, 0).getDate();

        const today = new Date();
        const todayStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;

        // Filter events based on selected status
        const filteredEvents = scheduleEvents.filter(ev => {
            if (statusFilter === 'all') return true;
            return ev.statusKey === statusFilter;
        });

        // 1. Previous Month Days
        for (let i = firstDayOfMonth - 1; i >= 0; i--) {
            const dayNum = daysInPrevMonth - i;
            const prevMonthDate = new Date(year, month - 1, dayNum);
            const dateStr = formatDateStr(prevMonthDate);

            const cell = createDayCell(dayNum, true, false, dateStr, filteredEvents);
            grid.appendChild(cell);
        }

        // 2. Current Month Days
        for (let day = 1; day <= daysInMonth; day++) {
            const currDate = new Date(year, month, day);
            const dateStr = formatDateStr(currDate);
            const isToday = (dateStr === todayStr);

            const cell = createDayCell(day, false, isToday, dateStr, filteredEvents);
            grid.appendChild(cell);
        }

        // 3. Next Month Days
        const totalCells = grid.children.length;
        const remainingCells = (totalCells % 7 === 0) ? 0 : 7 - (totalCells % 7);
        for (let day = 1; day <= remainingCells; day++) {
            const nextMonthDate = new Date(year, month + 1, day);
            const dateStr = formatDateStr(nextMonthDate);

            const cell = createDayCell(day, true, false, dateStr, filteredEvents);
            grid.appendChild(cell);
        }
    }

    function createDayCell(dayNum, isOtherMonth, isToday, dateStr, eventsList) {
        const cell = document.createElement('div');
        cell.className = `cal-day-cell ${isOtherMonth ? 'other-month' : ''} ${isToday ? 'today' : ''}`;

        const numDiv = document.createElement('div');
        numDiv.className = 'cal-day-num';
        numDiv.textContent = dayNum;
        cell.appendChild(numDiv);

        // Events on this date
        const dayEvents = eventsList.filter(ev => ev.date === dateStr);
        dayEvents.forEach(ev => {
            const pill = document.createElement('div');
            pill.className = `cal-event-pill ${ev.badgeClass}`;

            let iconOrImgHtml = '';
            if (ev.foto) {
                iconOrImgHtml = `<img src="${ev.foto}" class="cal-event-img" alt="${ev.nopol}">`;
            } else {
                const iconClass = (ev.jenisKendaraan === 'motor') ? 'bi-scooter' : 'bi-car-front-fill';
                iconOrImgHtml = `<i class="bi ${iconClass} cal-event-icon"></i>`;
            }

            pill.innerHTML = `${iconOrImgHtml} <span><strong>${ev.nopol}</strong> — ${ev.jenis}</span>`;
            pill.title = `${ev.nopol} (${ev.merek}) - ${ev.jenis}\nStatus: ${ev.statusLabel}`;
            pill.onclick = (e) => {
                e.stopPropagation();
                openModalUpdate(ev.id);
            };
            cell.appendChild(pill);
        });

        return cell;
    }

    function formatDateStr(d) {
        const y = d.getFullYear();
        const m = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${y}-${m}-${day}`;
    }

    function prevMonth() {
        currentCalDate.setMonth(currentCalDate.getMonth() - 1);
        renderCalendar();
    }

    function nextMonth() {
        currentCalDate.setMonth(currentCalDate.getMonth() + 1);
        renderCalendar();
    }

    function goToToday() {
        currentCalDate = new Date();
        renderCalendar();
    }

    function openModalUpdate(id) {
        const modalEl = document.getElementById(`modalUpdate${id}`);
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    }
</script>
@endsection