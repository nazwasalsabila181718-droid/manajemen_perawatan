<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // 1. Fungsi untuk menampilkan semua user
    public function index()
    {
        $users = User::orderBy('created_at', 'asc')->get();
        return view('admin.users.index', compact('users'));
    }

    // 2. Fungsi untuk menambah pengguna baru
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|in:administrator,teknisi,driver',
        ], [
            'name.required'       => 'Nama lengkap wajib diisi.',
            'email.required'      => 'Alamat email wajib diisi.',
            'email.unique'        => 'Email tersebut sudah digunakan oleh pengguna lain.',
            'password.required'   => 'Kata sandi wajib diisi.',
            'password.min'        => 'Kata sandi minimal 6 karakter.',
            'password.confirmed'  => 'Konfirmasi kata sandi tidak cocok.',
            'role.required'       => 'Peran (role) wajib dipilih.',
            'role.in'             => 'Peran yang dipilih tidak valid.',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna baru berhasil ditambahkan!');
    }

    // 3. Fungsi untuk mengubah nama dan role user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|in:administrator,teknisi,driver',
        ]);

        $user->update([
            'name' => $request->name,
            'role' => $request->role,
        ]);

        return redirect()->back()->with('success', 'Informasi pengguna berhasil diperbarui!');
    }

    // 4. Fungsi untuk menghapus user
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Mencegah user menghapus dirinya sendiri
        if (auth()->id() == $user->id) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->back()->with('success', 'Pengguna berhasil dihapus!');
    }
}