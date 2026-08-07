<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\Kendaraan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isDriver = $user && $user->role === 'driver';

        if ($isDriver) {
            // Ambil kendaraan yang dipegang oleh driver ini
            $driverKendaraan = Kendaraan::where('nama_driver', $user->name)->get();
            
            // Jika akun driver generik belum punya kendaraan spesifik, ambil 1 kendaraan pertama
            if ($driverKendaraan->isEmpty()) {
                $firstKendaraan = Kendaraan::orderBy('id', 'asc')->first();
                if ($firstKendaraan) {
                    $driverKendaraan = collect([$firstKendaraan]);
                }
            }

            $kendaraans = $driverKendaraan;
            $kendaraanIds = $kendaraans->pluck('id')->toArray();

            // Filter pembayaran khusus kendaraan driver ini saja
            $pembayarans = Pembayaran::with('kendaraan')
                ->whereIn('kendaraan_id', $kendaraanIds)
                ->orderBy('tanggal_pembayaran', 'desc')
                ->get();
        } else {
            // Untuk Admin & Teknisi: Tampilkan semua data
            $pembayarans = Pembayaran::with('kendaraan')->orderBy('tanggal_pembayaran', 'desc')->get();
            $kendaraans = Kendaraan::orderBy('nomor_polisi')->get();
            $kendaraanIds = $kendaraans->pluck('id')->toArray();
        }

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Hitung total pengeluaran biaya per kendaraan
        $biayaPerKendaraan = Pembayaran::with('kendaraan')
            ->select('kendaraan_id', DB::raw('SUM(jumlah) as total_biaya'))
            ->groupBy('kendaraan_id')
            ->get();

        $chartLabels = [];
        $chartData = [];

        foreach ($biayaPerKendaraan as $biaya) {
            $namaKendaraan = $biaya->kendaraan ? $biaya->kendaraan->nomor_polisi : 'Armada';
            $chartLabels[] = $namaKendaraan;
            $chartData[] = (float) $biaya->total_biaya;
        }

        $totalArmada = $kendaraans->count();
        $jumlahAman = 0;
        $jumlahMendekati = 0;
        $jumlahJatuhTempo = 0;

        foreach ($kendaraans as $k) {
            $pajakJatuhTempo = $k->pajak_tahunan ? Carbon::parse($k->pajak_tahunan) : null;
            $kirJatuhTempo = $k->kir_bengkel ? Carbon::parse($k->kir_bengkel) : null;
            $stnkJatuhTempo = $k->tanggal_stnk ? Carbon::parse($k->tanggal_stnk) : null;

            $isPajakOverdue = $pajakJatuhTempo ? $pajakJatuhTempo->isPast() : false;
            $isPajakNear = $pajakJatuhTempo && !$isPajakOverdue && $pajakJatuhTempo->diffInDays(now()) <= 30;

            $isKirOverdue = $kirJatuhTempo ? $kirJatuhTempo->isPast() : false;
            $isKirNear = $kirJatuhTempo && !$isKirOverdue && $kirJatuhTempo->diffInDays(now()) <= 30;

            $isStnkOverdue = $stnkJatuhTempo ? $stnkJatuhTempo->isPast() : false;
            $isStnkNear = $stnkJatuhTempo && !$isStnkOverdue && $stnkJatuhTempo->diffInDays(now()) <= 30;

            $isOdoTinggi = $k->odometer_terakhir >= 100000;

            if ($isPajakOverdue || $isKirOverdue || $isStnkOverdue || $isOdoTinggi) {
                $jumlahJatuhTempo++;
            } elseif ($isPajakNear || $isKirNear || $isStnkNear) {
                $jumlahMendekati++;
            } else {
                $jumlahAman++;
            }
        }

        return view('pembayaran.index', compact(
            'pembayarans', 'chartLabels', 'chartData', 'kendaraans',
            'totalArmada', 'jumlahAman', 'jumlahMendekati', 'jumlahJatuhTempo'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kendaraan_id'       => 'required|exists:kendaraans,id',
            'jenis_biaya'        => 'required|string',
            'jumlah'             => 'required|numeric|min:0',
            'tanggal_pembayaran' => 'required|date',
            'keterangan'         => 'nullable|string',
            'metode_pembayaran'  => 'required|in:transfer,qris,tunai',
        ], [
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih.',
            'metode_pembayaran.in'       => 'Metode pembayaran tidak valid.',
        ]);

        // Semua klaim baru selalu masuk pending – Admin wajib menyetujui
        Pembayaran::create([
            'kendaraan_id'      => $request->kendaraan_id,
            'jenis_biaya'       => $request->jenis_biaya,
            'jumlah'            => $request->jumlah,
            'tanggal_pembayaran'=> $request->tanggal_pembayaran,
            'keterangan'        => $request->keterangan,
            'metode_pembayaran' => $request->metode_pembayaran,
            'status'            => 'pending',
        ]);

        return redirect()->back()->with('success', 'Klaim biaya berhasil diajukan dan sedang menunggu persetujuan Admin.');
    }

    /**
     * Menyetujui klaim biaya
     */
    public function approve($id)
    {
        $pembayaran = Pembayaran::with('kendaraan')->findOrFail($id);
        $pembayaran->update(['status' => 'disetujui']);

        // Jika metode pembayaran QRIS, kirim data ke session agar modal QRIS tampil
        if ($pembayaran->metode_pembayaran === 'qris') {
            return redirect()->back()->with('success', 'Klaim berhasil disetujui.')
                ->with('qris_show', true)
                ->with('qris_jumlah', $pembayaran->jumlah)
                ->with('qris_kendaraan', $pembayaran->kendaraan ? $pembayaran->kendaraan->nomor_polisi : '-')
                ->with('qris_jenis', $pembayaran->jenis_biaya);
        }

        return redirect()->back()->with('success', 'Klaim biaya berhasil disetujui.');
    }

    /**
     * Menolak klaim biaya
     */
    public function reject($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        $pembayaran->update(['status' => 'ditolak']);

        return redirect()->back()->with('success', 'Klaim biaya telah ditolak.');
    }

    /**
     * Menampilkan halaman edit pembayaran
     */
    public function edit(Pembayaran $pembayaran)
    {
        $kendaraans = Kendaraan::orderBy('nomor_polisi')->get();
        return view('pembayaran.edit', compact('pembayaran', 'kendaraans'));
    }

    /**
     * Menyimpan perubahan data pembayaran
     */
    public function update(Request $request, Pembayaran $pembayaran)
    {
        $request->validate([
            'kendaraan_id' => 'required|exists:kendaraans,id',
            'jenis_biaya' => 'required|string',
            'jumlah' => 'required|numeric|min:0',
            'tanggal_pembayaran' => 'required|date',
            'keterangan' => 'nullable|string'
        ]);

        $data = $request->all();
        if ($request->jumlah >= 1000000) {
            $data['status'] = 'pending';
        } else {
            $data['status'] = 'disetujui';
        }

        $pembayaran->update($data);

        return redirect()->route('pembayaran.index')->with('success', 'Rincian biaya berhasil diperbarui.');
    }

    /**
     * Menghapus data pembayaran
     */
    public function destroy(Pembayaran $pembayaran)
    {
        $pembayaran->delete();

        return redirect()->route('pembayaran.index')->with('success', 'Rincian biaya berhasil dihapus.');
    }
}