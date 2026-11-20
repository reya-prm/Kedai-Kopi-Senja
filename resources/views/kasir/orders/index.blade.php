@extends('layout.app')
@section('title', 'Pesanan')

@section('content')
<div class="flex flex-wrap items-end justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Pesanan</h1>
        <p class="mt-1 text-sm text-gray-500">Daftar 50 pesanan terbaru.</p>
    </div>
    <a href="{{ route('kasir.orders.create') }}"
       class="rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300">+ Buat Pesanan</a>
</div>

<div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
    <table class="w-full text-left text-sm text-gray-600">
        <thead class="bg-gray-50 text-xs uppercase text-gray-700">
            <tr>
                <th class="px-5 py-3">No</th>
                <th class="px-5 py-3">Waktu</th>
                <th class="px-5 py-3">Meja</th>
                <th class="px-5 py-3">Bayar</th>
                <th class="px-5 py-3">Status</th>
                <th class="px-5 py-3 text-right">Total</th>
                <th class="px-5 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse ($items as $p)
            <tr class="transition-colors hover:bg-gray-50">
                <td class="px-5 py-3 font-medium text-gray-900">#{{ $p->id }}</td>
                <td class="px-5 py-3">{{ $p->created_at->format('d/m/Y H:i') }}</td>
                <td class="px-5 py-3">{{ $p->nomor_meja }}</td>
                <td class="px-5 py-3">{{ strtoupper($p->metode_bayar) }}</td>
                <td class="px-5 py-3">@include('kasir.orders._status', ['status' => $p->status])</td>
                <td class="px-5 py-3 text-right tabular-nums">Rp{{ number_format($p->total, 0, ',', '.') }}</td>
                <td class="px-5 py-3 text-right">
                    <a href="{{ route('kasir.orders.show', $p) }}" class="font-medium text-blue-700 hover:underline">Detail</a>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="px-5 py-10 text-center text-gray-500">Belum ada pesanan.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection