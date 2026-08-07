@extends('layouts.app')

@section('title', 'Checklist Harian')
@section('page_title', 'Checklist Harian (Pre-trip Inspection)')
@section('page_subtitle', 'Periksa kondisi fisik dan fungsi kendaraan sebelum keberangkatan.')

@section('content')
<div class="container-fluid p-0">

    @php
        $sections = [
            'Cairan & Pelumas' => [
                'cairan_oli_mesin' => 'Oli Mesin',
                'cairan_coolant' => 'Coolant / Air Radiator',
                'cairan_minyak_rem' => 'Minyak Rem',
                'cairan_wiper' => 'Air Wiper',
            ],
            'Roda & Kaki-kaki' => [
                'kaki_tekanan_ban' => 'Tekanan Ban',
                'kaki_keausan_ban' => 'Keausan Ban',
                'kaki_rem' => 'Sistem Pengereman',
            ],
            'Sistem Kelistrikan' => [
                'listrik_lampu_utama' => 'Lampu Utama',
                'listrik_lampu_sein' => 'Lampu Sein',
                'listrik_lampu_rem' => 'Lampu Rem',
                'listrik_klakson' => 'Klakson',
                'listrik_ac' => 'AC / Pendingin Kabin',
            ],
            'Kebersihan & Sanitasi' => [
                'kebersihan_interior' => 'Kebersihan Interior',
                'kebersihan_eksterior' => 'Kebersihan Eksterior',
            ],
        ];

        $opsi = [
            'Baik' => 'success',
            'Perlu Perhatian' => 'warning',
            'Buruk' => 'danger',
        ];
    @endphp

    <div class="card-premium p-4" style="max-width: 820px;">
        <form method="POST" action="{{ route('checklist.store') }}">
            @csrf

            <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">1. INFORMASI OPERASIONAL</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="form-group-premium mb-0">
                        <label class="form-label-premium">Armada Kendaraan</label>
                        <select name="kendaraan_id" class="form-control-premium" required>
                            <option value="">-- Pilih Kendaraan --</option>
                            @foreach ($kendaraans as $k)
                                <option value="{{ $k->id }}" {{ old('kendaraan_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nomor_polisi }} — {{ $k->merek }} {{ $k->tipe }}
                                </option>
                            @endforeach
                        </select>
                        @error('kendaraan_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group-premium mb-0">
                        <label class="form-label-premium">Tanggal Inspection</label>
                        <input type="date" name="tanggal_cek" class="form-control-premium" value="{{ old('tanggal_cek', now()->format('Y-m-d')) }}" required>
                        @error('tanggal_cek') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            @foreach ($sections as $judulSeksi => $items)
                <h6 class="fw-bold text-primary mb-3 border-bottom pb-2 mt-4">{{ strtoupper($judulSeksi) }}</h6>
                <div class="mb-3">
                    @foreach ($items as $fieldName => $label)
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center py-2.5 px-3 mb-2 rounded-3 border" style="background-color: var(--bg-primary);">
                            <div class="fw-semibold small text-primary mb-2 mb-sm-0">{{ $label }}</div>
                            <div class="d-flex gap-1">
                                @foreach ($opsi as $value => $warna)
                                    <input type="radio" class="btn-check" name="{{ $fieldName }}" id="{{ $fieldName }}_{{ Str::slug($value) }}" value="{{ $value }}" autocomplete="off" {{ old($fieldName) == $value ? 'checked' : '' }} required>
                                    <label class="btn btn-sm btn-outline-{{ $warna }} rounded-pill px-3 py-1 fw-bold" style="font-size: 11px;" for="{{ $fieldName }}_{{ Str::slug($value) }}">{{ $value }}</label>
                                @endforeach
                            </div>
                        </div>
                        @error($fieldName) <div class="text-danger small mb-2">{{ $message }}</div> @enderror
                    @endforeach
                </div>
            @endforeach

            <h6 class="fw-bold text-primary mb-3 border-bottom pb-2 mt-4">CATATAN TAMBAHAN</h6>
            <div class="form-group-premium mb-4">
                <label class="form-label-premium">Catatan Tambahan (Opsional)</label>
                <textarea name="catatan" class="form-control-premium" rows="3" placeholder="Tuliskan catatan kondisi khusus jika ada komponen yang perlu perhatian...">{{ old('catatan') }}</textarea>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button type="submit" class="btn-premium primary btn-lg">
                    <i class="bi bi-check-circle-fill"></i> Simpan Inspection Checklist
                </button>
                <a href="{{ route('dashboard') }}" class="btn-premium secondary btn-lg">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection