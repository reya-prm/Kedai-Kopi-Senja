<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = Carbon::parse($validated['from'] ?? now()->startOfMonth()->toDateString())->startOfDay();
        $to = Carbon::parse($validated['to'] ?? now()->toDateString())->endOfDay();

        $orders = DB::table('pesanan')
            ->where('status', 'selesai')
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('COUNT(*) AS total_orders, COALESCE(SUM(total), 0) AS total_billed')
            ->first();

        $rows = DB::table('detail_pesanan')
            ->join('pesanan', 'pesanan.id', '=', 'detail_pesanan.pesanan_id')
            ->where('pesanan.status', 'selesai')
            ->whereBetween('pesanan.created_at', [$from, $to])
            ->groupBy('detail_pesanan.menu_id', 'detail_pesanan.nama_menu')
            ->selectRaw('detail_pesanan.nama_menu, SUM(detail_pesanan.jumlah) AS total_qty, SUM(detail_pesanan.total_baris) AS total_revenue')
            ->orderByDesc('total_revenue')
            ->get();

        $summary = (object) [
            'total_orders' => (int) $orders->total_orders,
            'total_billed' => (int) $orders->total_billed,
            'total_portions' => (int) $rows->sum('total_qty'),
            'total_menu_revenue' => (int) $rows->sum('total_revenue'),
        ];

        $marginItems = [];
        $menus = Menu::with('resep.bahan')->orderBy('nama')->get();

        foreach ($menus as $menu) {
            $hpp = 0;
            $adaHargaNol = false;

            foreach ($menu->resep as $resep) {
                $hargaBahan = (float) $resep->bahan->harga_per_satuan;
                if ($hargaBahan <= 0) {
                    $adaHargaNol = true;
                }

                $hpp += (float) $resep->takaran * $hargaBahan;
            }

            $punyaResep = $menu->resep->isNotEmpty();
            $margin = $punyaResep ? $menu->harga - $hpp : 0;
            $persenMargin = $punyaResep && $menu->harga > 0
                ? ($margin / $menu->harga) * 100
                : 0;

            $marginItems[] = [
                'menu' => $menu,
                'punya_resep' => $punyaResep,
                'hpp' => $hpp,
                'margin' => $margin,
                'persen_margin' => $persenMargin,
                'ada_harga_nol' => $adaHargaNol,
            ];
        }

        return view('manajer.laporan.index', [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'rows' => $rows,
            'summary' => $summary,
            'marginItems' => $marginItems,
        ]);
    }
}
