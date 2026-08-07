<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kendaraan;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class OverdueSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(['email'=>'bambang@driver.com'], ['name'=>'Bambang Wijaya', 'password'=>Hash::make('password123'), 'role'=>'driver']);
        User::firstOrCreate(['email'=>'dwi@driver.com'], ['name'=>'Dwi Cahyono', 'password'=>Hash::make('password123'), 'role'=>'driver']);
        User::firstOrCreate(['email'=>'aris@driver.com'], ['name'=>'Aris Setiawan', 'password'=>Hash::make('password123'), 'role'=>'driver']);
        User::firstOrCreate(['email'=>'faris@driver.com'], ['name'=>'Faris Syahputra', 'password'=>Hash::make('password123'), 'role'=>'driver']);

        $k1 = Kendaraan::create([
            'jenis_kendaraan' => 'Mobil Boks',
            'merek' => 'Toyota',
            'tipe' => 'Dyna 110 FT Box',
            'nomor_polisi' => 'B 9123 BOX',
            'pool_lokasi' => 'Pool Logistik Barat',
            'nama_driver' => 'Bambang Wijaya',
            'pajak_tahunan' => Carbon::now()->subDays(15),
            'kir_bengkel' => Carbon::now()->subDays(45),
            'tanggal_stnk' => Carbon::now()->subDays(15),
            'pajak_5_tahunan' => Carbon::now()->addYears(2),
            'odometer_terakhir' => 125400,
        ]);
        Pembayaran::create(['kendaraan_id' => $k1->id, 'jenis_biaya' => 'Servis Rutin', 'jumlah' => 1850000, 'tanggal_pembayaran' => Carbon::now()->subDays(3), 'keterangan' => 'Servis besar & ganti ban']);

        $k2 = Kendaraan::create([
            'jenis_kendaraan' => 'Mobil Dinas',
            'merek' => 'Nissan',
            'tipe' => 'Serena 2.0 Highway Star',
            'nomor_polisi' => 'B 7451 SER',
            'pool_lokasi' => 'Pool Cabang Cilandak',
            'nama_driver' => 'Dwi Cahyono',
            'pajak_tahunan' => Carbon::now()->subDays(8),
            'tanggal_stnk' => Carbon::now()->subDays(8),
            'pajak_5_tahunan' => Carbon::now()->addYears(1),
            'odometer_terakhir' => 108900,
        ]);
        Pembayaran::create(['kendaraan_id' => $k2->id, 'jenis_biaya' => 'Pajak & Surat', 'jumlah' => 1200000, 'tanggal_pembayaran' => Carbon::now()->subDays(5), 'keterangan' => 'Pengurusan perpanjangan STNK']);

        $k3 = Kendaraan::create([
            'jenis_kendaraan' => 'Mobil Dinas',
            'merek' => 'Honda',
            'tipe' => 'Civic RS 1.5 Turbo',
            'nomor_polisi' => 'B 3290 JAT',
            'pool_lokasi' => 'Pool Pusat Slipi',
            'nama_driver' => 'Aris Setiawan',
            'pajak_tahunan' => Carbon::now()->addDays(7),
            'tanggal_stnk' => Carbon::now()->addDays(7),
            'pajak_5_tahunan' => Carbon::now()->addYears(3),
            'odometer_terakhir' => 68400,
        ]);

        $k4 = Kendaraan::create([
            'jenis_kendaraan' => 'Motor',
            'merek' => 'Yamaha',
            'tipe' => 'Aerox 155 VVA CyberCity',
            'nomor_polisi' => 'B 6182 MTR',
            'pool_lokasi' => 'Pool Lapangan Merdeka',
            'nama_driver' => 'Faris Syahputra',
            'pajak_tahunan' => Carbon::now()->addDays(12),
            'tanggal_stnk' => Carbon::now()->addDays(12),
            'pajak_5_tahunan' => Carbon::now()->addYears(2),
            'odometer_terakhir' => 42100,
        ]);

        // Seed MaintenanceSchedules cleanly (only overdue vehicles have past dates)
        $allVehicles = Kendaraan::all();
        $perawatanTypes = [
            ['jenis' => 'Oli Mesin', 'km' => 5000, 'bulan' => 3],
            ['jenis' => 'Oli Gardan / Transmisi', 'km' => 20000, 'bulan' => 12],
            ['jenis' => 'Aki', 'km' => 40000, 'bulan' => 24],
            ['jenis' => 'Ban', 'km' => 30000, 'bulan' => 18],
            ['jenis' => 'Servis Rem & Pad', 'km' => 15000, 'bulan' => 6],
        ];

        foreach ($allVehicles as $index => $k) {
            $isOverdueVehicle = in_array($k->nomor_polisi, ['B 9123 BOX', 'B 7451 SER', 'B 3290 JAT', 'B 6182 MTR']);
            
            // Give 2 maintenance items per vehicle
            $items = array_slice($perawatanTypes, ($index % 3), 2);
            foreach ($items as $pIdx => $p) {
                // Add day offset based on vehicle index so dates spread across different days of month
                $dayOffset = (($index * 4 + $pIdx * 7) % 26) - 13;

                if ($isOverdueVehicle && $pIdx === 0) {
                    // Overdue item
                    $lastDate = Carbon::now()->subMonths($p['bulan'])->addDays($dayOffset - 10);
                    $kmTerakhir = max(0, $k->odometer_terakhir - ($p['km'] + 2500));
                } else {
                    // Healthy / Near item
                    $months = max(1, $p['bulan'] - rand(0, 1));
                    $lastDate = Carbon::now()->subMonths($months)->addDays($dayOffset);
                    $kmTerakhir = max(0, $k->odometer_terakhir - 1500);
                }

                \App\Models\MaintenanceSchedule::create([
                    'kendaraan_id' => $k->id,
                    'jenis_perawatan' => $p['jenis'],
                    'interval_km' => $p['km'],
                    'interval_bulan' => $p['bulan'],
                    'km_terakhir' => $kmTerakhir,
                    'tanggal_terakhir' => $lastDate,
                    'catatan' => 'Perawatan rutin berkala ' . $p['jenis'] . ' untuk armada ' . $k->nomor_polisi,
                ]);
            }
        }
    }
}
