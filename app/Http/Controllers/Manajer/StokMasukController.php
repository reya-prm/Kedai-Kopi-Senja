<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\Bahan;
use App\Models\StokMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StokMasukController extends Controller
{   
    //form buat catat stok + riwayat 50 catatan terakhir
    public function index()
    {   
        return view('manajer.stok.index', [
            'ingredients' => Bahan::orderBy('nama')->get(),
            'items' => StokMasuk::with(['bahan', 'user'])->latest()->limit(50)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bahan_id' => ['required', 'exists:bahan,id'],
            'jumlah' => ['required', 'numeric', 'gt:0', 'decimal:0,3', 'max:99999999'],
            //gunanya biar gabisa pilih tanggal masa depan, jadi cukup di hari itu aja
            'tanggal' => ['required', 'date', 'before_or_equal:today'],
        ]);

        DB::transaction(function () use ($data, $request){
            StokMasuk::create([
                'bahan_id' => $data['bahan_id'],
                'user_id' => $request->user()->id,
                'jumlah' => $data['jumlah'],
                'tanggal' => $data['tanggal'],
            ]);

            Bahan::where('id', $data['bahan_id'])->increment('stok', $data['jumlah']);
        });

        return back()->with('succes', 'Stok masuk telat dicatat');
    }
}
