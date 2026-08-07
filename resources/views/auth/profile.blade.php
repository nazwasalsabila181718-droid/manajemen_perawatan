@extends('layouts.app')

@section('title', 'Edit Profil')
@section('page_title', 'Pengaturan Profil Pengguna')
@section('page_subtitle', 'Perbarui nama lengkap dan foto profil Anda agar dikenali dalam sistem.')

@section('content')
<div class="container-fluid p-0">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card-premium p-4">
                <h5 class="fw-bold mb-4"><i class="bi bi-person-gear text-primary me-2"></i> Edit Informasi Profil</h5>
                
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Avatar Upload Area -->
                    <div class="text-center mb-4">
                        <div class="d-inline-block position-relative mb-2">
                            <div class="rounded-circle overflow-hidden shadow border border-3 border-primary" style="width: 120px; height: 120px; background-color: var(--bg-primary);">
                                <div id="avatar-placeholder" class="align-items-center justify-content-center fw-bold text-white bg-primary h-100 fs-1" style="background: var(--accent-gradient) !important; {{ $user->profile_photo ? 'display: none !important;' : 'display: flex;' }}">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <img id="avatar-preview" src="{{ $user->profile_photo ? asset('storage/' . $user->profile_photo) : '' }}" alt="Avatar" style="width:100%; height:100%; object-fit:cover; {{ $user->profile_photo ? 'display: block;' : 'display: none;' }}">
                            </div>
                            
                            <!-- Floating Edit Button -->
                            <button type="button" class="btn btn-primary btn-sm rounded-circle position-absolute bottom-0 end-0 p-2 shadow-lg" onclick="document.getElementById('profile_photo').click()" style="width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; background: var(--accent-gradient); border: none;" title="Ganti Foto">
                                <i class="bi bi-camera-fill text-white"></i>
                            </button>

                            <!-- Floating Delete Button -->
                            <button type="button" id="btn-delete-photo" class="btn btn-danger btn-sm rounded-circle position-absolute bottom-0 start-0 p-2 shadow-lg" onclick="removePhotoPreview()" style="width: 34px; height: 34px; display: {{ $user->profile_photo ? 'flex' : 'none' }}; align-items: center; justify-content: center; border: none;" title="Hapus Foto">
                                <i class="bi bi-trash-fill text-white"></i>
                            </button>
                        </div>
                        <div class="text-muted small mt-2">Format: JPG, PNG, WEBP. Maks: 2MB</div>
                        <input type="file" name="profile_photo" id="profile_photo" accept="image/*" class="d-none" onchange="previewAvatar(this)">
                        <input type="hidden" name="delete_photo" id="delete_photo" value="0">
                    </div>

                    <!-- User Information fields -->
                    <div class="form-group-premium">
                        <label class="form-label-premium" for="email">Alamat Email</label>
                        <input type="email" id="email" class="form-control-premium" value="{{ $user->email }}" readonly disabled style="opacity: 0.7; cursor: not-allowed;">
                        <div class="text-muted small mt-1"><i class="bi bi-info-circle"></i> Email tidak dapat diubah karena merupakan identitas login unik Anda.</div>
                    </div>

                    <div class="form-group-premium">
                        <label class="form-label-premium" for="role">Hak Akses / Peran</label>
                        <input type="text" id="role" class="form-control-premium text-capitalize" value="{{ $user->role }}" readonly disabled style="opacity: 0.7; cursor: not-allowed;">
                    </div>

                    <div class="form-group-premium">
                        <label class="form-label-premium" for="name">Nama Lengkap</label>
                        <input type="text" name="name" id="name" class="form-control-premium" value="{{ $user->name }}" required placeholder="Masukkan nama lengkap Anda">
                    </div>

                    <div class="text-end mt-4">
                        <a href="{{ route('dashboard') }}" class="btn-premium secondary me-2">Batal</a>
                        <button type="submit" class="btn-premium primary">
                            <i class="bi bi-save-fill"></i> Simpan Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function removePhotoPreview() {
        document.getElementById('avatar-preview').style.display = 'none';
        document.getElementById('avatar-preview').src = '';
        document.getElementById('avatar-placeholder').style.setProperty('display', 'flex', 'important');
        document.getElementById('delete_photo').value = '1';
        document.getElementById('profile_photo').value = '';
        document.getElementById('btn-delete-photo').style.display = 'none';
    }

    function previewAvatar(input) {
        const preview = document.getElementById('avatar-preview');
        const placeholder = document.getElementById('avatar-placeholder');
        const deleteBtn = document.getElementById('btn-delete-photo');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                if (placeholder) {
                    placeholder.style.setProperty('display', 'none', 'important');
                }
                document.getElementById('delete_photo').value = '0';
                if (deleteBtn) {
                    deleteBtn.style.display = 'flex';
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
