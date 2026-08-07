<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    // 1. Menampilkan halaman form pendaftaran
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // 2. Memproses data dari form pendaftaran
    public function register(Request $request)
    {
        // Validasi inputan user
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Menyimpan user baru ke database
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'driver', // Secara default, user baru daftar sebagai driver
        ]);

        // Setelah sukses, lempar ke halaman login
        return redirect('/login')->with('success', 'Akun berhasil dibuat! Silakan login.');
    }
}