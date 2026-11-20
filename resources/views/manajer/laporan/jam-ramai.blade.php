@extends('layout.app')

@section('title', 'Laporan Jam Ramai')

@section('content')
<div class="space-y-5">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-sm font-medium text-blue-700">Laporan penjualan</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900">Jam Ramai</h1>
            <p class="mt-1 text-sm text-slate-600">Pesanan selesai dikelompokkan berdasarkan jam dibuat. Jam mengikuti zona waktu aplikasi (APP_TIMEZONE).</p>
        </div>
        <a href="{{ route('manajer.laporan.index') }}" class="text-sm font-medium text-blue-700 hover:text-blue-900">
            Kembali ke laporan penjualan
        </a>
    </div>

    <form method="GET" action="{{ route('manajer.laporan.jam-ramai') }}" class="flex flex-wrap items-end gap-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <div>
            <label for="from" class="mb-1 block text-sm font-medium text-slate-700">Dari tanggal</label>
            <input id="from" type="date" name="from" value="{{ $from }}" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100">
            @error('from')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="to" class="mb-1 block text-sm font-medium text-slate-700">Sampai tanggal</label>
            <input id="to" type="date" name="to" value="{{ $to }}" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100">
            @error('to')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit" class="rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-200">
            Tampilkan
        </button>
    </form>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Pesanan selesai</p>
            <p class="mt-2 text-2xl font-bold tabular-nums text-slate-900">{{ number_format($summary['total_orders'], 0, ',', '.') }}</p>
        </article>
        <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Porsi terjual</p>
            <p class="mt-2 text-2xl font-bold tabular-nums text-slate-900">{{ number_format($summary['total_portions'], 0, ',', '.') }}</p>
        </article>
        <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Total tagihan</p>
            <p class="mt-2 text-2xl font-bold tabular-nums text-slate-900">Rp{{ number_format($summary['total_billed'], 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-slate-500">Termasuk service dan pajak</p>
        </article>
        <article class="rounded-xl border border-blue-200 bg-blue-50 p-5">
            <p class="text-sm text-blue-800">Jam tersibuk</p>
            @if ($peakHour)
                <p class="mt-2 text-2xl font-bold tabular-nums text-blue-950">{{ sprintf('%02d:00', $peakHour['hour']) }}</p>
                <p class="mt-1 text-xs text-blue-800">{{ number_format($peakHour['order_count'], 0, ',', '.') }} pesanan selesai</p>
            @else
                <p class="mt-2 text-lg font-semibold text-blue-950">Belum ada data</p>
                <p class="mt-1 text-xs text-blue-800">Tidak ada pesanan selesai pada periode ini.</p>
            @endif
        </article>
    </div>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4">
            <h2 class="font-semibold text-slate-900">Rincian per jam</h2>
            <p class="mt-1 text-sm text-slate-500">Grafik membandingkan jumlah pesanan selesai pada tiap jam. Periode mengikuti tanggal pesanan dibuat.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-2xl text-left text-sm text-slate-700">
                <thead class="bg-slate-50 text-xs uppercase text-slate-600">
                    <tr>
                        <th class="px-5 py-3">Jam</th>
                        <th class="px-5 py-3">Pesanan</th>
                        <th class="px-5 py-3">Aktivitas</th>
                        <th class="px-5 py-3 text-right">Porsi</th>
                        <th class="px-5 py-3 text-right">Total tagihan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($rows as $row)
                        <tr @class(['bg-blue-50/60' => $peakHour && $row['hour'] === $peakHour['hour']])>
                            <td class="whitespace-nowrap px-5 py-3 font-medium text-slate-900">{{ sprintf('%02d:00–%02d:59', $row['hour'], $row['hour']) }}</td>
                            <td class="px-5 py-3 tabular-nums">{{ number_format($row['order_count'], 0, ',', '.') }}</td>
                            <td class="w-1/3 px-5 py-3">
                                <div class="h-2 overflow-hidden rounded-full bg-slate-100" role="img" aria-label="{{ $row['order_count'] }} pesanan">
                                    <div class="h-full rounded-full bg-blue-600" style="width: {{ $row['bar_percent'] }}%"></div>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-right tabular-nums">{{ number_format($row['portion_count'], 0, ',', '.') }}</td>
                            <td class="whitespace-nowrap px-5 py-3 text-right tabular-nums">Rp{{ number_format($row['billed_total'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
