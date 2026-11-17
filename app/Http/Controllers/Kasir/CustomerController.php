<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomerController extends Controller
{   
    //daftar pelanggan, bisa nyari lewat nomor telepon
    public function index(Request $request)
    {
        $query = User::where('role', 'pelanggan');

        if ($request->filled('q')) {
            $query->where('phone', 'like', '%' . $request->q . '%');
        }

        return view('kasir.customers.index', [
            'items' => $query->orderBy('name')->limit(100)->get(),
        ]);
    }

    public function resetPassword(User $user)
    {   
        //biar fitur ini berlaku cuma buat si pelanggan
        if ($user->role !== 'pelanggan'){
            abort(404);
        }

        //password sementara yg acak 10 karakter. model user yang nge hash otomatis 
        $temp = Str::random(10);

        $user->update([
            'password' => $temp,
            'must_change_password' => true, //wajib ganti password saat login berikutnya
        ]);

        return back()->with('success', "Password sementara untuk {$user->name} : {$temp} (sampaikan ke pelanggan, pesan ini hanya tampil sekali).");
    }
}
