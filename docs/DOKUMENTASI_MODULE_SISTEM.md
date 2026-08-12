# DOKUMENTASI ARSITEKTUR & MODULE SISTEM
## Sistem Manajemen Perawatan Armada (Fleet Maintenance System)

Dokumen ini berisi rincian teknis untuk **Module Utama** yang membangun **Sistem Manajemen Perawatan Armada**. Dokumen ini dirancang terstruktur dan diurutkan secara konsisten menggunakan penomoran `1, 2, 3, dst.` pada setiap rincian modul.

---

## ⚡ TABEL HAFALAN CEPAT (CHEAT SHEET MODULE)

| No | Nama Module | Fungsi Utama | Role Pengakses Utama | Controller / Middleware |
| :-: | :--- | :--- | :--- | :--- |
| **GROUP 1: OTENTIKASI & UTAMA** |
| 1 | **Auth & Profile** | Login, Logout, Registrasi, Update Profil | Semua User | `AuthController`, `ProfileController` |
| 2 | **Dashboard Utama** | Summary KPI, Grafis Kelayakan, Shortcut | Admin, Teknisi, Driver | `DashboardController` |
| 3 | **Manajemen Pengguna (RBAC)** | Hak Akses Otorisasi Multi-Role (Admin, Teknisi, Driver, Manager) | Administrator | `RoleMiddleware`, `User` |
| **GROUP 2: OPERASIONAL & FLEET** |
| 4 | **Manajemen Armada** | Master Data Kendaraan, KM, Dokumen (STNK/KIR) | Admin & Teknisi | `KendaraanController` |
| 5 | **Pre-Trip Inspection** | Checklist Harian (Cairan, Ban, Kelistrikan, Kebersihan) | Driver & Teknisi | `ChecklistKendaraanController` |
| 6 | **Status Armada** | Real-time Kesiapan Armada (Ready/Servis/Rusak) | Semua User | `StatusArmadaController` |
| **GROUP 3: PEMELIHARAAN & KELUHAN** |
| 7 | **Jadwal & Riwayat Servis** | Servis Berkala (KM/Bulan) & Log Riwayat Servis | Admin & Teknisi | `JadwalPerawatanController` |
| 8 | **Laporan Keluhan** | Pelaporan Kerusakan Fisik oleh Driver & Perbaikan | Driver & Teknisi | `KeluhanKendaraanController` |
| **GROUP 4: KEUANGAN, INVENTARIS, KOMUNIKASI & ANALITIK** |
| 9 | **Klaim Biaya Operasional** | Pengajuan & Approval Biaya (BBM, Tol, Service) | Driver, Teknisi, Admin | `PembayaranController` |
| 10 | **Manajemen Aset/Barang** | Stok Spare Part, Alat Bengkel, Kondisi Barang | Admin & Teknisi | `BarangController` |
| 11 | **Komunikasi Chat** | Pesan Instan Driver - Manager | Driver & Manager | `ChatController` |
| 12 | **Laporan & Analitik** | Grafis & Laporan Bulanan (Mobil Terboros/Termahal) | Admin | `LaporanController` |
| 13 | **Notifikasi Peringatan** | Peringatan Jatuh Tempo Servis, STNK, Pajak, KIR | Semua User | `NotificationController` |

---

## 📌 RINCIAN TEKNIS DOKUMENTASI MODULE

### 1. Module Autentikasi & Profil (Auth & Profile)
1. **Tujuan Module**: Mengelola otentikasi login/logout, registrasi akun baru, dan pembaruan foto/data profil pengguna.
2. **Alur Bisnis**: Pengguna Login/Register ➔ Verifikasi Kredensial & Role ➔ Sesi Dibentuk ➔ Akses Fitur Sesuai Role.
```mermaid
flowchart TD
    A(Pengguna Input Form Login/Register) --> B{Verifikasi Kredensial & Role}
    B -- Gagal --> C(Kembalikan Error Validation / Authentication)
    B -- Berhasil --> D(Sesi Dibentuk & Token Diterbitkan)
    D --> E(Akses Fitur Sesuai Role User)

    classDef default fill:#FFD93D,stroke:#6B4226,stroke-width:2px,color:#3E2A17,rx:10,ry:10;
    classDef highlight fill:#8B5E34,stroke:#3E2A17,stroke-width:2.5px,color:#FFF8E7,rx:10,ry:10;
    classDef decision fill:#FFC107,stroke:#5C3D1F,stroke-width:2px,color:#3E2A17;

    class A,C,D default;
    class E highlight;
    class B decision;
```
3. **Route yang Digunakan**: `GET/POST /login`, `GET/POST /register`, `POST /logout`, `GET/POST /profile`
4. **Controller yang Menangani**: `App\Http\Controllers\AuthController`, `App\Http\Controllers\ProfileController`
5. **Model & Tabel Database**: `App\Models\User` (Tabel `users`)
6. **Request Validation**: `email` (unique:users), `password` (min:8), `profile_photo` (image, max:2048).
7. **Response**: `ViewResponse` (`auth.login`, `auth.register`, `auth.profile`), `RedirectResponse`.

---

### 2. Module Dashboard Utama (Unified Role-Based Dashboard)
1. **Tujuan Module**: Menyajikan statistik ringkasan (KPI), grafik indikator kelayakan armada, dan shortcut aktivitas sesuai role user.
2. **Alur Bisnis**: User Akses `/` ➔ Cek `role` User ➔ Hitung KPI Aktif ➔ Tampilkan Dashboard Spesifik (Admin/Teknisi/Driver).
```mermaid
flowchart TD
    A(User Akses Route /) --> B{Cek Role User}
    B -- Admin / Manager --> C(Kalkulasi KPI Total Armada, Biaya & Feed Aktivitas)
    B -- Teknisi --> D(Kalkulasi Keluhan Baru/Diproses & Jadwal Servis)
    B -- Driver --> E(Filter Kendaraan Siap Pakai & Perlu Perhatian)
    C --> F(Render Dashboard Admin)
    D --> G(Render Dashboard Teknisi)
    E --> H(Render Dashboard Driver)

    classDef default fill:#FFD93D,stroke:#6B4226,stroke-width:2px,color:#3E2A17,rx:10,ry:10;
    classDef highlight fill:#8B5E34,stroke:#3E2A17,stroke-width:2.5px,color:#FFF8E7,rx:10,ry:10;
    classDef decision fill:#FFC107,stroke:#5C3D1F,stroke-width:2px,color:#3E2A17;

    class A,C,D,E default;
    class F,G,H highlight;
    class B decision;
```
3. **Route yang Digunakan**: `GET /` (`dashboard`)
4. **Controller yang Menangani**: `App\Http\Controllers\DashboardController`
5. **Model & Tabel Database**: `Kendaraan`, `MaintenanceSchedule`, `KeluhanKendaraan`, `Pembayaran`, `ChecklistKendaraan`.
6. **Request Validation**: Otorisasi middleware `auth`.
7. **Response**: `ViewResponse` (`dashboard` / `dashboard-teknisi` / `dashboard-driver`).

---

### 3. Module Manajemen Pengguna (RBAC - Role-Based Access Control)
1. **Tujuan Module**: Mengatur hak akses otorisasi bertingkat untuk role `administrator`, `teknisi`, `driver`, `manager`, dan `user`.
2. **Alur Bisnis**: Request Masuk ➔ RoleMiddleware Memeriksa Role User Login ➔ Cocokkan dengan Parameter Middleware ➔ Izinkan Akses atau Tolak (403 Forbidden).
```mermaid
flowchart TD
    A(Request Pengguna ke Endpoint Terproteksi) --> B(Middleware Auth Memeriksa Status Sesi)
    B -- Unauthenticated --> C(Redirect ke Halaman Login)
    B -- Authenticated --> D(RoleMiddleware Memeriksa Parameter Role Route)
    D --> E{Apakah Role User Diizinkan?}
    E -- Tidak --> F(Tampilkan Error 403 Forbidden)
    E -- Ya --> G(Izinkan Request Masuk ke Controller)

    classDef default fill:#FFD93D,stroke:#6B4226,stroke-width:2px,color:#3E2A17,rx:10,ry:10;
    classDef highlight fill:#8B5E34,stroke:#3E2A17,stroke-width:2.5px,color:#FFF8E7,rx:10,ry:10;
    classDef decision fill:#FFC107,stroke:#5C3D1F,stroke-width:2px,color:#3E2A17;

    class A,B,C,D default;
    class F,G highlight;
    class E decision;
```
3. **Route yang Digunakan**: Seluruh Route yang dilindungi `middleware('role:...')`
4. **Controller / Middleware yang Menangani**: `App\Http\Middleware\RoleMiddleware`, `App\Http\Controllers\AuthController`
5. **Model & Tabel Database**: `App\Models\User` (Tabel `users`)
6. **Request Validation**: `role` (required|in:admin,teknisi,user).
7. **Response**: `Next Request`, `Abort 403 Forbidden`, `RedirectResponse`.

