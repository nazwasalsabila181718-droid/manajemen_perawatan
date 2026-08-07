@extends('layouts.app')

@section('title', 'Pendaftaran Servis')
@section('page_title', 'Pendaftaran Perawatan & Servis')
@section('page_subtitle', 'Kelola pendaftaran perbaikan aset dan kendaraan operasional.')

@section('content')
<div class="container-fluid p-0">
    <div class="card-premium p-0 overflow-hidden">
        <div class="p-4 border-bottom">
            <h5 class="fw-bold mb-0">Daftar Pendaftaran Servis</h5>
        </div>
        <div class="table-responsive-premium border-0">
            <table class="table-premium">
                <thead>
                    <tr>
                        <th style="width: 100px;">ID</th>
                        <th>Deskripsi / Keterangan</th>
                        <th>Status Pengajuan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendaftarans as $pendaftaran)
                    <tr>
                        <td class="fw-bold text-primary">#{{ $pendaftaran->id }}</td>
                        <td class="fw-semibold">{{ $pendaftaran->keterangan ?? 'Servis Kendaraan' }}</td>
                        <td><span class="badge-premium warning">Pending</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                            Belum ada data pendaftaran servis.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
