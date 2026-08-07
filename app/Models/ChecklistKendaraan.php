<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistKendaraan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kendaraan_id',
        'user_id',
        'tanggal_cek',
        'cairan_oli_mesin',
        'cairan_coolant',
        'cairan_minyak_rem',
        'cairan_wiper',
        'kaki_tekanan_ban',
        'kaki_keausan_ban',
        'kaki_rem',
        'listrik_lampu_utama',
        'listrik_lampu_sein',
        'listrik_lampu_rem',
        'listrik_klakson',
        'listrik_ac',
        'kebersihan_interior',
        'kebersihan_eksterior',
        'catatan',
    ];

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
