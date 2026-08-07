@extends('layouts.app')

@section('title', 'Lapor Keluhan Kendaraan')
@section('page_title', 'Lapor Keluhan Kendaraan')
@section('page_subtitle', 'Laporkan kerusakan atau kendala teknis pada kendaraan operasional.')

@section('content')
<div class="container-fluid p-0">
    <div class="card-premium p-4" style="max-width: 640px;">
        <form method="POST" action="{{ route('keluhan-kendaraan.store') }}">
            @csrf

            <div class="form-group-premium">
                <label class="form-label-premium">Armada Kendaraan</label>
                <select name="kendaraan_id" class="form-control-premium" required>
                    <option value="">-- Pilih Kendaraan --</option>
                    @foreach ($kendaraans as $k)
                        <option value="{{ $k->id }}">{{ $k->nomor_polisi }} — {{ $k->merek }} {{ $k->tipe }}</option>
                    @endforeach
                </select>
                @error('kendaraan_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="form-group-premium">
                <label class="form-label-premium">Tingkat Urgensi Kerusakan</label>
                <select name="tingkat_urgensi" class="form-control-premium" required>
                    <option value="ringan">Ringan (Masih bisa beroperasi)</option>
                    <option value="sedang" selected>Sedang (Membutuhkan perhatian segera)</option>
                    <option value="berat">Berat (Kendaraan mogok / Darurat)</option>
                </select>
                @error('tingkat_urgensi') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="form-group-premium">
                <label class="form-label-premium">Deskripsi Keluhan / Gejala Kerusakan</label>
                <textarea name="keluhan" class="form-control-premium" rows="4" placeholder="Jelaskan secara detail gejala kerusakan (contoh: rem berdecit, lampu utama mati, mesin overheat)..." required>{{ old('keluhan') }}</textarea>
                @error('keluhan') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="d-flex align-items-center gap-2 mt-4">
                <button type="submit" class="btn-premium primary">Kirim Laporan Keluhan</button>
                <a href="{{ route('keluhan-kendaraan.index') }}" class="btn-premium secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection