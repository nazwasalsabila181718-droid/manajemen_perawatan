# SISTEM MANAJEMEN PERAWATAN ARMADA (FLEET MAINTENANCE SYSTEM)

Sistem Manajemen Perawatan Armada (*Fleet Maintenance System*) adalah aplikasi berbasis web yang dirancang khusus untuk mengoptimalkan pengelolaan dan pemeliharaan kendaraan operasional perusahaan.

---

## 1. Latar Belakang Proyek
Proyek Manajemen Perawatan Armada (*Fleet Maintenance*) ini dirancang khusus untuk mengoptimalkan pengelolaan kendaraan operasional perusahaan (seperti mobil boks, mobil dinas, atau motor kurir). 

Fokus utama dari keterlibatan siswa/mahasiswa PKL dalam proyek ini bukanlah pada aspek mekanikal atau pembongkaran mesin secara berat. Sebaliknya, proyek ini menitikberatkan pada **sistem administrasi, pengecekan fisik rutin, dan digitalisasi penjadwalan**. Tujuannya adalah memastikan seluruh armada kendaraan perusahaan selalu dalam kondisi siap pakai (*roadworthy*) dan meminimalkan risiko mogok mendadak yang dapat mengganggu operasional bisnis.

---

## 2. Spesifikasi Teknis Sistem
Untuk meningkatkan efisiensi dari sistem manual ke sistem digital, proyek ini dikembangkan sebagai aplikasi berbasis web dengan spesifikasi teknologi sebagai berikut:
* **Framework Aplikasi**: Laravel (PHP)
* **Sistem Basis Data (Database)**: MySQL / PostgreSQL
* **Arsitektur Pengguna**: Multi-user dengan pembagian hak akses (*Role-Based Access Control*) yang terdiri dari 3 level user.

---

## 3. Manajemen Hak Akses (3 User Roles)

| Peran (Role) | Hak Akses & Fungsi Utama dalam Sistem |
| :--- | :--- |
| **Admin** | <ul><li>Memiliki hak akses penuh (*Superuser*) terhadap sistem.</li><li>Mengelola data master kendaraan dan pengguna (Teknisi & User).</li><li>Meninjau laporan akhir rekapitulasi biaya bulanan.</li><li>Mengonfirmasi/menyetujui anggaran perbaikan besar.</li></ul> |
| **Teknisi** | <ul><li>Melakukan input hasil pengecekan fisik harian/mingguan (*Daily Checklist*).</li><li>Memperbarui angka odometer (KM) terbaru.</li><li>Memperbarui status pemeliharaan (misal: *"Sedang Diservis"*, *"Selesai"*).</li><li>Menginput nota pengeluaran perbaikan fisik kendaraan.</li></ul> |
| **User (Driver/Staf)** | <ul><li>Melihat daftar kendaraan operasional yang siap digunakan.</li><li>Melaporkan keluhan atau kendala minor saat membawa kendaraan.</li><li>Mengetahui jadwal jatuh tempo dokumen kendaraan yang sedang dibawa.</li></ul> |

---

## 4. Tahapan Proyek & Tugas Anak PKL (Dibagi Per Minggu)

### 📅 Minggu Pertama: Tahap Pendataan Aset (Database Kendaraan)
Siswa PKL ditugaskan untuk mengumpulkan data mentah armada dan menyusun struktur database awal. Sebelum diimplementasikan ke MySQL/PostgreSQL, mereka dapat memetakan data menggunakan Microsoft Excel atau Google Sheets.

Data wajib yang harus dikumpulkan meliputi:
1. **Identitas Fisik**: Jenis kendaraan, merek, tipe, dan nomor polisi (plat nomor).
2. **Manajemen Operasional**: Lokasi penyimpanan (pool) atau siapa pengguna/supir utamanya.
3. **Legalitas & Pajak**: Tanggal jatuh tempo STNK, Pajak Tahunan, Pajak 5 Tahunan, dan KIR (khusus mobil barang).
4. **Kondisi Aktual**: Angka odometer (KM) terakhir saat pendataan dimulai.

---

### 📅 Minggu Kedua: Tahap Pembuatan Lembar Cek Fisik Harian (Daily Check-list)
Siswa PKL diminta membuat sistem formulir digital (*Pre-trip Inspection*) menggunakan Laravel (atau diawali dengan Google Form sebagai prototipe). Mereka bertanggung jawab melakukan pengecekan fisik langsung ke lapangan setiap pagi dan menginputnya ke sistem dengan parameter:
* **Cairan**: Volume oli mesin, air radiator (*coolant*), minyak rem, dan air wiper.
* **Kaki-kaki**: Tekanan angin ban, kondisi keausan alur ban, dan fungsi pengereman.
* **Kelistrikan**: Lampu utama (dekat/jauh), lampu sein, lampu rem, klakson, dan fungsi AC.
* **Kebersihan**: Kondisi kebersihan interior dan eksterior kendaraan.

---

### 📅 Minggu Ketiga: Tahap Pembuatan Jadwal Servis Berkala (Maintenance Scheduling)
Berdasarkan parameter angka KM (odometer) atau waktu (bulan), siswa PKL membangun modul Kalender Servis di dalam aplikasi.
* **Contoh Aturan**: Setiap kelipatan 5.000 KM atau 3 bulan sekali, kendaraan A wajib masuk bengkel untuk ganti oli.
* **Peringatan Otomatis (*Warning System*)**: Di dalam aplikasi Laravel, sistem harus memberikan indikator warna:
  * 🟡 **Kuning** untuk mendekati jatuh tempo.
  * 🔴 **Merah** untuk melewati jatuh tempo jika ada kendaraan yang sudah mendekati jadwal servis atau batas akhir pajak/KIR.

---

### 📅 Minggu Keempat: Tahap Rekapitulasi Biaya (Expense Tracking) & Evaluasi
Siswa PKL membuat modul pencatatan keuangan untuk mendata seluruh pengeluaran riil setiap kendaraan.
* **Komponen Biaya**: Pembelian bensin/BBM, biaya tol, biaya parkir harian, hingga nota perbaikan dari bengkel resmi maupun bengkel luar.
* **Output Analisis Akhir Bulan**: Di akhir bulan, sistem yang dibuat harus mampu menghasilkan grafik atau laporan ringkas untuk menjawab kebutuhan manajemen, seperti:
  * *"Kendaraan mana yang paling boros mengonsumsi bensin?"*
  * *"Kendaraan mana yang memakan biaya perbaikan paling mahal dan sering masuk bengkel?"*

---

## 5. Indikator Keberhasilan Proyek PKL
1. Tersedianya database seluruh aset kendaraan yang rapi dan terstruktur di MySQL/PostgreSQL.
2. Terimplementasinya sistem aplikasi Laravel yang dapat diakses oleh Admin, Teknisi, dan User sesuai porsinya.
3. Proses birokrasi pengecekan kendaraan menjadi digital (tidak menggunakan kertas manual lagi).
