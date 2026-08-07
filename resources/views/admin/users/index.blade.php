@extends('layouts.app')

@section('title', 'Kelola Pengguna')
@section('page_title', 'Manajemen Pengguna Aplikasi')
@section('page_subtitle', 'Kelola hak akses, peran (role), dan akun pengguna sistem MaintAsset.')

@section('content')
<div class="container-fluid p-0">

    {{-- Notifikasi sukses / error --}}
    @if(session('success'))
        <div class="alert-premium success mb-4" role="alert">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="alert-premium danger mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <div class="row g-4">
        <!-- Left Column: User Table List -->
        <div class="col-lg-8">
            <div class="card-premium p-0 overflow-hidden">
                <div class="p-4 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0">Daftar Pengguna Aplikasi</h5>
                    <button type="button" class="btn-premium primary btn-sm px-3 py-2"
                            data-bs-toggle="modal" data-bs-target="#tambahPenggunaModal"
                            style="font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="bi bi-person-plus-fill"></i> Tambah Pengguna
                    </button>
                </div>
                
                <div class="table-responsive-premium border-0">
                    <table class="table-premium">
                        <thead>
                            <tr>
                                <th style="width: 60px;">No</th>
                                <th>Nama Pengguna</th>
                                <th>Alamat Email</th>
                                <th>Role</th>
                                <th class="text-end" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $index => $user)
                            @php
                                $isAdminRow = $user->role === 'administrator';
                                $avatarSize = $isAdminRow ? '54px' : '38px';
                                $avatarFontSize = $isAdminRow ? '17px' : '13px';
                            @endphp
                            <tr @if($isAdminRow) style="background: rgba(46, 111, 64, 0.015);" @endif>
                                <td class="text-muted small fw-semibold">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm overflow-hidden" style="width: {{ $avatarSize }}; height: {{ $avatarSize }}; font-size: {{ $avatarFontSize }}; flex-shrink: 0; background: var(--accent-gradient) !important; @if($isAdminRow) border: 3px solid var(--accent); @endif">
                                            @if($user->profile_photo)
                                                <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Avatar" style="width:100%; height:100%; object-fit:cover;">
                                            @else
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            @endif
                                        </div>
                                        <div>
                                            <div class="fw-bold text-primary d-flex align-items-center gap-2">
                                                {{ $user->name }}
                                                @if($isAdminRow)
                                                    <span class="badge bg-primary text-white" style="font-size: 9px; padding: 2.5px 6px; background-color: var(--accent) !important; border-radius: 4px;">UTAMA</span>
                                                @endif
                                            </div>
                                            <div class="text-muted small">{{ $user->created_at->format('d M Y') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="small text-secondary">{{ $user->email }}</td>
                                <td>
                                    @php
                                        $badgeStyle = match($user->role) {
                                            'administrator' => 'danger',
                                            'manager' => 'indigo',
                                            'teknisi' => 'warning',
                                            'driver' => 'success',
                                            default => 'info'
                                        };
                                    @endphp
                                    <span class="badge-premium {{ $badgeStyle }} text-capitalize">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <!-- Edit Role Button -->
                                        <button type="button" class="btn-premium secondary btn-sm p-2" data-bs-toggle="modal" title="Edit Role" data-bs-target="#editModal{{ $user->id }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        
                                        <!-- Delete Button -->
                                        @if(auth()->id() != $user->id)
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-premium danger btn-sm p-2" title="Hapus Pengguna">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="bi bi-people-fill fs-2 d-block mb-2 opacity-50"></i>
                                    Belum ada data pengguna.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Role Duties/Guide -->
        <div class="col-lg-4">
            <div class="card-premium p-4">
                <h5 class="fw-bold mb-1" style="font-size: 14px;"><i class="bi bi-card-checklist text-primary me-2"></i>Panduan Peran & Tugas</h5>
                <p class="text-secondary small mb-3" style="font-size: 11px;">Tanggung jawab dan batas operasional masing-masing hak akses.</p>

                <div class="d-flex flex-column gap-2">
                    <!-- Administrator -->
                    <div class="p-2 rounded-3 border" style="background: rgba(239, 68, 68, 0.02); border-color: rgba(239, 68, 68, 0.15) !important;">
                        <h6 class="fw-bold text-danger mb-1 d-flex align-items-center gap-1" style="font-size: 12px;">
                            <i class="bi bi-shield-lock-fill"></i> Administrator
                        </h6>
                        <ul class="text-secondary mb-0 ps-3" style="line-height: 1.4; font-size: 11px;">
                            <li>Kelola penuh pendaftaran armada & data akun pengguna</li>
                            <li>Tinjau dan audit laporan analitik biaya & kepatuhan KIR</li>
                            <li>Otorisasi persetujuan klaim biaya perbaikan besar (&ge; 1 jt)</li>
                        </ul>
                    </div>


                    <!-- Teknisi -->
                    <div class="p-2 rounded-3 border" style="background: rgba(245, 158, 11, 0.02); border-color: rgba(245, 158, 11, 0.15) !important;">
                        <h6 class="fw-bold text-warning mb-1 d-flex align-items-center gap-1" style="font-size: 12px;">
                            <i class="bi bi-tools"></i> Teknisi (Technician)
                        </h6>
                        <ul class="text-secondary mb-0 ps-3" style="line-height: 1.4; font-size: 11px;">
                            <li>Tinjau laporan inspeksi checklist harian dari driver</li>
                            <li>Perbarui angka odometer & status kelayakan servis armada</li>
                            <li>Input pengeluaran/rincian nota biaya perbaikan</li>
                        </ul>
                    </div>

                    <!-- Driver -->
                    <div class="p-2 rounded-3 border" style="background: rgba(16, 185, 129, 0.02); border-color: rgba(16, 185, 129, 0.15) !important;">
                        <h6 class="fw-bold text-success mb-1 d-flex align-items-center gap-1" style="font-size: 12px;">
                            <i class="bi bi-person-badge-fill"></i> Driver / Staf Lapangan
                            <span class="ms-1 text-muted fw-normal" style="font-size: 10px;">(contoh: Driver Armada)</span>
                        </h6>
                        <ul class="text-secondary mb-0 ps-3" style="line-height: 1.4; font-size: 11px;">
                            <li>Isi checklist pre-trip harian (cairan, ban, kelistrikan, AC)</li>
                            <li>Laporkan kerusakan fisik atau kendala kendaraan secara instan</li>
                            <li>Pantau masa aktif dokumen STNK & KIR kendaraan yang digunakan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Role (Outside Table Grid) -->
@foreach($users as $user)
<div class="modal fade" id="editModal{{ $user->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="editModalLabel{{ $user->id }}"><i class="bi bi-person-gear text-primary me-2"></i> Edit Role Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body p-4">
                    <div class="form-group-premium">
                        <label for="name{{ $user->id }}" class="form-label-premium">Nama Pengguna</label>
                        <input type="text" name="name" id="name{{ $user->id }}" class="form-control-premium" value="{{ $user->name }}" required>
                    </div>
                    <div class="form-group-premium">
                        <label for="role{{ $user->id }}" class="form-label-premium">Pilih Role / Hak Akses Baru</label>
                        <select name="role" id="role{{ $user->id }}" class="form-control-premium" required>
                            <option value="administrator" {{ $user->role == 'administrator' ? 'selected' : '' }}>Administrator</option>
                            <option value="teknisi" {{ $user->role == 'teknisi' ? 'selected' : '' }}>Teknisi</option>
                            <option value="driver" {{ $user->role == 'driver' ? 'selected' : '' }}>Driver</option>
                        </select>
                    </div>
                    
                    <div class="alert-premium warning mb-0" role="alert">
                        <i class="bi bi-info-circle-fill fs-5"></i>
                        <div class="small">Perubahan hak akses akan langsung aktif pada sesi berikutnya.</div>
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

{{-- ===== MODAL TAMBAH PENGGUNA BARU ===== --}}
<div class="modal fade" id="tambahPenggunaModal" tabindex="-1" aria-labelledby="tambahPenggunaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
        <div class="modal-content border-0 overflow-hidden" style="border-radius: 18px; box-shadow: 0 20px 60px rgba(0,0,0,0.18);">

            {{-- Header --}}
            <div class="modal-header border-0" style="background: var(--accent-gradient); padding: 20px 24px;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:42px;height:42px;border-radius:10px;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;font-size:20px;color:#fff;">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0" id="tambahPenggunaModalLabel">Tambah Pengguna Baru</h5>
                        <p class="text-white mb-0" style="font-size:11px;opacity:.8;">Buat akun pengguna dengan role yang sesuai</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Body --}}
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4" style="background: var(--bg-secondary);">

                    @if($errors->any())
                        <div class="alert-premium danger mb-3" role="alert">
                            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                            <ul class="mb-0 ps-3 small">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Nama --}}
                    <div class="form-group-premium">
                        <label for="new_name" class="form-label-premium">Nama Lengkap</label>
                        <div class="position-relative">
                            <input type="text" name="name" id="new_name" class="form-control-premium"
                                   value="{{ old('name') }}" placeholder="Contoh: Budi Santoso" required
                                   style="padding-left: 40px;">
                            <i class="bi bi-person position-absolute text-muted" style="left:13px;top:50%;transform:translateY(-50%);font-size:15px;"></i>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="form-group-premium">
                        <label for="new_email" class="form-label-premium">Alamat Email</label>
                        <div class="position-relative">
                            <input type="email" name="email" id="new_email" class="form-control-premium"
                                   value="{{ old('email') }}" placeholder="contoh@email.com" required
                                   style="padding-left: 40px;">
                            <i class="bi bi-envelope position-absolute text-muted" style="left:13px;top:50%;transform:translateY(-50%);font-size:15px;"></i>
                        </div>
                    </div>

                    {{-- Role --}}
                    <div class="form-group-premium">
                        <label for="new_role" class="form-label-premium">Peran (Role)</label>
                        <select name="role" id="new_role" class="form-control-premium" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="administrator" {{ old('role') == 'administrator' ? 'selected' : '' }}>
                                🛡️ Administrator
                            </option>
                            <option value="teknisi" {{ old('role') == 'teknisi' ? 'selected' : '' }}>
                                🔧 Teknisi
                            </option>
                            <option value="driver" {{ old('role') == 'driver' ? 'selected' : '' }}>
                                🚗 Driver
                            </option>
                        </select>
                    </div>

                    {{-- Password --}}
                    <div class="form-group-premium">
                        <label for="new_password" class="form-label-premium">Kata Sandi</label>
                        <div class="position-relative">
                            <input type="password" name="password" id="new_password" class="form-control-premium"
                                   placeholder="Minimal 6 karakter" required style="padding-left: 40px;">
                            <i class="bi bi-lock position-absolute text-muted" style="left:13px;top:50%;transform:translateY(-50%);font-size:15px;"></i>
                        </div>
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div class="form-group-premium mb-0">
                        <label for="new_password_confirmation" class="form-label-premium">Konfirmasi Kata Sandi</label>
                        <div class="position-relative">
                            <input type="password" name="password_confirmation" id="new_password_confirmation"
                                   class="form-control-premium" placeholder="Ulangi kata sandi" required
                                   style="padding-left: 40px;">
                            <i class="bi bi-check-circle position-absolute text-muted" style="left:13px;top:50%;transform:translateY(-50%);font-size:15px;"></i>
                        </div>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="modal-footer border-0 px-4 pb-4 pt-2" style="background: var(--bg-secondary);">
                    <button type="button" class="btn-premium secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-premium primary">
                        <i class="bi bi-person-check-fill me-1"></i> Tambah Pengguna
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Jika ada validation error, buka kembali modal tambah pengguna
    @if($errors->any())
    document.addEventListener('DOMContentLoaded', function() {
        const modalEl = document.getElementById('tambahPenggunaModal');
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    });
    @endif
</script>
@endsection
