<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\Bahan;
use App\Services\SupplierService;
use Illuminate\Support\Carbon;

class SupplierController extends Controller
{   
    //dipanggil lewat tombol (sinkron harga dari supplier)
    public function sync(SupplierService $supplier)
    {
        $result = $supplier->syncPrices();

        if(! $result['ok']) {
            //kalo gagal harga lama tetep dipakai dan kasih tau tanggal nya
            $last = Bahan::max('harga_diperbarui_pada');
            $info = $last ? Carbon::parse($last)->format('d/m/Y') : 'belum pernah diperbarui';

            return back()->with('error', "Layanan Supplier tidak dapat dihubungi. Sistem memakai harga terakhir yang tersimpan (diperbarui: {$info}).");
        }

        $date = $result['date']->format('d/m/Y');

        return back()->with('success', "Harga {$result['updated']} bahan diperbarui dari Supplier (data tanggal {$date}). {$result['skipped']} bahan dilewati.");
    }
}
