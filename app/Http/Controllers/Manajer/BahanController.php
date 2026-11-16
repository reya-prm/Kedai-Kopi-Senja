<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\Bahan;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class BahanController extends Controller
{   
    // //daftar semua bahan, sesuai abjad
    // public function index()
    // {
    //     return view('manajer.bahan.index', ['daftar' => Bahan::orderBy('nama')->get()]);
    // }

    // //form tambah. tambah dan ubah bisa dipake disini
    // public function create()
    // {
    //     return view('manajer.bahan.form', ['bahan' => new Bahan]);
    // }

    // public function store(Request $request)
    // {
    //     $data = $request->validate($this->aturan(null));

    //     Bahan::create($data);

    //     return redirect()->route('manajer.bahan.index')->with('sukses', 'Bahan berhasil ditambahkan.');
    // }

    // public function edit(Bahan $bahan)
    // {
    //     return view('manajer.bahan.form', compact('bahan'));
    // }

    // public function update(Request $request, Bahan $bahan)
    // {
    //     $data = $request->validate($this->aturan($bahan));

    //     $bahan->update($data);

    //     return redirect()->route('manajer.bahan.index')->with('sukses', 'Bahan berhasil diperbarui.');
    // }

    // public function destroy(Bahan $bahan)
    // {
    //     try{
    //         $bahan->delete();
    //     } catch (QueryException $e) {
    //         //23000 = 
    //         if ($e->getCode() !== '23000') {
    //             throw $e;
    //         }
    //     }
    // }

}
