@extends('layouts.app')

@section('title', 'Inventaris & Spare Part Armada')
@section('page_title', 'Manajemen Spare Part & Inventaris')
@section('page_subtitle', 'Pantau kondisi kelayakan inventaris bengkel dan spare part armada.')

@section('content')
<div class="container-fluid p-0">

    <!-- Top Action Row -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-3 mb-4">
        <!-- Search bar -->
        <div class="search-box-premium">
            <i class="bi bi-search"></i>
            <input type="text" id="asset-search" class="form-control-premium" placeholder="Cari nama aset/barang...">
        </div>
        
        <!-- Add Button -->
        <button class="btn-premium primary" data-bs-toggle="modal" data-bs-target="#addAssetModal">
            <i class="bi bi-plus-lg"></i>
            <span>Tambah Aset Baru</span>
        </button>
    </div>

    <!-- Stats summary row -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card-ultra">
                <div class="stat-icon-wrapper indigo">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="stat-meta">
                    <div class="stat-label-text">Total Jumlah Aset</div>
                    <div class="stat-value-text">{{ $total }} <span class="fs-6 fw-normal text-muted">unit</span></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card-ultra">
                <div class="stat-icon-wrapper amber">
                    <i class="bi bi-tools"></i>
                </div>
                <div class="stat-meta">
                    <div class="stat-label-text">Perlu Perawatan</div>
                    <div class="stat-value-text text-warning">{{ $perlu_rawat }} <span class="fs-6 fw-normal text-muted">unit</span></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card-ultra">
                <div class="stat-icon-wrapper emerald">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div class="stat-meta">
                    <div class="stat-label-text">Kondisi Bagus</div>
                    <div class="stat-value-text text-success">{{ $selesai }} <span class="fs-6 fw-normal text-muted">unit</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Data Table Card -->
    <div class="card-premium p-0 overflow-hidden">
        <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Daftar Inventaris Aset</h5>
            <span class="badge-premium indigo" id="row-count">
                Menampilkan {{ $barangs->count() }} item
            </span>
        </div>
        
        <div class="table-responsive-premium border-0">
            <table class="table-premium" id="assets-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">No</th>
                        <th>Nama Aset / Barang</th>
                        <th>Jumlah Unit</th>
                        <th>Status Perawatan</th>
                        <th class="text-end" style="width: 170px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($barangs as $index => $barang)
                    <tr class="asset-row">
                        <td class="text-muted small fw-semibold">{{ $index + 1 }}</td>
                        <td>
                            <div class="fw-bold text-primary asset-name">{{ $barang->nama_barang }}</div>
                            <div class="text-muted small">Ditambahkan: {{ $barang->created_at->format('d M Y') }}</div>
                        </td>
                        <td><span class="fw-bold fs-6">{{ $barang->jumlah }}</span> unit</td>
                        <td>
                            <span class="badge-premium {{ $barang->status == 'Bagus' ? 'success' : 'warning' }}">
                                <i class="bi {{ $barang->status == 'Bagus' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' }}"></i>
                                {{ $barang->status }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <button class="btn-premium secondary btn-sm edit-status-btn p-2" 
                                        data-id="{{ $barang->id }}" 
                                        data-name="{{ $barang->nama_barang }}" 
                                        data-status="{{ $barang->status }}"
                                        title="Ubah Status Kelayakan">
                                    <i class="bi bi-arrow-left-right"></i>
                                </button>
                                
                                <button class="btn-premium danger btn-sm delete-btn p-2" 
                                        data-id="{{ $barang->id }}" 
                                        data-name="{{ $barang->nama_barang }}"
                                        title="Hapus Aset">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                            Belum ada data barang inventaris. Silakan tambah data baru!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Tambah Aset Baru -->
<div class="modal fade" id="addAssetModal" tabindex="-1" aria-labelledby="addAssetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="addAssetModalLabel"><i class="bi bi-box-seam text-primary me-2"></i> Tambah Aset Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('barang.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="form-group-premium">
                        <label class="form-label-premium" for="nama_barang">Nama Aset / Barang</label>
                        <input type="text" name="nama_barang" id="nama_barang" class="form-control-premium" placeholder="Contoh: AC Aula Utama, Printer L3110" required>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="form-group-premium mb-0">
                                <label class="form-label-premium" for="jumlah">Jumlah Unit</label>
                                <input type="number" name="jumlah" id="jumlah" class="form-control-premium" min="1" placeholder="Jumlah" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group-premium mb-0">
                                <label class="form-label-premium" for="status">Kondisi Awal</label>
                                <select name="status" id="status" class="form-control-premium" required>
                                    <option value="Bagus">Bagus (Siap Pakai)</option>
                                    <option value="Perlu Perawatan">Perlu Perawatan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3">
                    <button type="button" class="btn-premium secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-premium primary">Simpan Aset</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Ubah Status Aset -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="updateStatusModalLabel"><i class="bi bi-arrow-left-right text-primary me-2"></i> Ubah Status Kelayakan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="update-status-form" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <span class="text-secondary small fw-medium">NAMA ASET:</span>
                        <div class="fs-5 fw-bold text-primary mt-1" id="status-barang-name">-</div>
                    </div>
                    
                    <div class="form-group-premium mb-0 mt-3">
                        <label class="form-label-premium" for="new_status">Status Perawatan Terbaru</label>
                        <select name="status" id="new_status" class="form-control-premium" required>
                            <option value="Bagus">Bagus (Siap Pakai)</option>
                            <option value="Perlu Perawatan">Perlu Perawatan</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3">
                    <button type="button" class="btn-premium secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-premium primary">Perbarui Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-danger" id="deleteModalLabel"><i class="bi bi-exclamation-triangle-fill me-2"></i> Hapus Aset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="delete-form" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body p-4">
                    <p class="text-secondary mb-2">Apakah Anda yakin ingin menghapus data aset ini dari sistem?</p>
                    <div class="fs-5 fw-bold text-danger bg-danger-subtle p-3 rounded-3 border border-danger-subtle" id="delete-barang-name">-</div>
                    <p class="text-muted small mt-3 mb-0">Tindakan ini permanen dan data yang dihapus tidak dapat dipulihkan.</p>
                </div>
                <div class="modal-footer px-4 py-3">
                    <button type="button" class="btn-premium secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-premium danger">Ya, Hapus Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('action') === 'add') {
            const addAssetModal = new bootstrap.Modal(document.getElementById('addAssetModal'));
            addAssetModal.show();
        }

        const searchInput = document.getElementById('asset-search');
        const rows = document.querySelectorAll('.asset-row');
        const rowCountBadge = document.getElementById('row-count');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = searchInput.value.toLowerCase().trim();
                let visibleCount = 0;

                rows.forEach(row => {
                    const name = row.querySelector('.asset-name').textContent.toLowerCase();
                    if (name.includes(query)) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                rowCountBadge.textContent = `Menampilkan ${visibleCount} item`;
            });
        }

        const editButtons = document.querySelectorAll('.edit-status-btn');
        const updateStatusModalEl = document.getElementById('updateStatusModal');
        const updateStatusModal = new bootstrap.Modal(updateStatusModalEl);
        const statusForm = document.getElementById('update-status-form');
        const statusBarangName = document.getElementById('status-barang-name');
        const newStatusSelect = document.getElementById('new_status');

        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const status = this.getAttribute('data-status');

                statusBarangName.textContent = name;
                newStatusSelect.value = status;
                statusForm.action = `/barang/${id}/status`;
                
                updateStatusModal.show();
            });
        });

        const deleteButtons = document.querySelectorAll('.delete-btn');
        const deleteModalEl = document.getElementById('deleteModal');
        const deleteModal = new bootstrap.Modal(deleteModalEl);
        const deleteForm = document.getElementById('delete-form');
        const deleteBarangName = document.getElementById('delete-barang-name');

        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');

                deleteBarangName.textContent = name;
                deleteForm.action = `/barang/${id}`;
                
                deleteModal.show();
            });
        });
    });
</script>
@endsection
