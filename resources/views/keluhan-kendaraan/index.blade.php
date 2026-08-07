@extends('layouts.app')

@section('title', 'Keluhan Kendaraan')
@section('page_title', 'Keluhan & Kendala Armada')
@section('page_subtitle', 'Laporkan dan pantau keluhan kerusakan atau masalah teknis kendaraan.')

@section('content')
<div class="container-fluid p-0">

    <div class="card-premium p-0 overflow-hidden mb-4">
        <div class="p-4 border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <span class="badge-premium danger">Baru</span>
                <span class="badge-premium indigo">Diproses</span>
                <span class="badge-premium success">Selesai</span>
            </div>

            <div class="d-flex align-items-center gap-3">
                <form method="GET" class="m-0">
                    <select name="status" class="form-control-premium py-1.5" onchange="this.form.submit()" style="max-width: 180px;">
                        <option value="">Semua Status</option>
                        <option value="baru" {{ request('status') == 'baru' ? 'selected' : '' }}>Baru</option>
                        <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>Diproses</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </form>

                <a href="{{ route('keluhan-kendaraan.create') }}" class="btn-premium primary">
                    <i class="bi bi-plus-lg"></i> Lapor Keluhan
                </a>
            </div>
        </div>

        <div class="table-responsive-premium border-0">
            <table class="table-premium" id="tabelKeluhan">
                <thead>
                    <tr>
                        <th>Waktu Lapor</th>
                        <th>Kendaraan</th>
                        <th>Pelapor</th>
                        <th>Deskripsi Keluhan</th>
                        <th>Urgensi</th>
                        <th>Status</th>
                        @if($bisaKelola)
                        <th class="text-end">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($keluhans as $item)
                    <tr>
                        <td class="small text-muted">{{ $item->created_at->format('d M Y, H:i') }}</td>
                        <td class="fw-bold text-primary">{{ $item->kendaraan->nomor_polisi }}</td>
                        <td class="fw-semibold">{{ $item->pelapor->name }}</td>
                        <td style="max-width: 300px;" class="small text-secondary">{{ $item->keluhan }}</td>
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
                        @if($bisaKelola)
                        <td class="text-end">
                            <button type="button" class="btn-premium secondary btn-sm"
                                    data-bs-toggle="modal" data-bs-target="#modalTindak{{ $item->id }}">
                                <i class="bi bi-tools"></i> Tindak Lanjuti
                            </button>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $bisaKelola ? 7 : 6 }}" class="text-center text-muted py-5">
                            <i class="bi bi-check-circle fs-2 d-block mb-2 opacity-50 text-success"></i>
                            Tidak ada keluhan kendaraan saat ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modals Tindak Lanjut -->
@if($bisaKelola)
@foreach ($keluhans as $item)
<div class="modal fade" id="modalTindak{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('keluhan-kendaraan.update', $item) }}">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-wrench-adjustable text-primary me-2"></i> Penanganan Keluhan — {{ $item->kendaraan->nomor_polisi }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 rounded-3 mb-3 border" style="background-color: var(--bg-primary);">
                        <div class="fw-bold small text-secondary">Isi Keluhan:</div>
                        <div class="text-primary mt-1" style="font-size: 14px;">{{ $item->keluhan }}</div>
                    </div>

                    <div class="form-group-premium">
                        <label class="form-label-premium">Status Penanganan</label>
                        <select name="status" class="form-control-premium" required>
                            <option value="baru" {{ $item->status === 'baru' ? 'selected' : '' }}>Baru / Menunggu</option>
                            <option value="diproses" {{ $item->status === 'diproses' ? 'selected' : '' }}>Diproses (Di Bengkel)</option>
                            <option value="selesai" {{ $item->status === 'selesai' ? 'selected' : '' }}>Selesai (Siap Operasional)</option>
                        </select>
                    </div>

                    <div class="form-group-premium mb-0">
                        <label class="form-label-premium">Catatan Penanganan & Solusi</label>
                        <textarea name="catatan_penanganan" class="form-control-premium" rows="3" placeholder="Deskripsikan perbaikan yang telah dilakukan...">{{ $item->catatan_penanganan }}</textarea>
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
@endforeach
@endif
@endsection