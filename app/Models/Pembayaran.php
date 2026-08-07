<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'kendaraan_id',
        'jenis_biaya',
        'jumlah',
        'tanggal_pembayaran',
        'keterangan',
        'status',
        'metode_pembayaran',
    ];

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }
}
