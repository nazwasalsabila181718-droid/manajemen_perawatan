# DOKUMENTASI ARSITEKTUR & MODULE SISTEM
## Sistem Manajemen Perawatan Armada (Fleet Maintenance System)

Dokumen ini berisi rincian teknis untuk 12 module utama yang membangun **Sistem Manajemen Perawatan Armada**. Setiap module dijelaskan berdasarkan 10 kriteria arsitektur perangkat lunak.

---

## DAFTAR MODULE

1. [Module 1: Autentikasi & Profil (Auth & Profile)](#1-module-autentikasi--profil-auth--profile)
2. [Module 2: Dashboard Utama (Unified Role-Based Dashboard)](#2-module-dashboard-utama-unified-role-based-dashboard)
3. [Module 3: Manajemen Armada (Kendaraan / Fleet Management)](#3-module-manajemen-armada-kendaraan--fleet-management)
4. [Module 4: Pre-Trip Inspection (Checklist Harian Kendaraan)](#4-module-pre-trip-inspection-checklist-harian-kendaraan)
5. [Module 5: Status Armada (Monitoring Status Operasional)](#5-module-status-armada-monitoring-status-operasional)
6. [Module 6: Jadwal & Riwayat Perawatan (Maintenance Schedule & Log)](#6-module-jadwal--riwayat-perawatan-maintenance-schedule--log)
7. [Module 7: Keluhan Kendaraan (Issue / Complaint Reporting)](#7-module-keluhan-kendaraan-issue--complaint-reporting)
8. [Module 8: Klaim Biaya Operasional / Pembayaran (Expense Claim & Approval)](#8-module-klaim-biaya-operasional--pembayaran-expense-claim--approval)
9. [Module 9: Manajemen Aset & Inventory (Barang / Spare Parts)](#9-module-manajemen-aset--inventory-barang--spare-parts)
10. [Module 10: Laporan & Analitik (Analytical Reports)](#10-module-laporan--analitik-analytical-reports)
11. [Module 11: Manajemen Pengguna (RBAC User Management)](#11-module-manajemen-pengguna-rbac-user-management)
12. [Module 12: Notifikasi Peringatan System (Notification Center)](#12-module-notifikasi-peringatan-system-notification-center)

---

### 1. Module Autentikasi & Profil (Auth & Profile)

1. **Tujuan Module**: Mengelola registrasi pengguna baru, otentikasi login/logout pengakses sistem, dan pembaruan profil pengguna beserta foto profil.
2. **Alur Bisnis**:
   - Pengguna mengisi form registrasi / login -> Sistem me-validate kredensial -> Session dibentuk berdasarkan `role` (`administrator`, `teknisi`, `driver/user`).
   - Pengguna dapat memperbarui data profil (nama, email, password) & mengunggah foto profil yang disimpan ke sistem penyimpanan (*storage*).
3. **Route yang Digunakan**:
   - `GET /login`, `POST /login` (`login`, `login.post`)
   - `GET /register`, `POST /register` (`register`, `register.post`)
   - `POST /logout` (`logout`)
   - `GET /profile`, `POST /profile` (`profile.show`, `profile.update`)
4. **Controller yang Menangani**: `App\Http\Controllers\AuthController`, `App\Http\Controllers\ProfileController`
5. **Request Validation**:
   - `email` (required, email, unique:users), `password` (required, min:8, confirmed), `name` (required), `profile_photo` (nullable, image, mimes:jpg,jpeg,png, max:2048).
6. **Service yang Dipanggil**: `Auth` Facade (Laravel Standard Authentication Services), `Storage` Facade (untuk upload/hapus foto profil).
7. **Repository atau Model yang Digunakan**: `App\Models\User`
8. **Tabel Database yang Diakses**: `users`
9. **Relasi Antar Tabel**:
   - `users.id` 1-to-many ke `checklists.user_id`
   - `users.id` 1-to-many ke `keluhan_kendaraans.user_id`
   - `users.id` 1-to-many ke `pembayarans.user_id`
10. **Response yang Dikembalikan**: `RedirectResponse` (ke `/` dashboard / `/login`) dan `ViewResponse` (`auth.login`, `auth.register`, `auth.profile`).

---

### 2. Module Dashboard Utama (Unified Role-Based Dashboard)

1. **Tujuan Module**: Menyajikan ringkasan statistik (KPI), grafik indikator kondisi armada, peringatan perawatan terdekat, dan shortcut aktivitas sesuai peran user (Admin, Teknisi, Driver).
2. **Alur Bisnis**:
   - User mengakses halaman awal `/` -> Controller mendeteksi `auth()->user()->role` -> Menghitung KPI aktif (jumlah armada, status servis, keluhan pending, biaya bulan berjalan) -> Menampilkan view dashboard sesuai role.
3. **Route yang Digunakan**: `GET /` (`dashboard`)
4. **Controller yang Menangani**: `App\Http\Controllers\DashboardController`
5. **Request Validation**: Tidak ada (Hanya otorisasi middleware `auth`).
6. **Service yang Dipanggil**: Aggregation Queries, Carbon Service (untuk perhitungan sisa hari & tenggat tanggal).
7. **Repository atau Model yang Digunakan**: `App\Models\Kendaraan`, `App\Models\MaintenanceSchedule`, `App\Models\KeluhanKendaraan`, `App\Models\Pembayaran`, `App\Models\ChecklistKendaraan`.
8. **Tabel Database yang Diakses**: `kendaraans`, `maintenance_schedules`, `keluhan_kendaraans`, `pembayarans`, `checklists`.
9. **Relasi Antar Tabel**:
   - Aggregation join antara `kendaraans` dengan `maintenance_schedules` dan `keluhan_kendaraans`.
10. **Response yang Dikembalikan**: `ViewResponse` (`dashboard` / `dashboard-teknisi` / `dashboard-driver`).

---

### 3. Module Manajemen Armada (Kendaraan / Fleet Management)

1. **Tujuan Module**: Mengelola data fisik armada (Mobil & Motor), lokasi pool, penanggung jawab driver, angka odometer (KM), foto kendaraan, serta dokumen legalitas (STNK, Pajak Tahunan, Pajak 5 Tahunan, KIR).
2. **Alur Bisnis**:
   - Admin menambahkan/mengedit data armada & upload foto -> Teknisi/Admin dapat meng-update odometer berkala -> Sistem mengevaluasi warna indikator kelayakan (🟢 Safe, 🟡 Warning, 🔴 Overdue).
3. **Route yang Digunakan**:
   - `GET /kendaraan` (`kendaraan.index`)
   - `POST /kendaraan` (`kendaraan.store`) [Role: Admin]
   - `PUT /kendaraan/{id}` (`kendaraan.update`) [Role: Admin, Teknisi]
   - `PATCH /kendaraan/{id}/odometer` (`kendaraan.update-odometer`) [Role: Admin, Teknisi]
   - `DELETE /kendaraan/{id}` (`kendaraan.destroy`) [Role: Admin]
4. **Controller yang Menangani**: `App\Http\Controllers\KendaraanController`
5. **Request Validation**:
   - `nomor_polisi` (required, unique:kendaraans), `jenis_kendaraan` (required: Mobil/Motor), `merek`, `tipe`, `odometer_terakhir` (numeric, min:0), `foto_kendaraan` (image, max:3072).
6. **Service yang Dipanggil**: `Storage` Facade (manajemen foto armada), Notification Generator Service.
7. **Repository atau Model yang Digunakan**: `App\Models\Kendaraan`
8. **Tabel Database yang Diakses**: `kendaraans`
9. **Relasi Antar Tabel**:
   - `kendaraans.id` 1-to-many ke `maintenance_schedules.kendaraan_id`
   - `kendaraans.id` 1-to-many ke `maintenance_logs.kendaraan_id`
   - `kendaraans.id` 1-to-many ke `checklists.kendaraan_id`
   - `kendaraans.id` 1-to-many ke `keluhan_kendaraans.kendaraan_id`
   - `kendaraans.id` 1-to-many ke `pembayarans.kendaraan_id`
10. **Response yang Dikembalikan**: `ViewResponse` (`kendaraan.index`), `RedirectResponse` dengan pesan flash notification.

---

### 4. Module Pre-Trip Inspection (Checklist Harian Kendaraan)

1. **Tujuan Module**: Memastikan kelayakan armada sebelum dioperasikan melalui pengecekan harian 4 parameter wajib (Cairan, Kaki-kaki, Kelistrikan, Kebersihan).
2. **Alur Bisnis**:
   - Driver/Teknisi memilih armada & menginput angka KM -> Memilih status OK/Bermasalah pada item checklist -> Menginput catatan fisik -> Sistem menyimpan log checklist & otomatis memicu keluhan jika ada parameter bermasalah.
3. **Route yang Digunakan**:
   - `GET /checklist/create` (`checklist.create`)
   - `POST /checklist` (`checklist.store`)
4. **Controller yang Menangani**: `App\Http\Controllers\ChecklistKendaraanController`
5. **Request Validation**:
   - `kendaraan_id` (required, exists:kendaraans,id), `odometer` (required, numeric), `oli_mesin`, `air_radiator`, `rem`, `ban`, `lampu`, `klakson`, `interior`, `eksterior` (required: ok/ada_masalah).
6. **Service yang Dipanggil**: Automatic Issue Dispatcher (membuat entri `KeluhanKendaraan` otomatis jika ditemui item `ada_masalah`).
7. **Repository atau Model yang Digunakan**: `App\Models\ChecklistKendaraan`, `App\Models\Kendaraan`, `App\Models\KeluhanKendaraan`.
8. **Tabel Database yang Diakses**: `checklists`, `kendaraans`, `keluhan_kendaraans`.
9. **Relasi Antar Tabel**:
   - `checklists` belongsTo `kendaraans` & `users`.
10. **Response yang Dikembalikan**: `ViewResponse` (`checklist.create`), `RedirectResponse` ke dashboard/status armada.

---

### 5. Module Status Armada (Monitoring Status Operasional)

1. **Tujuan Module**: Menampilkan peta status real-time kesiapan operasional armada (Ready, In Use, Under Maintenance, Damaged) beserta posisi driver & odometer.
2. **Alur Bisnis**:
   - Sistem melakukan rekapitulasi kondisi armada berdasarkan data checklist harian terbaru, tiket keluhan pending, dan jadwal maintenance aktif -> Menampilkan ringkasan visual status armada.
3. **Route yang Digunakan**: `GET /status-armada` (`status-armada.index`)
4. **Controller yang Menangani**: `App\Http\Controllers\StatusArmadaController`
5. **Request Validation**: Tidak ada.
6. **Service yang Dipanggil**: Fleet Status Evaluator.
7. **Repository atau Model yang Digunakan**: `App\Models\Kendaraan`, `App\Models\ChecklistKendaraan`, `App\Models\KeluhanKendaraan`.
8. **Tabel Database yang Diakses**: `kendaraans`, `checklists`, `keluhan_kendaraans`.
9. **Relasi Antar Tabel**: Join query antara `kendaraans` dan status checklist/keluhan terbaru.
10. **Response yang Dikembalikan**: `ViewResponse` (`status-armada.index`).

---

### 6. Module Jadwal & Riwayat Perawatan (Maintenance Schedule & Log)

1. **Tujuan Module**: Mengatur interval penggantian rutin komponen armada (Oli Mesin, Oli Gardan, Aki, Ban, Rem, Busi, Coolant) berdasarkan batasan Kilometer dan/atau Bulan, serta menyajikan Kalender Pemeliharaan interaktif dan Log Riwayat Servis.
2. **Alur Bisnis**:
   - Admin/Teknisi mendaftarkan jadwal komponen & interval -> Kalender Interaktif memetakan proyeksi penggantian -> Teknisi melakukan penggantian & menekan "Catat Ganti" -> Sistem meng-update odometer & tanggal terakhir, lalu mencatat log permanen ke `maintenance_logs`.
3. **Route yang Digunakan**:
   - `GET /jadwal-perawatan` (`jadwal-perawatan.index`)
   - `GET /jadwal-perawatan/tambah` (`jadwal-perawatan.create`)
   - `POST /jadwal-perawatan` (`jadwal-perawatan.store`)
   - `PATCH /jadwal-perawatan/{id}` (`jadwal-perawatan.update`)
   - `GET /jadwal-perawatan/riwayat` (`jadwal-perawatan.riwayat`)
4. **Controller yang Menangani**: `App\Http\Controllers\JadwalPerawatanController`
5. **Request Validation**:
   - `kendaraan_id` (required), `jenis_perawatan` (required), `interval_km` (nullable, numeric), `interval_bulan` (nullable, numeric), `km_terakhir` (required, numeric), `tanggal_terakhir` (required, date).
6. **Service yang Dipanggil**: Carbon Date Addons, Odometer Calculation Service, Maintenance Logger Service.
7. **Repository atau Model yang Digunakan**: `App\Models\MaintenanceSchedule`, `App\Models\MaintenanceLog`, `App\Models\Kendaraan`.
8. **Tabel Database yang Diakses**: `maintenance_schedules`, `maintenance_logs`, `kendaraans`.
9. **Relasi Antar Tabel**:
   - `maintenance_schedules` belongsTo `kendaraans`
   - `maintenance_logs` belongsTo `kendaraans` & `maintenance_schedules`
10. **Response yang Dikembalikan**: `ViewResponse` (`jadwal-perawatan.index`, `jadwal-perawatan.create`, `jadwal-perawatan.riwayat`), `RedirectResponse`.

---

### 7. Module Keluhan Kendaraan (Issue / Complaint Reporting)

1. **Tujuan Module**: Tempat pelaporan kerusakan atau kendala fisik armada oleh Driver/User dan pelacakan status penanganannya oleh Teknisi/Admin.
2. **Alur Bisnis**:
   - Driver membuat laporan keluhan & upload foto kendala -> Status awal `Pending` -> Teknisi menerima & mengubah status menjadi `Diproses` -> Setelah perbaikan selesai, status diubah menjadi `Selesai` disertai catatan perbaikan & estimasi biaya.
3. **Route yang Digunakan**:
   - `GET /keluhan-kendaraan` (`keluhan-kendaraan.index`)
   - `GET /keluhan-kendaraan/tambah` (`keluhan-kendaraan.create`)
   - `POST /keluhan-kendaraan` (`keluhan-kendaraan.store`)
   - `PATCH /keluhan-kendaraan/{id}` (`keluhan-kendaraan.update`) [Role: Admin, Teknisi]
4. **Controller yang Menangani**: `App\Http\Controllers\KeluhanKendaraanController`
5. **Request Validation**:
   - `kendaraan_id` (required), `judul_keluhan` (required, max:255), `deskripsi` (required), `foto_keluhan` (nullable, image, max:3072), `status` (in:pending,diproses,selesai,ditolak).
6. **Service yang Dipanggil**: `Storage` Facade (unggah foto bukti kerusakan), Push Notification Service.
7. **Repository atau Model yang Digunakan**: `App\Models\KeluhanKendaraan`, `App\Models\Kendaraan`, `App\Models\User`.
8. **Tabel Database yang Diakses**: `keluhan_kendaraans`, `kendaraans`, `users`.
9. **Relasi Antar Tabel**:
   - `keluhan_kendaraans` belongsTo `kendaraans` & `users`.
10. **Response yang Dikembalikan**: `ViewResponse` (`keluhan-kendaraan.index`, `keluhan-kendaraan.create`), `RedirectResponse`.

---

### 8. Module Klaim Biaya Operasional / Pembayaran (Expense Claim & Approval)

1. **Tujuan Module**: Pencatatan transaksi pengeluaran operasional armada (BBM, Tol, Parkir, Servis Bengkel, Spare Part) dan alur persetujuan (*approval*) anggaran oleh Admin.
2. **Alur Bisnis**:
   - User/Teknisi mengajukan biaya operasional & melampirkan foto nota/struk -> Status pengajuan `Pending` -> Admin meninjau detail klaim -> Admin menekan tombol `Approve` atau `Reject` -> Total biaya yang disetujui terakumulasi pada laporan keuangan bulanan.
3. **Route yang Digunakan**:
   - `GET /pembayaran` (`pembayaran.index`)
   - `POST /pembayaran` (`pembayaran.store`)
   - `POST /pembayaran/{id}/approve` (`pembayaran.approve`) [Role: Admin]
   - `POST /pembayaran/{id}/reject` (`pembayaran.reject`) [Role: Admin]
   - `GET /pembayaran/{id}/edit`, `PUT /pembayaran/{id}`, `DELETE /pembayaran/{id}`
4. **Controller yang Menangani**: `App\Http\Controllers\PembayaranController`
5. **Request Validation**:
   - `kendaraan_id` (required), `kategori` (required: bbm, servis, sparepart, tol_parkir, dll), `jumlah_biaya` (required, numeric, min:1), `bukti_pembayaran` (nullable, image, max:3072).
6. **Service yang Dipanggil**: `Storage` Facade (foto nota), Financial Audit Log Service.
7. **Repository atau Model yang Digunakan**: `App\Models\Pembayaran`, `App\Models\Kendaraan`, `App\Models\User`.
8. **Tabel Database yang Diakses**: `pembayarans`, `kendaraans`, `users`.
9. **Relasi Antar Tabel**:
   - `pembayarans` belongsTo `kendaraans` & `users`.
10. **Response yang Dikembalikan**: `ViewResponse` (`pembayaran.index`, `pembayaran.edit`), `RedirectResponse`.

---

### 9. Module Manajemen Aset & Inventory (Barang / Spare Parts)

1. **Tujuan Module**: Mengelola stok barang inventaris, peralatan bengkel, dan suku cadang armada beserta status kondisi barang.
2. **Alur Bisnis**:
   - Admin/Teknisi mencatat barang masuk -> Memperbarui jumlah stok dan status kondisi (Baik / Rusak / Perlu Diganti) -> Menghapus atau mengarsipkan barang.
3. **Route yang Digunakan**:
   - `GET /barang` (`barang.index`)
   - `POST /barang` (`barang.store`)
   - `PATCH /barang/{id}/status` (`barang.update-status`)
   - `DELETE /barang/{id}` (`barang.destroy`)
4. **Controller yang Menangani**: `App\Http\Controllers\BarangController`
5. **Request Validation**:
   - `nama_barang` (required), `kode_barang` (required, unique:barangs), `jumlah` (required, numeric), `kondisi` (required).
6. **Service yang Dipanggil**: Stock Inventory Tracker Service.
7. **Repository atau Model yang Digunakan**: `App\Models\Barang`
8. **Tabel Database yang Diakses**: `barangs`
9. **Relasi Antar Tabel**: Tabel independen master data inventaris.
10. **Response yang Dikembalikan**: `ViewResponse` (`barang.index`), `RedirectResponse`.

---

### 10. Module Laporan & Analitik (Analytical Reports)

1. **Tujuan Module**: Menyajikan laporan analitik komprehensif bagi Administrator mengenai total pengeluaran biaya per armada, efisiensi perawatan, rekapitulasi keluhan, dan performa kelayakan kendaraan.
2. **Alur Bisnis**:
   - Admin memilih periode bulan/tahun atau filter armada -> Controller melakukan agregasi kalkulasi biaya & statistik perawatan -> Menampilkan visualisasi tabel dan grafik analitik yang siap dicetak/dianalisis.
3. **Route yang Digunakan**: `GET /laporan-analitik` (`laporan.index`) [Role: Admin]
4. **Controller yang Menangani**: `App\Http\Controllers\LaporanController`
5. **Request Validation**: `bulan` (nullable, numeric), `tahun` (nullable, numeric), `kendaraan_id` (nullable, exists:kendaraans,id).
6. **Service yang Dipanggil**: Financial Reporting Aggregator, Maintenance Performance Analytics Engine.
7. **Repository atau Model yang Digunakan**: `App\Models\Pembayaran`, `App\Models\MaintenanceLog`, `App\Models\Kendaraan`, `App\Models\KeluhanKendaraan`.
8. **Tabel Database yang Diakses**: `pembayarans`, `maintenance_logs`, `kendaraans`, `keluhan_kendaraans`.
9. **Relasi Antar Tabel**: Cross-table query join antara `kendaraans`, `pembayarans`, `maintenance_logs`.
10. **Response yang Dikembalikan**: `ViewResponse` (`laporan.index`).

---

### 11. Module Manajemen Pengguna (RBAC User Management)

1. **Tujuan Module**: Mengelola akun pengguna sistem, pembagian hak akses (*Role-Based Access Control*), dan status keaktifan user (`administrator`, `teknisi`, `user/driver`).
2. **Alur Bisnis**:
   - Admin membuka daftar pengguna -> Tambah akun user baru dengan pilihan role -> Mengedit data/role user -> Menghapus/nonaktifkan akun.
3. **Route yang Digunakan**:
   - `GET /admin/users` (`admin.users.index`) [Role: Admin]
   - `POST /admin/users` (`admin.users.store`) [Role: Admin]
   - `PATCH /admin/users/{id}` (`admin.users.update`) [Role: Admin]
   - `DELETE /admin/users/{id}` (`admin.users.destroy`) [Role: Admin]
4. **Controller yang Menangani**: `App\Http\Controllers\Admin\UserController`
5. **Request Validation**:
   - `name` (required), `email` (required, email, unique:users), `role` (required, in:administrator,teknisi,user,driver), `password` (required saat create, min:8).
6. **Service yang Dipanggil**: Hash Service (enkripsi password), RBAC Guard.
7. **Repository atau Model yang Digunakan**: `App\Models\User`
8. **Tabel Database yang Diakses**: `users`
9. **Relasi Antar Tabel**: `users.id` berhubungan ke `checklists`, `keluhan_kendaraans`, `pembayarans`.
10. **Response yang Dikembalikan**: `ViewResponse` (`admin.users.index`), `RedirectResponse`.

---

### 12. Module Notifikasi Peringatan System (Notification Center)

1. **Tujuan Module**: Memberikan peringatan (*alert notification*) otomatis kepada pengguna mengenai dokumen kendaraan yang mendekati tenggat (STNK/Pajak/KIR) atau jadwal pemeliharaan armada yang jatuh tempo.
2. **Alur Bisnis**:
   - System Engine memindai tabel `kendaraans` dan `maintenance_schedules` -> Mendeteksi H-30/H-7 tgl jatuh tempo atau KM berlebih -> Menampilkan badge jumlah notifikasi di navbar -> User membuka dropdown/halaman notifikasi & menandai sudah dibaca.
3. **Route yang Digunakan**:
   - `GET /notifikasi` (`notifikasi.index`)
   - `GET /notifikasi/count` (`notifikasi.count`) [AJAX Endpoint]
   - `GET /notifikasi/list` (`notifikasi.list`) [AJAX Endpoint]
   - `POST /notifikasi/{id}/read` (`notifikasi.read`)
   - `POST /notifikasi/baca-semua` (`notifikasi.read-all`)
4. **Controller yang Menangani**: `App\Http\Controllers\NotificationController`
5. **Request Validation**: Validation ID notification (exists:notifications,id).
6. **Service yang Dipanggil**: Fleet Alert System Service, Dynamic Date Calculator.
7. **Repository atau Model yang Digunakan**: `Notification` Model / Laravel Database Notifications, `App\Models\Kendaraan`, `App\Models\MaintenanceSchedule`.
8. **Tabel Database yang Diakses**: `notifications` (atau dynamic query `kendaraans` & `maintenance_schedules`).
9. **Relasi Antar Tabel**: `notifications` belongsTo `users` (opsional jika menyimpan log per user).
10. **Response yang Dikembalikan**: `JsonResponse` (untuk badge & AJAX list) dan `ViewResponse` (`notifikasi.index`).
