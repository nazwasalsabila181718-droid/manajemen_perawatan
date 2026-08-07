<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MaintenanceSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'kendaraan_id',
        'jenis_perawatan',
        'interval_km',
        'interval_bulan',
        'km_terakhir',
        'tanggal_terakhir',
        'catatan',
    ];

    protected $casts = [
        'tanggal_terakhir' => 'date',
    ];

    public const JENIS_PERAWATAN = [
        'Oli Mesin',
        'Oli Gardan / Transmisi',
        'Filter Udara',
        'Filter Oli',
        'Aki',
        'Ban',
        'Kampas Rem',
        'Timing Belt / Rantai Keteng',
        'Air Radiator / Coolant',
        'Busi',
    ];

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function sisaKm(): ?int
    {
        if (!$this->interval_km || !$this->km_terakhir) {
            return null;
        }

        $odometerSekarang = $this->kendaraan->odometer_terakhir ?? $this->km_terakhir;
        $batasKm = $this->km_terakhir + $this->interval_km;

        return $batasKm - $odometerSekarang;
    }

    public function sisaHari(): ?int
    {
        if (!$this->interval_bulan || !$this->tanggal_terakhir) {
            return null;
        }

        $batasTanggal = $this->tanggal_terakhir->copy()->addMonths($this->interval_bulan);

        return now()->diffInDays($batasTanggal, false);
    }

    public function status(): string
    {
        $sisaKm = $this->sisaKm();
        $sisaHari = $this->sisaHari();

        $indikator = [];
        if ($sisaKm !== null) $indikator[] = $sisaKm;
        if ($sisaHari !== null) $indikator[] = $sisaHari;

        if (empty($indikator)) {
            return 'aman';
        }

        $palingMendesak = min($indikator);

        if ($palingMendesak < 0) return 'terlambat';
        if ($palingMendesak <= 500 || ($sisaHari !== null && $sisaHari <= 14)) return 'segera';

        return 'aman';
    }

    public function statusLabel(): string
    {
        return match ($this->status()) {
            'terlambat' => 'Terlambat',
            'segera' => 'Segera Ganti',
            default => 'Aman',
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status()) {
            'terlambat' => 'bg-danger',
            'segera' => 'bg-warning text-dark',
            default => 'bg-success',
        };
    }
}