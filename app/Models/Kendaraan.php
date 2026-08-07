<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    use HasFactory;

    protected $fillable = [
        'jenis_kendaraan',
        'merek',
        'tipe',
        'nomor_polisi',
        'pool_lokasi',
        'nama_driver',
        'tanggal_stnk',
        'pajak_tahunan',
        'pajak_5_tahunan',
        'kir_bengkel',
        'odometer_terakhir',
        'foto_kendaraan',
        'servis_terakhir_km',
        'servis_terakhir_tanggal',
        'interval_servis_km',
        'interval_servis_bulan',
    ];

    public function maintenanceSchedules()
    {
        return $this->hasMany(MaintenanceSchedule::class);
    }
}