---

### 4. Module Manajemen Armada (Kendaraan / Fleet Management)
1. **Tujuan Module**: Mengelola data fisik kendaraan, posisi pool, odometer (KM), foto armada, dan dokumen legalitas (STNK, Pajak, KIR).
2. **Alur Bisnis**: Admin Input Kendaraan ➔ Teknisi/Admin Update KM ➔ Sistem Evaluasi Indikator Warna (🟢 Safe, 🟡 Warning, 🔴 Overdue).
```mermaid
flowchart TD
    A(Admin Input Data / Foto Kendaraan Baru) --> B(Simpan ke Database)
    C(Teknisi/Admin Update Odometer KM) --> D(Update Database)
    B --> E(Sistem Evaluasi Tanggal Pajak, STNK, KIR & Odometer Servis)
    D --> E
    E --> F{Hasil Evaluasi Status}
    F -- Overdue / Expiry Past --> G(🔴 Merah: Jatuh Tempo / Perlu Rawat)
    F -- Expiry <= 30 Hari / Sisa KM <= 500 --> H(🟡 Kuning: Mendekati Jatuh Tempo)
    F -- Kondisi Masih Panjang --> I(🟢 Hijau: Kondisi Safe / Aman)

    classDef default fill:#FFD93D,stroke:#6B4226,stroke-width:2px,color:#3E2A17,rx:10,ry:10;
    classDef highlight fill:#8B5E34,stroke:#3E2A17,stroke-width:2.5px,color:#FFF8E7,rx:10,ry:10;
    classDef decision fill:#FFC107,stroke:#5C3D1F,stroke-width:2px,color:#3E2A17;

    class A,B,C,D,E default;
    class G,H,I highlight;
    class F decision;
```
3. **Route yang Digunakan**: `GET/POST /kendaraan`, `PUT /kendaraan/{id}`, `PATCH /kendaraan/{id}/odometer`, `DELETE /kendaraan/{id}`
4. **Controller yang Menangani**: `App\Http\Controllers\KendaraanController`
5. **Model & Tabel Database**: `App\Models\Kendaraan` (Tabel `kendaraans`)
6. **Request Validation**: `nomor_polisi` (unique:kendaraans), `jenis_kendaraan` (Mobil/Motor), `foto_kendaraan` (max:3072).
7. **Response**: `ViewResponse` (`kendaraan.index`), `RedirectResponse` pesan flash.

---

### 5. Module Pre-Trip Inspection (Checklist Harian Kendaraan)
1. **Tujuan Module**: Memastikan kelayakan fisik armada sebelum dioperasikan via pengecekan 4 parameter harian.
2. **Alur Bisnis**: Driver/Teknisi Isi Form Checklist ➔ Pilih Status OK / Bermasalah ➔ Simpan Log ➔ Otomatis Picu Tiket Keluhan Jika Ada Masalah.
```mermaid
flowchart TD
    A(Driver / Teknisi Buka Form Pre-Trip Inspection) --> B(Isi 13 Parameter: Cairan, Kaki-kaki, Kelistrikan, Kebersihan)
    B --> C(Simpan Record Checklist Kendaraan)
    C --> D{Ada Parameter Bermasalah / Buruk?}
    D -- Ya --> E(Kirim Notifikasi Otomatis ke Admin & Teknisi)
    D -- Tidak --> F(Inspeksi Selesai - Armada Siap Operasional)
    E --> F

    classDef default fill:#FFD93D,stroke:#6B4226,stroke-width:2px,color:#3E2A17,rx:10,ry:10;
    classDef highlight fill:#8B5E34,stroke:#3E2A17,stroke-width:2.5px,color:#FFF8E7,rx:10,ry:10;
    classDef decision fill:#FFC107,stroke:#5C3D1F,stroke-width:2px,color:#3E2A17;

    class A,B,C,E default;
    class F highlight;
    class D decision;
```
3. **Route yang Digunakan**: `GET /checklist/create`, `POST /checklist`
4. **Controller yang Menangani**: `App\Http\Controllers\ChecklistKendaraanController`
5. **Model & Tabel Database**: `ChecklistKendaraan` (`checklists`), `Kendaraan`, `KeluhanKendaraan`.
6. **Request Validation**: `kendaraan_id` (exists:kendaraans,id), `odometer` (numeric), `cairan_*`, `kaki_*`, `listrik_*`, `kebersihan_*`.
7. **Response**: `ViewResponse` (`checklist.create`), `RedirectResponse`.

---

### 6. Module Status Armada (Monitoring Status Operasional)
1. **Tujuan Module**: Menampilkan peta status kesiapan armada secara real-time (Ready, Servis, Rusak, Dipakai).
2. **Alur Bisnis**: Rekap Data Checklist Harian + Tiket Keluhan Pending + Jadwal Servis Aktif ➔ Sajikan Ringkasan Visual Status.
```mermaid
flowchart TD
    A(Sistem Tarik Data Checklist Harian Terbaru) --> D(Koleksi & Agregasikan Status Seluruh Armada)
    B(Sistem Tarik Data Keluhan Berstatus Baru / Diproses) --> D
    C(Sistem Tarik Status Perawatan Servis Aktif) --> D
    D --> E(Sajikan Dashboard Visual Ringkasan Status Armada Real-time)

    classDef default fill:#FFD93D,stroke:#6B4226,stroke-width:2px,color:#3E2A17,rx:10,ry:10;
    classDef highlight fill:#8B5E34,stroke:#3E2A17,stroke-width:2.5px,color:#FFF8E7,rx:10,ry:10;

    class A,B,C,D default;
    class E highlight;
```
3. **Route yang Digunakan**: `GET /status-armada` (`status-armada.index`)
4. **Controller yang Menangani**: `App\Http\Controllers\StatusArmadaController`
5. **Model & Tabel Database**: `Kendaraan`, `ChecklistKendaraan`, `KeluhanKendaraan`.
6. **Request Validation**: Otorisasi middleware `auth`.
7. **Response**: `ViewResponse` (`status-armada.index`).

---

### 7. Module Jadwal & Riwayat Perawatan (Maintenance Schedule & Log)
1. **Tujuan Module**: Menentukan jadwal servis rutin komponen (Oli, Ban, Rem, Coolant) berdasarkan KM/Bulan dan mencatat riwayat perbaikan.
2. **Alur Bisnis**: Atur Jadwal & Interval Komponen ➔ Kalender Memetakan Penggantian ➔ Teknisi Klik "Catat Ganti" ➔ Odometer & Log Servis Terupdate.
```mermaid
flowchart TD
    A(Admin/Teknisi Atur Interval KM / Bulan Per Komponen) --> B(Sistem Hitung Sisa KM & Sisa Hari)
    B --> C{Status Perawatan}
    C -- Terlambat / Segera --> D(Tampilkan Warning Peringatan Servis)
    C -- Aman --> E(Status Kondisi Aman)
    F(Teknisi Melakukan Servis & Klik Catat Ganti) --> G(Salin Record Lama ke Tabel Maintenance Log)
    G --> H(Update Odometer Terakhir & Tanggal Terakhir di Schedule)

    classDef default fill:#FFD93D,stroke:#6B4226,stroke-width:2px,color:#3E2A17,rx:10,ry:10;
    classDef highlight fill:#8B5E34,stroke:#3E2A17,stroke-width:2.5px,color:#FFF8E7,rx:10,ry:10;
    classDef decision fill:#FFC107,stroke:#5C3D1F,stroke-width:2px,color:#3E2A17;

    class A,B,D,F,G default;
    class E,H highlight;
    class C decision;
```
3. **Route yang Digunakan**: `GET/POST /jadwal-perawatan`, `PATCH /jadwal-perawatan/{id}`, `GET /jadwal-perawatan/riwayat`
4. **Controller yang Menangani**: `App\Http\Controllers\JadwalPerawatanController`
5. **Model & Tabel Database**: `MaintenanceSchedule` (`maintenance_schedules`), `MaintenanceLog` (`maintenance_logs`), `Kendaraan`.
6. **Request Validation**: `kendaraan_id` (required), `jenis_perawatan` (required), `interval_km` (numeric), `tanggal_terakhir` (date).
7. **Response**: `ViewResponse` (`jadwal-perawatan.index`, `jadwal-perawatan.riwayat`), `RedirectResponse`.

---

### 8. Module Laporan Keluhan Kendaraan (Issue Reporting)
1. **Tujuan Module**: Pelaporan fisik kendala armada oleh Driver/User dan pemantauan status perbaikan oleh Teknisi/Admin.
2. **Alur Bisnis**: Driver Buat Laporan & Upload Bukti ➔ Status `Baru` ➔ Teknisi Terima & Ubah ke `Diproses` ➔ Setelah Selesai Ubah ke `Selesai`.
```mermaid
flowchart TD
    A(Driver Melaporkan Keluhan Kendaraan) --> B(Record Disimpan dengan Status Baru)
    B --> C(NotifHelper Kirim Notifikasi ke Admin & Manager)
    D(Teknisi / Admin Buka Daftar Keluhan) --> E(Ubah Status Keluhan ke Diproses)
    E --> F(Lakukan Perbaikan Fisik Armada)
    F --> G(Input Catatan Penanganan & Ubah Status ke Selesai)

    classDef default fill:#FFD93D,stroke:#6B4226,stroke-width:2px,color:#3E2A17,rx:10,ry:10;
    classDef highlight fill:#8B5E34,stroke:#3E2A17,stroke-width:2.5px,color:#FFF8E7,rx:10,ry:10;

    class A,B,C,D,E,F default;
    class G highlight;
```
3. **Route yang Digunakan**: `GET/POST /keluhan-kendaraan`, `PATCH /keluhan-kendaraan/{id}`
4. **Controller yang Menangani**: `App\Http\Controllers\KeluhanKendaraanController`
5. **Model & Tabel Database**: `KeluhanKendaraan` (`keluhan_kendaraans`), `Kendaraan`, `User`.
6. **Request Validation**: `kendaraan_id` (required), `judul_keluhan` (required, max:255), `foto_keluhan` (image, max:3072).
7. **Response**: `ViewResponse` (`keluhan-kendaraan.index`), `RedirectResponse`.

---

### 9. Module Klaim Biaya Operasional (Expense Claim & Approval)
1. **Tujuan Module**: Pencatatan transaksi pengeluaran (BBM, Tol, Parkir, Servis Bengkel) dan alur persetujuan (*approval*) anggaran oleh Admin.
2. **Alur Bisnis**: User/Teknisi Input Klaim & Foto Struk ➔ Status `Pending` ➔ Admin Review ➔ Admin Klik `Approve` / `Reject` ➔ Terakumulasi ke Laporan.
```mermaid
flowchart TD
    A(Driver / Teknisi Input Form Klaim Biaya) --> B(Simpan Pembayaran dengan Status Pending)
    B --> C(Admin Review Daftar Klaim Biaya)
    C --> D{Keputusan Admin}
    D -- Reject --> E(Ubah Status ke Ditolak)
    D -- Approve --> F(Ubah Status ke Disetujui)
    F --> G{Metode Pembayaran?}
    G -- QRIS --> H(Tampilkan Modal Transaksi QRIS)
    G -- Transfer / Tunai --> I(Proses Pembayaran Selesai)
    H --> I

    classDef default fill:#FFD93D,stroke:#6B4226,stroke-width:2px,color:#3E2A17,rx:10,ry:10;
    classDef highlight fill:#8B5E34,stroke:#3E2A17,stroke-width:2.5px,color:#FFF8E7,rx:10,ry:10;
    classDef decision fill:#FFC107,stroke:#5C3D1F,stroke-width:2px,color:#3E2A17;

    class A,B,C,F,H default;
    class E,I highlight;
    class D,G decision;
```
3. **Route yang Digunakan**: `GET/POST /pembayaran`, `POST /pembayaran/{id}/approve`, `POST /pembayaran/{id}/reject`
4. **Controller yang Menangani**: `App\Http\Controllers\PembayaranController`
5. **Model & Tabel Database**: `Pembayaran` (`pembayarans`), `Kendaraan`, `User`.
6. **Request Validation**: `kendaraan_id` (required), `kategori` (required), `jumlah_biaya` (numeric, min:1), `bukti_pembayaran` (image, max:3072).
7. **Response**: `ViewResponse` (`pembayaran.index`), `RedirectResponse`.

