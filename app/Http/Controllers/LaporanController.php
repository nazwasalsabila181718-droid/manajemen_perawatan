<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kendaraan;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // 1. Data BBM / Bensin per kendaraan bulan ini
        // Mengakomodasi kategori "Bahan Bakar" (BBM)
        $bbmPerKendaraan = Pembayaran::with('kendaraan')
            ->whereYear('tanggal_pembayaran', $currentYear)
            ->whereMonth('tanggal_pembayaran', $currentMonth)
            ->whereIn('jenis_biaya', ['Bahan Bakar', 'Bensin', 'BBM'])
            ->where('status', 'disetujui')
            ->select('kendaraan_id', DB::raw('SUM(jumlah) as total_bbm'))
            ->groupBy('kendaraan_id')
            ->orderBy('total_bbm', 'desc')
            ->get();

        // 2. Data Perbaikan / Servis per kendaraan bulan ini
        // Mengakomodasi kategori "Servis Ringan", "Servis Rutin", "Perbaikan", dll.
        $servisPerKendaraan = Pembayaran::with('kendaraan')
            ->whereYear('tanggal_pembayaran', $currentYear)
            ->whereMonth('tanggal_pembayaran', $currentMonth)
            ->whereNotIn('jenis_biaya', ['Bahan Bakar', 'Bensin', 'BBM', 'Tol & Parkir', 'Pajak & Surat'])
            ->where('status', 'disetujui')
            ->select(
                'kendaraan_id', 
                DB::raw('SUM(jumlah) as total_perbaikan'),
                DB::raw('COUNT(id) as frekuensi_bengkel')
            )
            ->groupBy('kendaraan_id')
            ->orderBy('total_perbaikan', 'desc')
            ->get();

        // Cari kendaraan terboros bensin
        $kendaraanTerboros = $bbmPerKendaraan->first();

        // Cari kendaraan termahal perbaikan & tersering masuk bengkel
        $kendaraanTermahal = $servisPerKendaraan->first();
        $kendaraanTersering = $servisPerKendaraan->sortByDesc('frekuensi_bengkel')->first();

        // Siapkan data untuk grafik
        $bbmLabels = [];
        $bbmData = [];
        foreach ($bbmPerKendaraan as $item) {
            $bbmLabels[] = $item->kendaraan ? $item->kendaraan->nomor_polisi : 'Lainnya';
            $bbmData[] = $item->total_bbm;
        }

        $servisLabels = [];
        $servisCostData = [];
        $servisFreqData = [];
        foreach ($servisPerKendaraan as $item) {
            $servisLabels[] = $item->kendaraan ? $item->kendaraan->nomor_polisi : 'Lainnya';
            $servisCostData[] = $item->total_perbaikan;
            $servisFreqData[] = $item->frekuensi_bengkel;
        }

        return view('laporan.index', compact(
            'bbmPerKendaraan',
            'servisPerKendaraan',
            'kendaraanTerboros',
            'kendaraanTermahal',
            'kendaraanTersering',
            'bbmLabels',
            'bbmData',
            'servisLabels',
            'servisCostData',
            'servisFreqData'
        ));
    }
}
