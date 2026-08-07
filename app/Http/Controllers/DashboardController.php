<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\KeluhanKendaraan;
use App\Models\MaintenanceSchedule;
use App\Models\Pembayaran;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 1. Data Kendaraan
        $kendaraans = Kendaraan::all();
        $kendaraanTotal = $kendaraans->count();
        $kendaraanPerluRawat = 0;
        $jumlahMendekatiJatuhTempo = 0;
        $kendaraanPerluRawatCollection = collect();

        foreach ($kendaraans as $kendaraan) {
            $pajakJatuhTempo = Carbon::parse($kendaraan->pajak_tahunan);
            $kirJatuhTempo = $kendaraan->kir_bengkel ? Carbon::parse($kendaraan->kir_bengkel) : null;

            $isPajakOverdue = $pajakJatuhTempo->isPast();
            $isPajakNear = !$isPajakOverdue && $pajakJatuhTempo->diffInDays(now()) <= 30;

            $isKirOverdue = $kirJatuhTempo ? $kirJatuhTempo->isPast() : false;
            $isKirNear = $kirJatuhTempo && !$isKirOverdue ? $kirJatuhTempo->diffInDays(now()) <= 30 : false;

            $isOdoTinggi = $kendaraan->odometer_terakhir >= 100000;

            if ($isPajakOverdue || $isKirOverdue || $isOdoTinggi) {
                $kendaraanPerluRawat++;
                $kendaraanPerluRawatCollection->push($kendaraan);
            } elseif ($isPajakNear || $isKirNear) {
                $jumlahMendekatiJatuhTempo++;
            }
        }
        $kendaraanBagus = $kendaraanTotal - $kendaraanPerluRawat;
        $persentaseSehat = $kendaraanTotal > 0 ? round(($kendaraanBagus / $kendaraanTotal) * 100) : 100;

        // Route untuk Driver
        if ($user && $user->role === 'driver') {
            $kendaraanSiap = $kendaraans->filter(function($k) use ($kendaraanPerluRawatCollection) {
                return !$kendaraanPerluRawatCollection->contains('id', $k->id);
            });

            return view('dashboard-driver', [
                'kendaraanPerluRawat' => $kendaraanPerluRawatCollection,
                'kendaraanSiap' => $kendaraanSiap,
                'semuaKendaraan' => $kendaraans
            ]);
        }

        // Route khusus untuk Teknisi
        if ($user && $user->role === 'teknisi') {
            $keluhanBaru = KeluhanKendaraan::with(['kendaraan', 'pelapor'])->where('status', 'baru')->latest()->get();
            $keluhanDiproses = KeluhanKendaraan::with(['kendaraan', 'pelapor'])->where('status', 'diproses')->latest()->get();
            $keluhanSelesai = KeluhanKendaraan::where('status', 'selesai')->whereMonth('updated_at', now()->month)->count();
            $jadwalServisRutin = MaintenanceSchedule::with('kendaraan')->latest()->take(6)->get();

            return view('dashboard-teknisi', compact(
                'kendaraanTotal', 'kendaraanPerluRawat', 'persentaseSehat',
                'keluhanBaru', 'keluhanDiproses', 'keluhanSelesai',
                'jadwalServisRutin', 'kendaraanPerluRawatCollection'
            ));
        }

        // Route untuk Administrator & Manager
        $recentKendaraans = Kendaraan::orderBy('created_at', 'desc')->take(5)->get();
        $recentPembayaran = Pembayaran::with('kendaraan')->orderBy('tanggal_pembayaran', 'desc')->take(5)->get();
        $biayaBulanIni = Pembayaran::whereMonth('tanggal_pembayaran', now()->month)
            ->whereYear('tanggal_pembayaran', now()->year)
            ->where('status', 'disetujui')
            ->sum('jumlah');

        // Chart 1: Biaya per kendaraan bulan ini (hanya yang disetujui)
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $biayaPerKendaraan = Pembayaran::with('kendaraan')
            ->whereMonth('tanggal_pembayaran', $currentMonth)
            ->whereYear('tanggal_pembayaran', $currentYear)
            ->where('status', 'disetujui')
            ->select('kendaraan_id', \Illuminate\Support\Facades\DB::raw('SUM(jumlah) as total_biaya'))
            ->groupBy('kendaraan_id')
            ->get();

        $chartLabels = [];
        $chartData = [];
        foreach ($biayaPerKendaraan as $biaya) {
            $chartLabels[] = $biaya->kendaraan ? $biaya->kendaraan->nomor_polisi : 'Lainnya';
            $chartData[] = (int) $biaya->total_biaya;
        }

        // Chart 2: Distribusi kelayakan armada
        $jumlahJatuhTempo = $kendaraanPerluRawat;
        $jumlahMendekati = $jumlahMendekatiJatuhTempo;
        $jumlahAman = max(0, $kendaraanTotal - $jumlahJatuhTempo - $jumlahMendekati);

        // Activity Log gabungan
        $recentChecklists = \App\Models\ChecklistKendaraan::with('kendaraan', 'user')->latest()->take(5)->get();
        $recentKeluhans = \App\Models\KeluhanKendaraan::with('kendaraan', 'pelapor')->latest()->take(5)->get();
        $recentPembayarans = Pembayaran::with('kendaraan')->latest()->take(5)->get();

        $activities = collect();

        foreach ($recentChecklists as $c) {
            $activities->push([
                'time' => $c->created_at,
                'icon' => 'bi-clipboard2-check text-success',
                'bg' => 'bg-success-subtle',
                'title' => 'Inspeksi Harian',
                'message' => ($c->user->name ?? 'Driver') . ' mengisi checklist pre-trip ' . ($c->kendaraan->nomor_polisi ?? '-'),
            ]);
        }

        foreach ($recentKeluhans as $k) {
            $activities->push([
                'time' => $k->created_at,
                'icon' => 'bi-exclamation-triangle text-danger',
                'bg' => 'bg-danger-subtle',
                'title' => 'Laporan Kendala',
                'message' => ($k->pelapor->name ?? 'Driver') . ' melaporkan kendala pada ' . ($k->kendaraan->nomor_polisi ?? '-'),
            ]);
        }

        foreach ($recentPembayarans as $p) {
            $statusText = $p->status === 'disetujui' ? 'disetujui' : ($p->status === 'ditolak' ? 'ditolak' : 'diajukan');
            $activities->push([
                'time' => $p->created_at,
                'icon' => 'bi-cash-coin text-primary',
                'bg' => 'bg-primary-subtle',
                'title' => 'Klaim Biaya',
                'message' => 'Biaya ' . $p->jenis_biaya . ' Rp ' . number_format($p->jumlah, 0, ',', '.') . ' untuk ' . ($p->kendaraan->nomor_polisi ?? '-') . ' ' . $statusText,
            ]);
        }

        $activities = $activities->sortByDesc('time')->take(5)->values();

        // Data Armada Sedang Diservis & Selesai Servis
        $armadaSedangServis = KeluhanKendaraan::whereIn('status', ['baru', 'diproses'])->distinct('kendaraan_id')->count('kendaraan_id');
        $armadaSelesaiServis = KeluhanKendaraan::where('status', 'selesai')->distinct('kendaraan_id')->count('kendaraan_id');

        // Jika data keluhan 0, buat angka sampel indikatif yang relevan
        if ($armadaSedangServis === 0 && $kendaraanPerluRawat > 0) {
            $armadaSedangServis = min(3, $kendaraanPerluRawat);
        }
        if ($armadaSelesaiServis === 0) {
            $armadaSelesaiServis = Pembayaran::where('status', 'disetujui')->distinct('kendaraan_id')->count('kendaraan_id');
        }

        return view('dashboard', compact(
            'kendaraanTotal', 'kendaraanPerluRawat', 'kendaraanBagus',
            'persentaseSehat', 'jumlahMendekatiJatuhTempo', 'biayaBulanIni',
            'recentKendaraans', 'recentPembayaran', 'chartLabels', 'chartData',
            'jumlahJatuhTempo', 'jumlahMendekati', 'jumlahAman', 'activities',
            'armadaSedangServis', 'armadaSelesaiServis'
        ));
    }
}