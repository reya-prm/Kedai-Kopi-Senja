@extends('layout.app')
@section('title', 'Penjualan per Menu')

@section('content')
<div>
    <h1 class="text-2xl font-bold text-gray-900">Penjualan per Menu</h1>
    <p class="mt-1 text-sm text-gray-500">Hanya pesanan berstatus Selesai. Pendapatan memakai harga saat transaksi (belum termasuk service dan pajak).</p>
</div>

<form method="GET" action="{{ route('manajer.report.sales') }}" class="flex flex-wrap items-end gap-4 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
    <div>
        <label class="mb-1 block text-sm font-medium text-gray-900">Dari</label>
        <input type="date" name="from" value="{{ $from }}" class="rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-600 focus:ring-blue-600">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-gray-900">Sampai</label>
        <input type="date" name="to" value="{{ $to }}" class="rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-600 focus:ring-blue-600">
    </div>
    <button class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300">Tampilkan</button>
</form>

@error('from') <p class="text-sm text-red-600" role="alert">{{ $message }}</p> @enderror
@error('to') <p class="text-sm text-red-600" role="alert">{{ $message }}</p> @enderror

<div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
    <table class="w-full text-left text-sm text-gray-600">
        <thead class="bg-gray-50 text-xs uppercase text-gray-700">
            <tr>
                <th class="px-5 py-3">Menu</th>
                <th class="px-5 py-3 text-right">Jumlah terjual</th>
                <th class="px-5 py-3 text-right">Pendapatan</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse ($rows as $r)
            <tr class="transition-colors hover:bg-gray-50">
                <td class="px-5 py-3 font-medium text-gray-900">{{ $r->nama }}</td>
                <td class="px-5 py-3 text-right tabular-nums">{{ $r->total_qty }}</td>
                <td class="px-5 py-3 text-right tabular-nums">Rp{{ number_format($r->total_revenue, 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr><td colspan="3" class="px-5 py-10 text-center text-gray-500">Belum ada penjualan (pesanan selesai) pada periode ini.</td></tr>
        @endforelse
        </tbody>
        @if ($rows->isNotEmpty())
            <tfoot>
                <tr class="border-t border-gray-200 bg-gray-50 font-semibold text-gray-900">
                    <td class="px-5 py-3">Total</td>
                    <td class="px-5 py-3 text-right tabular-nums">{{ $rows->sum('total_qty') }}</td>
                    <td class="px-5 py-3 text-right tabular-nums">Rp{{ number_format($rows->sum('total_revenue'), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        @endif
    </table>
</div>
@endsection