---

### 10. Module Manajemen Aset & Inventory (Barang / Spare Parts)
1. **Tujuan Module**: Mengelola stok barang inventaris, peralatan bengkel, dan suku cadang armada beserta status kondisinya.
2. **Alur Bisnis**: Catat Barang Baru ➔ Update Jumlah Stok & Status Kondisi (Baik / Perlu Diganti) ➔ Hapus/Arsipkan Barang.
```mermaid
flowchart TD
    A(Admin / Teknisi Input Data Barang Inventaris Baru) --> B(Simpan Record Barang ke Database)
    C(Update Jumlah Stok / Ubah Status Kondisi Barang) --> D(Perbarui Record Database)
    E(Hapus Barang yang Tidak Digunakan / Rusak Total) --> F(Hapus Record dari Database)

    classDef default fill:#FFD93D,stroke:#6B4226,stroke-width:2px,color:#3E2A17,rx:10,ry:10;
    classDef highlight fill:#8B5E34,stroke:#3E2A17,stroke-width:2.5px,color:#FFF8E7,rx:10,ry:10;

    class A,B,C,E default;
    class D,F highlight;
```
3. **Route yang Digunakan**: `GET/POST /barang`, `PATCH /barang/{id}/status`, `DELETE /barang/{id}`
4. **Controller yang Menangani**: `App\Http\Controllers\BarangController`
5. **Model & Tabel Database**: `Barang` (`barangs`).
6. **Request Validation**: `nama_barang` (required), `kode_barang` (unique:barangs), `jumlah` (numeric), `kondisi` (required).
7. **Response**: `ViewResponse` (`barang.index`), `RedirectResponse`.

---

### 11. Module Komunikasi Chat Driver - Manager (Direct Messaging)
1. **Tujuan Module**: Fasilitas pesan instan langsung antara Driver dan Manager dengan polling otomatis dan penandaan status pesan dibaca.
2. **Alur Bisnis**: Driver memilih Manager (atau Manager memilih Driver) ➔ Pengiriman Pesan ➔ Polling Periodik `poll` Memeriksa Pesan Baru ➔ Update Status Dibaca (`is_read`).
```mermaid
flowchart TD
    A(Driver / Manager Buka Fitur Chat) --> B{Role User}
    B -- Driver --> C(Otomatis Sambungkan ke Manager)
    B -- Manager --> D(Pilih Driver dari Sidebar)
    C --> E(Kirim Pesan Chat via POST /chat)
    D --> E
    E --> F(Polling Periodik GET /chat/poll Ambil Pesan Baru)
    F --> G(Update Status is_read Menjadi True)

    classDef default fill:#FFD93D,stroke:#6B4226,stroke-width:2px,color:#3E2A17,rx:10,ry:10;
    classDef highlight fill:#8B5E34,stroke:#3E2A17,stroke-width:2.5px,color:#FFF8E7,rx:10,ry:10;
    classDef decision fill:#FFC107,stroke:#5C3D1F,stroke-width:2px,color:#3E2A17;

    class A,C,D,E,F default;
    class G highlight;
    class B decision;
```
3. **Route yang Digunakan**: `GET /chat`, `POST /chat`, `GET /chat/poll`, `GET /chat/unread-count`
4. **Controller yang Menangani**: `App\Http\Controllers\ChatController`
5. **Model & Tabel Database**: `ChatMessage` (`chat_messages`), `User`.
6. **Request Validation**: `receiver_id` (exists:users,id), `message` (required|string|max:2000).
7. **Response**: `ViewResponse` (`chat.index`), `JsonResponse`.

