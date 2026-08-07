<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceLog;
use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalPerawatanController extends Controller
{
    public function index(Request $request)
    {
        $query = MaintenanceSchedule::with('kendaraan');

        if ($request->filled('kendaraan_id')) {
            $query->where('kendaraan_id', $request->kendaraan_id);
        }

        $jadwal = $query->get()->sortBy(function ($item) {
            return match ($item->status()) {
                'terlambat' => 0,
                'segera' => 1,
                default => 2,
            };
        });

        $kendaraans = Kendaraan::orderBy('nomor_polisi')->get();

        return view('jadwal-perawatan.index', compact('jadwal', 'kendaraans'));
    }

    public function create()
    {
        $kendaraans = Kendaraan::orderBy('nomor_polisi')->get();
        $jenisPerawatan = MaintenanceSchedule::JENIS_PERAWATAN;

        return view('jadwal-perawatan.create', compact('kendaraans', 'jenisPerawatan'));
    }

    public function store(Request $request)
    {
        if ($request->has('km_terakhir')) {
            $request->merge([
                'km_terakhir' => str_replace(['.', ','], '', $request->km_terakhir)
            ]);
        }

        $validated = $request->validate([
            'kendaraan_id'      => 'required|exists:kendaraans,id',
            'jenis_perawatan'   => 'required|string|max:255',
            'interval_km'       => 'nullable|integer|min:0',
            'interval_bulan'    => 'nullable|integer|min:0',
            'km_terakhir'       => 'nullable|integer|min:0',
            'tanggal_terakhir'  => 'nullable|date',
            'catatan'           => 'nullable|string',
        ]);

        MaintenanceSchedule::create($validated);

        return redirect()->route('jadwal-perawatan.index')
            ->with('success', 'Jadwal perawatan berhasil ditambahkan.');
    }

    public function update(Request $request, MaintenanceSchedule $jadwalPerawatan)
    {
        if ($request->has('km_terakhir')) {
            $request->merge([
                'km_terakhir' => str_replace(['.', ','], '', $request->km_terakhir)
            ]);
        }

        $validated = $request->validate([
            'km_terakhir'      => 'nullable|integer|min:0',
            'tanggal_terakhir' => 'nullable|date',
            'catatan'          => 'nullable|string',
        ]);

        // Catat riwayat SEBELUM data lama ditimpa
        MaintenanceLog::create([
            'maintenance_schedule_id' => $jadwalPerawatan->id,
            'kendaraan_id' => $jadwalPerawatan->kendaraan_id,
            'user_id' => Auth::id(),
            'jenis_perawatan' => $jadwalPerawatan->jenis_perawatan,
            'km_saat_servis' => $validated['km_terakhir'] ?? null,
            'tanggal_servis' => $validated['tanggal_terakhir'] ?? null,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        $jadwalPerawatan->update($validated);

        return back()->with('success', 'Data perawatan berhasil diperbarui (dicatat sudah diganti).');
    }

    public function destroy(MaintenanceSchedule $jadwalPerawatan)
    {
        $jadwalPerawatan->delete();

        return back()->with('success', 'Jadwal perawatan dihapus.');
    }

    /**
     * Halaman Riwayat Servis — log semua penggantian yang pernah dilakukan.
     */
    public function riwayat(Request $request)
    {
        $query = MaintenanceLog::with(['kendaraan', 'user']);

        if ($request->filled('kendaraan_id')) {
            $query->where('kendaraan_id', $request->kendaraan_id);
        }

        $logs = $query->latest()->get();
        $kendaraans = Kendaraan::orderBy('nomor_polisi')->get();

        return view('jadwal-perawatan.riwayat', compact('logs', 'kendaraans'));
    }
}