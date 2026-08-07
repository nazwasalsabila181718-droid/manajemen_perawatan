<?php
namespace Database\Seeders;

use App\Models\Kendaraan;
use App\Models\MaintenanceSchedule;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DummyMaintenanceSeeder extends Seeder
{
    public function run()
    {
        $kendaraans = Kendaraan::inRandomOrder()->limit(6)->get();

        if ($kendaraans->count() < 6) {
            echo "Kendaraan kurang dari 6";
            return;
        }

        // 1. Aman
        $k = $kendaraans[0];
        $odo = $k->odometer_terakhir ?? 10000;
        MaintenanceSchedule::create([
            'kendaraan_id' => $k->id,
            'jenis_perawatan' => 'Oli Mesin',
            'interval_km' => 5000,
            'interval_bulan' => 6,
            'km_terakhir' => max(0, $odo - 1000), // sisa = 4000 (Aman)
            'tanggal_terakhir' => Carbon::now()->subMonths(1),
        ]);

        // 2. Aman
        $k = $kendaraans[1];
        $odo = $k->odometer_terakhir ?? 15000;
        MaintenanceSchedule::create([
            'kendaraan_id' => $k->id,
            'jenis_perawatan' => 'Kampas Rem',
            'interval_km' => 20000,
            'interval_bulan' => 12,
            'km_terakhir' => max(0, $odo - 5000), // sisa = 15000 (Aman)
            'tanggal_terakhir' => Carbon::now()->subMonths(3),
        ]);

        // 3. Segera
        $k = $kendaraans[2];
        $odo = $k->odometer_terakhir ?? 25000;
        MaintenanceSchedule::create([
            'kendaraan_id' => $k->id,
            'jenis_perawatan' => 'Ban',
            'interval_km' => 40000,
            'interval_bulan' => 24,
            'km_terakhir' => max(0, $odo - 39800), // sisa = 200 KM (Segera)
            'tanggal_terakhir' => Carbon::now()->subMonths(2),
        ]);

        // 4. Segera (Hari)
        $k = $kendaraans[3];
        $odo = $k->odometer_terakhir ?? 30000;
        MaintenanceSchedule::create([
            'kendaraan_id' => $k->id,
            'jenis_perawatan' => 'Aki',
            'interval_km' => null,
            'interval_bulan' => 18,
            'km_terakhir' => null,
            'tanggal_terakhir' => Carbon::now()->subMonths(18)->addDays(10), // sisa = 10 hari (Segera)
        ]);

        // 5. Terlambat
        $k = $kendaraans[4];
        $odo = $k->odometer_terakhir ?? 35000;
        MaintenanceSchedule::create([
            'kendaraan_id' => $k->id,
            'jenis_perawatan' => 'Air Radiator / Coolant',
            'interval_km' => 10000,
            'interval_bulan' => 12,
            'km_terakhir' => max(0, $odo - 11000), // sisa = -1000 KM (Terlambat)
            'tanggal_terakhir' => Carbon::now()->subMonths(5),
        ]);

        // 6. Terlambat (Bulan)
        $k = $kendaraans[5];
        $odo = $k->odometer_terakhir ?? 40000;
        MaintenanceSchedule::create([
            'kendaraan_id' => $k->id,
            'jenis_perawatan' => 'Filter Udara',
            'interval_km' => 10000,
            'interval_bulan' => 6,
            'km_terakhir' => max(0, $odo - 2000), // sisa = 8000 KM (Aman)
            'tanggal_terakhir' => Carbon::now()->subMonths(7), // sisa = -1 bulan (Terlambat)
        ]);

        echo "Berhasil membuat 6 data jadwal perawatan contoh.\n";
    }
}