---

### 12. Module Laporan & Analitik (Analytical Reports)
1. **Tujuan Module**: Analisis akhir bulan total pengeluaran biaya per armada, efisiensi BBM, serta analisis perbaikan termahal & tersering ke bengkel.
2. **Alur Bisnis**: Admin Pilih Periode Bulan/Tahun ➔ Controller Kalkulasi Agregasi Biaya ➔ Tampilkan Grafik Kendaraan Terboros BBM & Termahal Bengkel.
```mermaid
flowchart TD
    A(Admin Buka Halaman Laporan Analitik) --> B(Query Pembayaran Status Disetujui Bulan Ini)
    B --> C(Pisahkan Agregasi Biaya BBM dan Biaya Bengkel/Servis)
    C --> D(Hitung Total Pengeluaran & Frekuensi Masuk Bengkel per Armada)
    D --> E(Identifikasi Kendaraan Terboros BBM & Termahal Bengkel)
    E --> F(Render Visualisasi Chart.js & Tabel Summary Bulanan)

    classDef default fill:#FFD93D,stroke:#6B4226,stroke-width:2px,color:#3E2A17,rx:10,ry:10;
    classDef highlight fill:#8B5E34,stroke:#3E2A17,stroke-width:2.5px,color:#FFF8E7,rx:10,ry:10;

    class A,B,C,D,E default;
    class F highlight;
```
3. **Route yang Digunakan**: `GET /laporan-analitik` (`laporan.index`) [Role: Admin]
4. **Controller yang Menangani**: `App\Http\Controllers\LaporanController`
5. **Model & Tabel Database**: `Pembayaran`, `MaintenanceLog`, `Kendaraan`, `KeluhanKendaraan`.
6. **Request Validation**: `bulan` (numeric), `tahun` (numeric), `kendaraan_id` (nullable).
7. **Response**: `ViewResponse` (`laporan.index`).

---

### 13. Module Notifikasi Peringatan System (Notification Center)
1. **Tujuan Module**: Peringatan (*alert*) otomatis mengenai dokumen armada yang mendekati tenggat (STNK/Pajak/KIR) atau jadwal pemeliharaan yang jatuh tempo.
2. **Alur Bisnis**: System Engine Pindai Tanggal & KM ➔ Tampilkan Badge Jumlah Notifikasi di Navbar ➔ User Klik & Tandai Sudah Dibaca.
```mermaid
flowchart TD
    A(Notification Engine Pindai Dokumen STNK/Pajak/KIR & Jadwal Servis) --> B(Gabungkan Notifikasi Alert Dokumen & Record App Notifications)
    B --> C(Hitung Total Unread Count untuk Badge Navbar)
    D(User Buka Dropdown / Halaman Notifikasi) --> E(Sajikan Daftar Notifikasi Terpadu)
    E --> F(User Klik Notifikasi / Klik Mark All as Read)
    F --> G(Update Status is_read Menjadi True)

    classDef default fill:#FFD93D,stroke:#6B4226,stroke-width:2px,color:#3E2A17,rx:10,ry:10;
    classDef highlight fill:#8B5E34,stroke:#3E2A17,stroke-width:2.5px,color:#FFF8E7,rx:10,ry:10;

    class A,B,C,D,E,F default;
    class G highlight;
```
3. **Route yang Digunakan**: `GET /notifikasi`, `GET /notifikasi/count`, `POST /notifikasi/baca-semua`
4. **Controller yang Menangani**: `App\Http\Controllers\NotificationController`
5. **Model & Tabel Database**: `Notification` (`notifications`), `Kendaraan`, `MaintenanceSchedule`.
6. **Request Validation**: `id` notification (exists:notifications,id).
7. **Response**: `JsonResponse` (untuk badge navbar), `ViewResponse` (`notifikasi.index`).
