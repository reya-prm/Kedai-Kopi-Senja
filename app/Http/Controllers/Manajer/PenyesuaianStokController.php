<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\Bahan;
use App\Models\PenyesuaianStok;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PenyesuaianStokController extends Controller
{
    public function index(): View
    {
        return view('manajer.stok.penyesuaian', [
            'ingredients' => Bahan::orderBy('nama')->get(),
            'items' => PenyesuaianStok::with(['bahan', 'user'])->latest()->limit(50)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bahan_id' => ['required', 'integer', 'exists:bahan,id'],
            'jenis' => ['required', 'in:opname,rusak'],
            'jumlah' => ['required', 'numeric', 'min:0', 'decimal:0,3', 'max:99999999'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ]);

        $jumlah = round((float) $data['jumlah'], 3);

        if ($data['jenis'] === 'rusak' && $jumlah <= 0) {
            throw ValidationException::withMessages([
                'jumlah' => 'Jumlah barang rusak harus lebih dari nol.',
            ]);
        }

        $record = DB::transaction(function () use ($data, $jumlah, $request): PenyesuaianStok {
            $bahan = Bahan::query()->lockForUpdate()->findOrFail($data['bahan_id']);
            $stokSebelum = round((float) $bahan->stok, 3);

            if ($data['jenis'] === 'rusak' && $jumlah > $stokSebelum) {
                throw ValidationException::withMessages([
                    'jumlah' => "Jumlah rusak melebihi stok tersedia ({$stokSebelum} {$bahan->satuan_dasar}).",
                ]);
            }

            $stokSesudah = $data['jenis'] === 'opname'
                ? $jumlah
                : round($stokSebelum - $jumlah, 3);

            $bahan->update(['stok' => $stokSesudah]);

            return PenyesuaianStok::create([
                'bahan_id' => $bahan->id,
                'user_id' => $request->user()->id,
                'jenis' => $data['jenis'],
                'jumlah' => $jumlah,
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSesudah,
                'catatan' => $data['catatan'] ?? null,
            ]);
        });

        $pesan = $record->jenis === 'opname'
            ? "Stok {$record->bahan->nama} disesuaikan ke {$record->stok_sesudah} {$record->bahan->satuan_dasar}."
            : "Barang rusak dicatat. Stok {$record->bahan->nama} sekarang {$record->stok_sesudah} {$record->bahan->satuan_dasar}.";

        return redirect()->route('manajer.penyesuaian-stok.index')->with('success', $pesan);
    }
}
