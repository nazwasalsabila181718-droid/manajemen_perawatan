<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_schedule_id',
        'kendaraan_id',
        'user_id',
        'jenis_perawatan',
        'km_saat_servis',
        'tanggal_servis',
        'catatan',
    ];

    protected $casts = [
        'tanggal_servis' => 'date',
    ];

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedule()
    {
        return $this->belongsTo(MaintenanceSchedule::class, 'maintenance_schedule_id');
    }
}