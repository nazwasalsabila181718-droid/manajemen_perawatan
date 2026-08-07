<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kendaraan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class KendaraanController extends Controller
{
    public function index()
    {
        $kendaraans = Kendaraan::orderBy('created_at', 'desc')->get();

        // Menghitung status kendaraan secara dinamis
        $total = $kendaraans->count();
        $perlu_rawat = 0;

        foreach ($kendaraans as $kendaraan) {
            $now = Carbon::now();

            // Evaluasi Pajak & KIR
            $pajakJatuhTempo = Carbon::parse($kendaraan->pajak_tahunan);
            $kirJatuhTempo = $kendaraan->kir_bengkel ? Carbon::parse($kendaraan->kir_bengkel) : null;

            $isPajakOverdue = $pajakJatuhTempo->isPast();
            $isPajakWarning = !$isPajakOverdue && $pajakJatuhTempo->diffInDays($now) <= 30;

            $isKirOverdue = $kirJatuhTempo ? $kirJatuhTempo->isPast() : false;
            $isKirWarning = $kirJatuhTempo && !$isKirOverdue ? $kirJatuhTempo->diffInDays($now) <= 30 : false;

            // Evaluasi Servis Berkala (Tanggal & KM)
            $isServisKmOverdue = false;
            $isServisKmWarning = false;
            if ($kendaraan->servis_terakhir_km !== null) {
                $batasKm = $kendaraan->servis_terakhir_km + $kendaraan->interval_servis_km;
                $isServisKmOverdue = $kendaraan->odometer_terakhir >= $batasKm;
                $isServisKmWarning = !$isServisKmOverdue && ($batasKm - $kendaraan->odometer_terakhir) <= 500;
            }

            $isServisTanggalOverdue = false;
            $isServisTanggalWarning = false;
            if ($kendaraan->servis_terakhir_tanggal !== null) {
                $batasTanggal = Carbon::parse($kendaraan->servis_terakhir_tanggal)->addMonths($kendaraan->interval_servis_bulan);
                $isServisTanggalOverdue = $batasTanggal->isPast();
                $isServisTanggalWarning = !$isServisTanggalOverdue && $batasTanggal->diffInDays($now) <= 14;
            }

            // Gabungkan Status
            if ($isPajakOverdue || $isKirOverdue || $isServisKmOverdue || $isServisTanggalOverdue) {
                $kendaraan->status_perawatan = 'Jatuh Tempo (Merah)';
                $kendaraan->status_badge = 'danger';
                $perlu_rawat++;
            } elseif ($isPajakWarning || $isKirWarning || $isServisKmWarning || $isServisTanggalWarning) {
                $kendaraan->status_perawatan = 'Mendekati Jatuh Tempo (Kuning)';
                $kendaraan->status_badge = 'warning';
                $perlu_rawat++;
            } else {
                $kendaraan->status_perawatan = 'Aman (Hijau)';
                $kendaraan->status_badge = 'success';
            }
        }

        $selesai = $total - $perlu_rawat;

        $mobils = $kendaraans->filter(function($k) {
            return strpos(strtolower($k->jenis_kendaraan), 'motor') === false;
        });

        $motors = $kendaraans->filter(function($k) {
            return strpos(strtolower($k->jenis_kendaraan), 'motor') !== false;
        });

        return view('kendaraan.index', compact('kendaraans', 'mobils', 'motors', 'total', 'perlu_rawat', 'selesai'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_kendaraan'   => 'required|string|max:255',
            'merek'             => 'required|string|max:255',
            'tipe'              => 'required|string|max:255',
            'nomor_polisi'      => 'required|string|max:50',
            'pool_lokasi'       => 'required|string|max:255',
            'nama_driver'       => 'required|string|max:255',
            'tanggal_stnk'      => 'required|date',
            'pajak_tahunan'     => 'required|date',
            'pajak_5_tahunan'   => 'required|date',
            'kir_bengkel'       => 'nullable|date',
            'odometer_terakhir' => 'required|integer|min:0',
            'foto_kendaraan'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->except('foto_kendaraan');

        if ($request->hasFile('foto_kendaraan')) {
            $data['foto_kendaraan'] = $request->file('foto_kendaraan')->store('kendaraan', 'public');
        }

        Kendaraan::create($data);

        return redirect()->back()->with('success', 'Kendaraan baru berhasil didaftarkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jenis_kendaraan'  => 'required|string|max:255',
            'merek'            => 'required|string|max:255',
            'tipe'             => 'required|string|max:255',
            'nomor_polisi'     => 'required|string|max:50',
            'pool_lokasi'      => 'required|string|max:255',
            'nama_driver'      => 'required|string|max:255',
            'tanggal_stnk'     => 'required|date',
            'pajak_tahunan'    => 'required|date',
            'pajak_5_tahunan'  => 'required|date',
            'kir_bengkel'      => 'nullable|date',
            'foto_kendaraan'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $kendaraan = Kendaraan::findOrFail($id);
        $data = $request->except('foto_kendaraan');

        if ($request->hasFile('foto_kendaraan')) {
            // Hapus foto lama
            if ($kendaraan->foto_kendaraan) {
                Storage::disk('public')->delete($kendaraan->foto_kendaraan);
            }
            $data['foto_kendaraan'] = $request->file('foto_kendaraan')->store('kendaraan', 'public');
        }

        $kendaraan->update($data);

        return redirect()->back()->with('success', 'Data kendaraan berhasil diperbarui.');
    }

    public function updateOdometer(Request $request, $id)
    {
        $request->validate([
            'odometer_terakhir' => 'required|integer|min:0',
        ]);

        $kendaraan = Kendaraan::findOrFail($id);
        $kendaraan->update([
            'odometer_terakhir' => $request->odometer_terakhir
        ]);

        return redirect()->back()->with('success', 'Odometer kendaraan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kendaraan = Kendaraan::findOrFail($id);

        // Hapus foto jika ada
        if ($kendaraan->foto_kendaraan) {
            Storage::disk('public')->delete($kendaraan->foto_kendaraan);
        }

        $kendaraan->delete();

        return redirect()->back()->with('success', 'Data kendaraan berhasil dihapus.');
    }
}