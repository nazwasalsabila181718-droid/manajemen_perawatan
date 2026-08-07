<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kendaraan;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $drivers = [
            'Budi Santoso', 'Agus Supriyadi', 'Joko Widodo', 'Ahmad Rizal', 'Reza Rahadian',
            'Slamet Mulyono', 'Iwan Fals', 'Rudi Heryanto', 'Hendra Gunawan', 'Taufik Hidayat'
        ];

        $kendaraans = [
            ['jenis' => 'Mobil Dinas', 'merek' => 'Toyota', 'tipe' => 'Innova Zenix 2.0 V Hybrid'],
            ['jenis' => 'Mobil Boks',  'merek' => 'Isuzu', 'tipe' => 'Elf NLR 55 Alumunium Box'],
            ['jenis' => 'Mobil Dinas', 'merek' => 'Honda', 'tipe' => 'HR-V 1.5 SE CVT'],
            ['jenis' => 'Motor',       'merek' => 'Yamaha', 'tipe' => 'NMAX 155 Connected'],
            ['jenis' => 'Mobil Dinas', 'merek' => 'Mitsubishi', 'tipe' => 'Pajero Sport 2.4 Dakar'],
            ['jenis' => 'Mobil Boks',  'merek' => 'Daihatsu', 'tipe' => 'Gran Max Pick Up 1.5 Box'],
            ['jenis' => 'Motor',       'merek' => 'Honda', 'tipe' => 'PCX 160 ABS'],
            ['jenis' => 'Mobil Dinas', 'merek' => 'Hyundai', 'tipe' => 'Stargazer Prime IVT'],
            ['jenis' => 'Mobil Boks',  'merek' => 'Mitsubishi', 'tipe' => 'Fuso Canter FE 74 Box'],
            ['jenis' => 'Motor',       'merek' => 'Honda', 'tipe' => 'CB150X Adventure'],
        ];

        foreach ($drivers as $index => $nama_driver) {
            $email = strtolower(str_replace(' ', '.', $nama_driver)) . '@driver.com';
            // Create User (Driver)
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $nama_driver,
                    'password' => Hash::make('password123'),
                    'role' => 'driver',
                ]
            );

            // Create Kendaraan
            $nopol = 'B ' . rand(1000, 9999) . ' ' . chr(rand(65, 90)) . chr(rand(65, 90));
            $k = $kendaraans[$index];
            $kendaraan = Kendaraan::create([
                'jenis_kendaraan' => $k['jenis'],
                'merek' => $k['merek'],
                'tipe' => $k['tipe'],
                'nomor_polisi' => $nopol,
                'pool_lokasi' => 'Pool Pusat',
                'nama_driver' => $nama_driver,
                'tanggal_stnk' => Carbon::now()->addMonths(rand(1, 12)),
                'pajak_tahunan' => Carbon::now()->addMonths(rand(1, 12)),
                'pajak_5_tahunan' => Carbon::now()->addYears(rand(1, 4)),
                'kir_bengkel' => $k['jenis'] == 'Mobil Boks' ? Carbon::now()->addMonths(rand(1, 6)) : null,
                'odometer_terakhir' => rand(10000, 150000),
            ]);

            // Add dummy pembayaran for this month so chart looks good
            Pembayaran::create([
                'kendaraan_id' => $kendaraan->id,
                'jenis_biaya' => rand(0, 1) ? 'Servis Rutin' : 'Pajak & Surat',
                'jumlah' => rand(500000, 2500000),
                'tanggal_pembayaran' => Carbon::now()->subDays(rand(1, 15)),
                'keterangan' => 'Pengeluaran rutin untuk ' . $k['merek'] . ' ' . $k['tipe'],
            ]);
        }
    }
}
