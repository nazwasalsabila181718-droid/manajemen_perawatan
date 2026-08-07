<?php

namespace App\Http\Controllers;

use App\Models\KeluhanKendaraan;
use App\Models\Kendaraan;
use App\Helpers\NotifHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeluhanKendaraanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $bisaKelola = in_array($user->role, ['administrator', 'manager', 'teknisi']);

        $query = KeluhanKendaraan::with(['kendaraan', 'pelapor', 'penindak']);

        if (!$bisaKelola) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $keluhans = $query->latest()->get();
        $kendaraans = Kendaraan::orderBy('nomor_polisi')->get();

        return view('keluhan-kendaraan.index', compact('keluhans', 'kendaraans', 'bisaKelola'));
    }

    public function create()
    {
        $kendaraans = Kendaraan::orderBy('nomor_polisi')->get();

        return view('keluhan-kendaraan.create', compact('kendaraans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kendaraan_id' => 'required|exists:kendaraans,id',
            'keluhan' => 'required|string|max:2000',
            'tingkat_urgensi' => 'required|in:ringan,sedang,berat',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'baru';

        KeluhanKendaraan::create($validated);

        NotifHelper::kirimKeRole(
            ['administrator', 'manager'],
            'keluhan',
            'Keluhan Baru: ' . ucfirst($validated['tingkat_urgensi']),
            auth()->user()->name . ' melaporkan keluhan pada kendaraan.',
            route('keluhan-kendaraan.index')
        );

        return redirect()->route('keluhan-kendaraan.index')
            ->with('success', 'Keluhan berhasil dilaporkan.');
    }

    public function update(Request $request, KeluhanKendaraan $keluhanKendaraan)
    {
        $validated = $request->validate([
            'status' => 'required|in:baru,diproses,selesai',
            'catatan_penanganan' => 'nullable|string',
        ]);

        $validated['ditangani_oleh'] = Auth::id();

        $keluhanKendaraan->update($validated);

        return back()->with('success', 'Status keluhan berhasil diperbarui.');
    }
}