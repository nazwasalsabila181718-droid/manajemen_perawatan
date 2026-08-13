<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StatusArmadaController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\JadwalPerawatanController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\KeluhanKendaraanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication Routes
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (Required Auth)
Route::middleware(['auth'])->group(function () {
    // Unified Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Settings
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // Manajemen Aset / Barang (Asset Management)
    Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
    Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
    Route::patch('/barang/{id}/status', [BarangController::class, 'updateStatus'])->name('barang.update-status');
    Route::delete('/barang/{id}', [BarangController::class, 'destroy'])->name('barang.destroy');

    // Manajemen Armada / Kendaraan (Fleet Management)
    Route::get('/kendaraan', [KendaraanController::class, 'index'])->name('kendaraan.index');
    Route::post('/kendaraan', [KendaraanController::class, 'store'])->name('kendaraan.store')->middleware('role:administrator');
    Route::put('/kendaraan/{id}', [KendaraanController::class, 'update'])->name('kendaraan.update')->middleware('role:administrator,teknisi');
    Route::patch('/kendaraan/{id}/odometer', [KendaraanController::class, 'updateOdometer'])->name('kendaraan.update-odometer')->middleware('role:administrator,teknisi');
    Route::delete('/kendaraan/{id}', [KendaraanController::class, 'destroy'])->name('kendaraan.destroy')->middleware('role:administrator');

    // Pre-trip Inspection (Daily Checklist)
    Route::get('/checklist/create', [\App\Http\Controllers\ChecklistKendaraanController::class, 'create'])->name('checklist.create')->middleware('role:administrator,teknisi,user,driver');
    Route::post('/checklist', [\App\Http\Controllers\ChecklistKendaraanController::class, 'store'])->name('checklist.store')->middleware('role:administrator,teknisi,user,driver');

    // Status Armada
    Route::get('/status-armada', [StatusArmadaController::class, 'index'])->name('status-armada.index');

    // Pembayaran / Klaim Biaya Operasional
    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::post('/pembayaran', [PembayaranController::class, 'store'])->name('pembayaran.store');
    Route::post('/pembayaran/{pembayaran}/approve', [PembayaranController::class, 'approve'])->name('pembayaran.approve')->middleware('role:administrator');
    Route::post('/pembayaran/{pembayaran}/reject', [PembayaranController::class, 'reject'])->name('pembayaran.reject')->middleware('role:administrator');
    Route::get('/pembayaran/{pembayaran}/edit', [PembayaranController::class, 'edit'])->name('pembayaran.edit');
    Route::put('/pembayaran/{pembayaran}', [PembayaranController::class, 'update'])->name('pembayaran.update');
    Route::delete('/pembayaran/{pembayaran}', [PembayaranController::class, 'destroy'])->name('pembayaran.destroy');

    // Laporan Analitik Akhir Bulan
    Route::get('/laporan-analitik', [\App\Http\Controllers\LaporanController::class, 'index'])->name('laporan.index')->middleware('role:administrator');



    // Jadwal Perawatan (Oli, Aki, Ban, dll)
    Route::get('/jadwal-perawatan', [JadwalPerawatanController::class, 'index'])->name('jadwal-perawatan.index');
    Route::get('/jadwal-perawatan/tambah', [JadwalPerawatanController::class, 'create'])->name('jadwal-perawatan.create')->middleware('role:administrator,teknisi');
    Route::post('/jadwal-perawatan', [JadwalPerawatanController::class, 'store'])->name('jadwal-perawatan.store')->middleware('role:administrator,teknisi');
    Route::patch('/jadwal-perawatan/{jadwalPerawatan}', [JadwalPerawatanController::class, 'update'])->name('jadwal-perawatan.update')->middleware('role:administrator,teknisi');
    Route::delete('/jadwal-perawatan/{jadwalPerawatan}', [JadwalPerawatanController::class, 'destroy'])->name('jadwal-perawatan.destroy')->middleware('role:administrator,teknisi');

    // Riwayat Servis
    Route::get('/jadwal-perawatan/riwayat', [JadwalPerawatanController::class, 'riwayat'])->name('jadwal-perawatan.riwayat');

    // Notifikasi Peringatan Perawatan
    Route::prefix('notifikasi')->name('notifikasi.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/count', [NotificationController::class, 'count'])->name('count');
        Route::get('/list', [NotificationController::class, 'list'])->name('list');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/baca-semua', [NotificationController::class, 'markAllAsRead'])->name('read-all');
    });

    // Keluhan Kendaraan
    Route::get('/keluhan-kendaraan', [KeluhanKendaraanController::class, 'index'])->name('keluhan-kendaraan.index');
    Route::get('/keluhan-kendaraan/tambah', [KeluhanKendaraanController::class, 'create'])->name('keluhan-kendaraan.create');
    Route::post('/keluhan-kendaraan', [KeluhanKendaraanController::class, 'store'])->name('keluhan-kendaraan.store');
    Route::patch('/keluhan-kendaraan/{keluhanKendaraan}', [KeluhanKendaraanController::class, 'update'])->name('keluhan-kendaraan.update')->middleware('role:administrator,teknisi');
});