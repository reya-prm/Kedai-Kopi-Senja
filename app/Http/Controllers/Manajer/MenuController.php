<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Bahan;
use App\Models\Resep;
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

        // menu baru selalu nonaktif dulu, baru bisa diaktifin kalau udah punya resep
        $data['aktif'] = false;

        $menu = Menu::create($data);

        // langsung ke halaman ubah biar resepnya bisa diisi
        return redirect()->route('manajer.menu.edit', $menu)
            ->with('success', 'Menu dibuat dan masih nonaktif. Isi resepnya dulu, baru bisa diaktifkan.');
    }

    // $menu keisi otomatis dari id di URL, kalau id-nya nggak ada langsung 404
    public function edit(Menu $menu)
    {
        return view('manajer.menu.form', [
            'menu' => $menu,
            'ingredients' => Bahan::orderBy('nama')->get(), // buat pilihan bahan di form resep
        ]);
    }

    public function update(Request $request, Menu $menu)
    {
        // aktif sengaja nggak ada di sini: statusnya cuma diubah lewat tombol Aktifkan/Nonaktifkan
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kategori' => ['nullable', 'string', 'max:100'],
            'harga' => ['required', 'integer', 'min:0', 'max:10000000'],
        ]);

        $menu->update($data);

        return redirect()->route('manajer.menu.index')->with('success', 'Menu berhasil diperbarui.');
    }


    // tombol cepat buat nonaktifin atau aktifin menu dari halaman daftar
    public function toggleActive(Menu $menu)
    {
        // mau diaktifin tapi belum ada resep -> tolak.
        // dicek di server, jadi tetap aman walau ada yang kirim request langsung tanpa lewat tombol
        if (! $menu->aktif && ! $menu->resep()->exists()) {
            return back()->with('error', 'Menu belum punya resep, jadi belum bisa diaktifkan.');
        }

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

        // ---- resep ----

    public function storeRecipe(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'bahan_id' => ['required', 'exists:bahan,id'],
            // gt:0 = harus lebih dari nol, decimal:0,3 = maksimal 3 angka di belakang koma
            'takaran' => ['required', 'numeric', 'gt:0', 'decimal:0,3', 'max:99999999'],
        ]);

        // cari dulu: bahan ini udah ada di resep menu ini atau belum
        $resep = Resep::where('menu_id', $menu->id)
            ->where('bahan_id', $data['bahan_id'])
            ->first();

        if ($resep) {
            // udah ada -> takarannya aja yang diganti, jadi nggak ada bahan dobel
            $resep->update(['takaran' => $data['takaran']]);
        } else {
            // belum ada -> bikin baris baru
            Resep::create([
                'menu_id' => $menu->id,
                'bahan_id' => $data['bahan_id'],
                'takaran' => $data['takaran'],
            ]);
        }

        return back()->with('success', 'Resep disimpan.');
    }

    public function destroyRecipe(Menu $menu, Resep $resep)
    {
        // jaga-jaga: URL /menu/1/recipe/99 nggak boleh bisa ngapus resep milik menu lain
        if ($resep->menu_id !== $menu->id) {
            abort(404);
        }

        $resep->delete();

        // resep habis -> menu otomatis nonaktif, biar nggak bisa dipesan tanpa resep
        if ($menu->aktif && ! $menu->resep()->exists()) {
            $menu->update(['aktif' => false]);

            return back()->with('success', 'Bahan dihapus dari resep. Menu dinonaktifkan karena resepnya kosong.');
        }

        return back()->with('success', 'Bahan dihapus dari resep.');
    }
}