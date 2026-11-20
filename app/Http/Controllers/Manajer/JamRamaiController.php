<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use LogicException;

class JamRamaiController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = Carbon::parse($validated['from'] ?? now()->startOfMonth()->toDateString())->startOfDay();
        $to = Carbon::parse($validated['to'] ?? now()->toDateString())->endOfDay();
        $orderHour = $this->hourExpression('created_at');
        $detailHour = $this->hourExpression('pesanan.created_at');

        $ordersByHour = DB::table('pesanan')
            ->where('status', 'selesai')
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw("{$orderHour} AS hour, COUNT(*) AS order_count, COALESCE(SUM(total), 0) AS billed_total")
            ->groupByRaw($orderHour)
            ->get()
            ->keyBy(fn (object $row): int => (int) $row->hour);

        $portionsByHour = DB::table('detail_pesanan')
            ->join('pesanan', 'pesanan.id', '=', 'detail_pesanan.pesanan_id')
            ->where('pesanan.status', 'selesai')
            ->whereBetween('pesanan.created_at', [$from, $to])
            ->selectRaw("{$detailHour} AS hour, COALESCE(SUM(detail_pesanan.jumlah), 0) AS portion_count")
            ->groupByRaw($detailHour)
            ->get()
            ->keyBy(fn (object $row): int => (int) $row->hour);

        $rows = collect(range(0, 23))->map(function (int $hour) use ($ordersByHour, $portionsByHour): array {
            $orders = $ordersByHour->get($hour);
            $portions = $portionsByHour->get($hour);

            return [
                'hour' => $hour,
                'order_count' => (int) ($orders->order_count ?? 0),
                'portion_count' => (int) ($portions->portion_count ?? 0),
                'billed_total' => (int) ($orders->billed_total ?? 0),
            ];
        });

        $maxOrders = (int) $rows->max('order_count');
        $rows = $rows->map(function (array $row) use ($maxOrders): array {
            $row['bar_percent'] = $maxOrders > 0
                ? (int) round($row['order_count'] / $maxOrders * 100)
                : 0;

            return $row;
        });

        $peakHour = $maxOrders > 0 ? $rows->firstWhere('order_count', $maxOrders) : null;
        $summary = [
            'total_orders' => (int) $rows->sum('order_count'),
            'total_portions' => (int) $rows->sum('portion_count'),
            'total_billed' => (int) $rows->sum('billed_total'),
        ];

        return view('manajer.laporan.jam-ramai', [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'rows' => $rows,
            'summary' => $summary,
            'peakHour' => $peakHour,
        ]);
    }

    private function hourExpression(string $column): string
    {
        return match (DB::connection()->getDriverName()) {
            'mysql', 'mariadb' => "HOUR({$column})",
            'sqlite' => "CAST(strftime('%H', {$column}) AS INTEGER)",
            'pgsql' => "CAST(EXTRACT(HOUR FROM {$column}) AS INTEGER)",
            'sqlsrv' => "DATEPART(HOUR, {$column})",
            default => throw new LogicException('Driver database tidak didukung untuk laporan jam ramai.'),
        };
    }
}
