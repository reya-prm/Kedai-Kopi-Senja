<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        // role ikut jadi syarat login: akun pelanggan nggak bisa masuk lewat web,
        // karena pelanggan cuma boleh pakai APK
        $credentials = $data + ['role' => ['kasir', 'manajer']];

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            // pesannya sengaja satu aja, biar orang nggak bisa nebak username mana yang terdaftar
            return back()->withErrors(['username' => 'Username atau password salah.'])
                         ->onlyInput('username');
        }

        // ganti ID sesi setelah login, biar sesi lama nggak bisa dipakai orang lain
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();      // buang semua isi sesi
        $request->session()->regenerateToken(); // token CSRF diganti baru

        return redirect()->route('login');
    }
}