@extends('layouts.app')

@section('title', 'Edit Rincian Biaya')
@section('page_title', 'Edit Rincian Biaya')
@section('page_subtitle', 'Ubah rincian klaim atau catatan biaya operasional kendaraan.')

@section('content')
<div class="container-fluid p-0">
    <div class="card-premium p-4" style="max-width: 600px;">
        <form method="POST" action="{{ route('pembayaran.update', $pembayaran->id) }}">
            @csrf
            @method('PUT')

            <div class="form-group-premium">
                <label class="form-label-premium">Armada Kendaraan</label>
                <select name="kendaraan_id" class="form-control-premium" required>
                    @foreach ($kendaraans as $k)
                        <option value="{{ $k->id }}" {{ $pembayaran->kendaraan_id == $k->id ? 'selected' : '' }}>
                            {{ $k->nomor_polisi }} — {{ $k->merek }} {{ $k->tipe }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group-premium">
                <label class="form-label-premium">Jenis Biaya</label>
                <input type="text" name="jenis_biaya" class="form-control-premium" value="{{ $pembayaran->jenis_biaya }}" required>
            </div>

            <div class="form-group-premium">
                <label class="form-label-premium">Jumlah Nominal (Rp)</label>
                <input type="number" name="jumlah" class="form-control-premium" value="{{ $pembayaran->jumlah }}" required>
            </div>

            <div class="form-group-premium">
                <label class="form-label-premium">Tanggal Transaksi</label>
                <input type="date" name="tanggal_pembayaran" class="form-control-premium"
                       value="{{ \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('Y-m-d') }}" required>
            </div>

            <div class="form-group-premium">
                <label class="form-label-premium">Keterangan / Bukti Nota</label>
                <textarea name="keterangan" class="form-control-premium" rows="3">{{ $pembayaran->keterangan }}</textarea>
            </div>

            <div class="d-flex align-items-center gap-2 mt-4">
                <button type="submit" class="btn-premium primary">Simpan Perubahan</button>
                <a href="{{ route('pembayaran.index') }}" class="btn-premium secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection