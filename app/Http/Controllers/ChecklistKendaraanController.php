<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Kendaraan;
use App\Models\ChecklistKendaraan;
use App\Helpers\NotifHelper;

class ChecklistKendaraanController extends Controller
{
    public function create()
    {
        $kendaraans = Kendaraan::all();
        return view('checklist.create', compact('kendaraans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kendaraan_id' => 'required|exists:kendaraans,id',
            'tanggal_cek' => 'required|date',
            // Cairan
            'cairan_oli_mesin' => 'required|in:Baik,Perlu Perhatian,Buruk',
            'cairan_coolant' => 'required|in:Baik,Perlu Perhatian,Buruk',
            'cairan_minyak_rem' => 'required|in:Baik,Perlu Perhatian,Buruk',
            'cairan_wiper' => 'required|in:Baik,Perlu Perhatian,Buruk',
            // Kaki-kaki
            'kaki_tekanan_ban' => 'required|in:Baik,Perlu Perhatian,Buruk',
            'kaki_keausan_ban' => 'required|in:Baik,Perlu Perhatian,Buruk',
            'kaki_rem' => 'required|in:Baik,Perlu Perhatian,Buruk',
            // Kelistrikan
            'listrik_lampu_utama' => 'required|in:Baik,Perlu Perhatian,Buruk',
            'listrik_lampu_sein' => 'required|in:Baik,Perlu Perhatian,Buruk',
            'listrik_lampu_rem' => 'required|in:Baik,Perlu Perhatian,Buruk',
            'listrik_klakson' => 'required|in:Baik,Perlu Perhatian,Buruk',
            'listrik_ac' => 'required|in:Baik,Perlu Perhatian,Buruk',
            // Kebersihan
            'kebersihan_interior' => 'required|in:Baik,Perlu Perhatian,Buruk',
            'kebersihan_eksterior' => 'required|in:Baik,Perlu Perhatian,Buruk',
            'catatan' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['user_id'] = auth()->id();

        ChecklistKendaraan::create($data);

        NotifHelper::kirimKeRole(
            ['administrator', 'teknisi'],
            'checklist',
            'Checklist Harian Baru',
            auth()->user()->name . ' mengisi checklist pre-trip inspection.',
            route('dashboard') // sementara arahkan ke dashboard, ganti ke route('checklist.index') kalau halaman daftar checklist sudah dibuat
        );

        return redirect()->route('dashboard')->with('success', 'Formulir Pre-trip Inspection berhasil disimpan!');
    }
}