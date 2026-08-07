@extends('layouts.app')

@section('title', 'Tambah Jadwal Perawatan')
@section('page_title', 'Tambah Jadwal Perawatan')
@section('page_subtitle', 'Daftarkan item perawatan rutin baru untuk kendaraan.')

@section('content')
<div class="container-fluid p-0">
    <div class="card-premium p-4" style="max-width: 640px;">
        <form method="POST" action="{{ route('jadwal-perawatan.store') }}">
            @csrf

            <div class="form-group-premium">
                <label class="form-label-premium">Armada Kendaraan</label>
                <select name="kendaraan_id" class="form-control-premium" required>
                    <option value="">-- Pilih Kendaraan --</option>
                    @foreach ($kendaraans as $k)
                        <option value="{{ $k->id }}">{{ $k->nomor_polisi }} ({{ $k->merek }} {{ $k->tipe }})</option>
                    @endforeach
                </select>
                @error('kendaraan_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="form-group-premium">
                <label class="form-label-premium">Jenis Item / Komponen Perawatan</label>
                <select name="jenis_perawatan" class="form-control-premium" required>
                    <option value="">-- Pilih Jenis Perawatan --</option>
                    @foreach ($jenisPerawatan as $jenis)
                        <option value="{{ $jenis }}">{{ $jenis }}</option>
                    @endforeach
                </select>
                @error('jenis_perawatan') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="row g-3">
                <div class="col-6">
                    <div class="form-group-premium">
                        <label class="form-label-premium">Interval KM (Opsional)</label>
                        <input type="number" name="interval_km" class="form-control-premium" placeholder="Contoh: 5000">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group-premium">
                        <label class="form-label-premium">Interval Bulan (Opsional)</label>
                        <input type="number" name="interval_bulan" class="form-control-premium" placeholder="Contoh: 6">
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-6">
                    <div class="form-group-premium">
                        <label class="form-label-premium">KM Terakhir Ganti</label>
                        <input type="number" name="km_terakhir" class="form-control-premium" placeholder="Contoh: 45000">
                        <small class="text-muted">Masukkan angka tanpa titik/koma</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group-premium">
                        <label class="form-label-premium">Tanggal Terakhir Ganti</label>
                        <input type="date" name="tanggal_terakhir" class="form-control-premium">
                    </div>
                </div>
            </div>

            <div class="form-group-premium">
                <label class="form-label-premium">Catatan Tambahan (Opsional)</label>
                <textarea name="catatan" class="form-control-premium" rows="3" placeholder="Informasi spesifikasi suku cadang atau garansi"></textarea>
            </div>

            <div class="d-flex align-items-center gap-2 mt-4">
                <button type="submit" class="btn-premium primary">Simpan Jadwal</button>
                <a href="{{ route('jadwal-perawatan.index') }}" class="btn-premium secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection