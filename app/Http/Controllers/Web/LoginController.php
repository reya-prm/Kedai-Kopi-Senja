<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function form()
    {
        return view('auth.login');
    }

    public function masuk(Request $request)
    {   
        //validasi wajib isi form, biar ga asal submit
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        //membatasi hak login biar bisa cuma kasir dan manajer, trus nolak akun pelanggan juga walau data nya benar
        $kredensial = $data + ['peran' => ['kasir', 'manajer']];

        //bikin fitur remember me, trus kembaliin user ke halaman login kalo misal loginnya gagal trus di kasi pesan yg sesuai dngn kodingan di bawah
        if(! Auth::attempt($kredensial, $request->boolean('ingat'))){
            return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
        }

        //ganti id sesi yg baru, biar yg lama ga di pake sama orng
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function keluar(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate(); //buat buang semua isi sesi
        $request->session()->regenerateToken(); //token csrd diganti baru

        return redirect()->route('login');
    }
}
