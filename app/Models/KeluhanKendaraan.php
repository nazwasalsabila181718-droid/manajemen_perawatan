<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeluhanKendaraan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kendaraan_id',
        'user_id',
        'ditangani_oleh',
        'keluhan',
        'tingkat_urgensi',
        'status',
        'catatan_penanganan',
    ];

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function pelapor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function penindak()
    {
        return $this->belongsTo(User::class, 'ditangani_oleh');
    }

    public function urgensiBadgeClass(): string
    {
        return match ($this->tingkat_urgensi) {
            'berat' => 'bg-danger',
            'sedang' => 'bg-warning text-dark',
            default => 'bg-secondary',
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'selesai' => 'bg-success',
            'diproses' => 'bg-primary',
            default => 'bg-secondary',
        };
    }
}