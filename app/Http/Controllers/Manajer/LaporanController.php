<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{   
    //laporan hpp dan margin per menu
    public function margin()
    {   
        //ambil semua menu sama resep dan data bahan
        $menus = Menu::with('resep.bahan')->orderBy('nama')->get();

        $items = [];
        foreach($menus as $menu) {
            $hpp = 0;
            $adaHargaNol = false;

            //hitung hpp, jumlah(takaran x hargapersatuan bahan)
            foreach ($menu->resep as $r) {
                $hargaBahan = (float) $r->bahan->harga_per_satuan;
                if ($hargaBahan <= 0){
                    $adaHargaNol = true;
                }
                $hpp += (float) $r->takaran * $hargaBahan;
            }

            $punyaResep = $menu->resep->count() > 0;
            $margin = $punyaResep ? ($menu->harga - $hpp) : 0;
            $persenMargin = ($punyaResep && $menu->harga > 0)
                ? ($margin / $menu->harga) * 100
                : 0;

            $items[] = [
                'menu' => $menu,
                'punya_resep' => $punyaResep,
                'hpp' => $hpp,
                'margin' => $margin,
                'persen_margin' => $persenMargin,
                'ada_harga_nol' => $adaHargaNol,
            ];
        }

        return view('manajer.laporan.margin', compact('items'));
    }

    //laporan penjualan per periode
    public function penjualan(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
        ]);

        // awal bulan ini sampai hari ini 
        $tanggalMulai = $request->input('tanggal_mulai', now()->startOfMonth()->toDateString());
        $tanggalSelesai = $request->input('tanggal_selesai', now()->toDateString());

        $items = DB::table('detail_pesanan')
        ->join('pesanan', 'detail_pesanan.pesanan_id', '=', 'pesanan.id')
        ->where('pesanan.status', '!=', 'dibatalkan')
        ->whereDate('pesanan.created_at', '>=', $tanggalMulai)
        ->whereDate('pesanan.created_at', '<=', $tanggalSelesai)
        ->select(
            'detail_pesanan.menu_id',
            'detail_pesanan.nama_menu',
            DB::raw('SUM(detail_pesanan.jumlah) as total_terjual'),
            DB::raw('SUM(detail_pesanan.total_baris) as total_pendapatan')
        )
        ->groupBy('detail_pesanan.menu_id', 'detail_pesanan.nama_menu')
        ->orderByDesc('total_pendapatan')
        ->get();

        $grandTotalPorsi = 0;
        $grandTotalPendapatan = 0;
        foreach($items as $row){
            $grandTotalPorsi += (int) $row->total_terjual;
            $grandTotalPendapatan += (int) $row->total_pendapatan;
        }

        return view('manajer.laporan.penjualan', compact(
            'items',
            'tanggalMulai',
            'tanggalSelesai',
            'grandTotalPorsi',
            'grandTotalPendapatan'
        ));
    }
}
