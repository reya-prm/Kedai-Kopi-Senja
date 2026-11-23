<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\Bahan;
use Illuminate\Http\Request;

class BahanController extends Controller
{
    // daftar semua bahan, diurutkan sesuai abjad
    public function index()
    {
        return view('manajer.bahan.index', ['items' => Bahan::orderBy('nama')->get()]);
    }

    // daftar bahan yg stoknya dibawah atau sama dngn batas minimum
    public function lowStock()
    {
        return view('manajer.bahan.low-stock', [
            'items' => Bahan::lowStock()->orderBy('nama')->get(),
        ]);
    }

    // form tambah. model kosong dikirim biar form yang sama bisa dipakai buat tambah dan ubah
    public function create()
    {
        return view('manajer.bahan.form', ['bahan' => new Bahan]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'satuan_dasar' => ['required', 'in:g,ml,pcs'],
            'batas_minimum' => ['required', 'numeric', 'min:0', 'max:99999999'],
        ]);

        // cek nama ganda: "Susu" dan " susu " dianggap sama
        $key = Bahan::makeKey($data['nama']);

        if (Bahan::where('kunci_nama', $key)->exists()) {
            return back()->withErrors(['nama' => 'Nama bahan sudah ada.'])->withInput();
        }

        Bahan::create($data);

        return redirect()->route('manajer.bahan.index')->with('success', 'Bahan berhasil ditambahkan.');
    }

    // $bahan keisi otomatis dari id di URL, kalau id-nya nggak ada langsung 404
    public function edit(Bahan $bahan)
    {
        return view('manajer.bahan.form', compact('bahan'));
    }

    public function update(Request $request, Bahan $bahan)
    {
        // satuan_dasar sengaja nggak ada di sini, jadi nggak bisa diubah
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'batas_minimum' => ['required', 'numeric', 'min:0', 'max:99999999'],
        ]);

        // sama kayak tambah, tapi bahan ini sendiri nggak dihitung.
        // kalau nggak dikecualiin, nyimpen "Susu" tanpa ganti nama bakal dianggap bentrok sama dirinya sendiri
        $key = Bahan::makeKey($data['nama']);

        $dipakaiBahanLain = Bahan::where('kunci_nama', $key)
            ->where('id', '!=', $bahan->id)
            ->exists();

        if ($dipakaiBahanLain) {
            return back()->withErrors(['nama' => 'Nama bahan sudah ada.'])->withInput();
        }

        $bahan->update($data);

        return redirect()->route('manajer.bahan.index')->with('success', 'Bahan berhasil diperbarui.');
    }
}
