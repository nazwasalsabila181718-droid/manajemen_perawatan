<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;

class BarangController extends Controller
{
    public function index()
    {
        // Mengambil semua data barang dari database
        $barangs = Barang::orderBy('created_at', 'desc')->get();
        
        // Menghitung ringkasan data untuk kotak informasi
        $total = $barangs->sum('jumlah');
        $perlu_rawat = $barangs->where('status', 'Perlu Perawatan')->sum('jumlah');
        $selesai = $barangs->where('status', 'Bagus')->sum('jumlah'); 

        return view('barang.index', compact('barangs', 'total', 'perlu_rawat', 'selesai'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'status' => 'required|in:Bagus,Perlu Perawatan',
        ]);

        // Menyimpan data kiriman dari form barang ke database
        Barang::create([
            'nama_barang' => $request->nama_barang,
            'jumlah' => $request->jumlah,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Aset/Barang baru berhasil ditambahkan.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Bagus,Perlu Perawatan',
        ]);

        $barang = Barang::findOrFail($id);
        $barang->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Status perawatan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);
        $barang->delete();

        return redirect()->back()->with('success', 'Aset/Barang berhasil dihapus.');
    }
}
