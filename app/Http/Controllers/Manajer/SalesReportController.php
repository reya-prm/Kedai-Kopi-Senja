<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        // default periode: awal bulan ini sampai hari ini
        $from = Carbon::parse($request->input('from', now()->startOfMonth()->toDateString()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()->toDateString()))->endOfDay();

        $rows = DB::table('detail_pesanan')
            ->join('pesanan', 'pesanan.id', '=', 'detail_pesanan.pesanan_id')
            ->join('menu', 'menu.id', '=', 'detail_pesanan.menu_id')
            // cuma pesanan yang udah SELESAI yang dihitung (batal, baru, dan diproses nggak ikut)
            ->where('pesanan.status', 'selesai')
            ->whereBetween('pesanan.created_at', [$from, $to])
            ->groupBy('detail_pesanan.menu_id', 'menu.nama')
            // total_baris = harga saat transaksi x jumlah (snapshot), jadi nggak ikut berubah kalau harga menu diubah
            ->selectRaw('menu.nama, SUM(detail_pesanan.jumlah) AS total_qty, SUM(detail_pesanan.total_baris) AS total_revenue')
            ->orderByDesc('total_revenue')
            ->get();

        return view('manajer.report.sales', [
            'rows' => $rows,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
        ]);
    }
}