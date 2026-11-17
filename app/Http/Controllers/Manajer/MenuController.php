<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        // withCount = hitung jumlah resep tiap menu, hasilnya masuk ke kolom resep_count.
        // dipakai di tabel buat nandain menu yang belum punya resep
        return view('manajer.menu.index', [
            'items' => Menu::withCount('resep')->orderBy('nama')->get(),
        ]);
    }

    // model kosong dikirim biar form yang sama bisa dipakai buat tambah dan ubah
    public function create()
    {
        return view('manajer.menu.form', ['menu' => new Menu]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kategori' => ['nullable', 'string', 'max:100'],
            'harga' => ['required', 'integer', 'min:0', 'max:10000000'],
        ]);

        // checkbox yang nggak dicentang nggak dikirim browser, jadi boolean() otomatis false
        $data['aktif'] = $request->boolean('aktif');

        Menu::create($data);

        return redirect()->route('manajer.menu.index')->with('success', 'Menu berhasil ditambahkan.');
    }

    // $menu keisi otomatis dari id di URL, kalau id-nya nggak ada langsung 404
    public function edit(Menu $menu)
    {
        return view('manajer.menu.form', compact('menu'));
    }

    public function update(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kategori' => ['nullable', 'string', 'max:100'],
            'harga' => ['required', 'integer', 'min:0', 'max:10000000'],
        ]);

        $data['aktif'] = $request->boolean('aktif');

        $menu->update($data);

        return redirect()->route('manajer.menu.index')->with('success', 'Menu berhasil diperbarui.');
    }

    // tombol cepat buat nonaktifin atau aktifin menu dari halaman daftar
    public function toggleActive(Menu $menu)
    {
        // kalau tadinya aktif jadi nonaktif, kalau tadinya nonaktif jadi aktif
        $menu->update(['aktif' => ! $menu->aktif]);

        return back()->with('success', $menu->aktif ? 'Menu diaktifkan.' : 'Menu dinonaktifkan.');
    }

    public function destroy(Menu $menu)
    {
        try {
            $menu->delete(); // resepnya ikut kehapus otomatis (cascade)
        } catch (QueryException $e) {
            // 23000 = database nolak karena menu udah pernah dipesan.
            // sarannya nonaktifin aja. error jenis lain tetap dilempar biar ketahuan
            if ($e->getCode() !== '23000') {
                throw $e;
            }

            return back()->with('error', 'Menu sudah pernah dipesan jadi tidak bisa dihapus. Nonaktifkan saja.');
        }

        return back()->with('success', 'Menu dihapus.');
    }
}