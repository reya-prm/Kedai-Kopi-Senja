@extends('layout.app')
@section('title', 'Penjualan per Menu')

@section('content')
<div>
    <h1 class="text-xl font-bold">Penjualan per Menu</h1>
    <p class="text-sm text-gray-500">Hanya pesanan berstatus Selesai. Pendapatan memakai harga saat transaksi (belum termasuk service dan pajak).</p>
</div>

<form method="GET" action="{{ route('manajer.report.sales') }}" class="flex flex-wrap items-end gap-3">
    <div>
        <label class="block mb-2 text-sm font-medium text-gray-900">Dari</label>
        <input type="date" name="from" value="{{ $from }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5">
    </div>
    <div>
        <label class="block mb-2 text-sm font-medium text-gray-900">Sampai</label>
        <input type="date" name="to" value="{{ $to }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5">
    </div>
    <button class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">Tampilkan</button>
</form>

@error('from') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
@error('to') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

<div class="relative overflow-x-auto bg-white border border-gray-200 rounded-lg shadow-sm">
    <table class="w-full text-sm text-left text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
                <th class="px-6 py-3">Menu</th>
                <th class="px-6 py-3 text-right">Jumlah terjual</th>
                <th class="px-6 py-3 text-right">Pendapatan</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($rows as $r)
            <tr class="bg-white border-b border-gray-200">
                <td class="px-6 py-3 font-medium text-gray-900">{{ $r->nama }}</td>
                <td class="px-6 py-3 text-right">{{ $r->total_qty }}</td>
                <td class="px-6 py-3 text-right">Rp{{ number_format($r->total_revenue, 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr><td colspan="3" class="px-6 py-4 text-center">Belum ada penjualan (pesanan selesai) pada periode ini.</td></tr>
        @endforelse
        </tbody>
        @if ($rows->isNotEmpty())
            <tfoot>
                <tr class="font-semibold text-gray-900 bg-gray-50">
                    <td class="px-6 py-3">Total</td>
                    <td class="px-6 py-3 text-right">{{ $rows->sum('total_qty') }}</td>
                    <td class="px-6 py-3 text-right">Rp{{ number_format($rows->sum('total_revenue'), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        @endif
    </table>
</div>
@endsection