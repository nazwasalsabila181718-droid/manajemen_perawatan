<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barang;
use App\Models\Kendaraan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed User (Role Based)
        User::updateOrCreate(
            ['email' => 'admin@maint.io'],
            [
                'name' => 'Admin Utama',
                'password' => Hash::make('admin'),
                'role' => 'administrator',
                'profile_photo' => 'profile_photos/LIKd5ppmqvzf0IwjsZ4EZiLnxoGzbOjE2o8TnJXF.jpg',
            ]
        );


        User::updateOrCreate(
            ['email' => 'teknisi@maint.io'],
            [
                'name' => 'Teknisi Handal',
                'password' => Hash::make('password123'),
                'role' => 'teknisi',
            ]
        );

        User::updateOrCreate(
            ['email' => 'driver@maint.io'],
            [
                'name' => 'Driver Armada',
                'password' => Hash::make('password123'),
                'role' => 'driver',
            ]
        );

        // 2. Seed Barang
        if (Barang::count() == 0) {
            Barang::create(['nama_barang' => 'Air Conditioner (AC) Aula Utama', 'jumlah' => 4, 'status' => 'Bagus']);
            Barang::create(['nama_barang' => 'Laptop Lenovo ThinkPad L14 Gen 2', 'jumlah' => 12, 'status' => 'Bagus']);
            Barang::create(['nama_barang' => 'Printer Epson L3210 (HRD)', 'jumlah' => 2, 'status' => 'Perlu Perawatan']);
        }
        
        // 3. Seed Kendaraan
        if (Kendaraan::count() == 0) {
            Kendaraan::create([
                'jenis_kendaraan' => 'Mobil Dinas',
                'merek' => 'Toyota',
                'tipe' => 'Avanza 1.3 G M/T',
                'nomor_polisi' => 'B 1980 SMA',
                'pool_lokasi' => 'Pool Pusat Slipi',
                'nama_driver' => 'Joko Susilo',
                'tanggal_stnk' => Carbon::now()->addMonths(6),
                'pajak_tahunan' => Carbon::now()->addMonths(6),
                'pajak_5_tahunan' => Carbon::now()->addYears(2),
                'odometer_terakhir' => 45200,
            ]);
        }

        // 4. Run DummyDataSeeder & OverdueSeeder
        $this->call(DummyDataSeeder::class);
        $this->call(OverdueSeeder::class);
    }
}
