@extends('layout.app')
@section('title', 'Pesanan')

@section('content')
<div class="flex items-center justify-between">
    <h1 class="text-xl font-bold">Pesanan</h1>
    <a href="{{ route('kasir.orders.create') }}"
       class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-4 py-2">+ Buat Pesanan</a>
</div>

<div class="relative overflow-x-auto bg-white border border-gray-200 rounded-lg shadow-sm">
    <table class="w-full text-sm text-left text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
                <th class="px-6 py-3">No</th>
                <th class="px-6 py-3">Waktu</th>
                <th class="px-6 py-3">Meja</th>
                <th class="px-6 py-3">Bayar</th>
                <th class="px-6 py-3">Status</th>
                <th class="px-6 py-3 text-right">Total</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody>
        @forelse ($items as $p)
            <tr class="bg-white border-b border-gray-200">
                <td class="px-6 py-3 font-medium text-gray-900">#{{ $p->id }}</td>
                <td class="px-6 py-3">{{ $p->created_at->format('d/m/Y H:i') }}</td>
                <td class="px-6 py-3">{{ $p->nomor_meja }}</td>
                <td class="px-6 py-3">{{ strtoupper($p->metode_bayar) }}</td>
                <td class="px-6 py-3">@include('kasir.orders._status', ['status' => $p->status])</td>
                <td class="px-6 py-3 text-right">Rp{{ number_format($p->total, 0, ',', '.') }}</td>
                <td class="px-6 py-3 text-right">
                    <a href="{{ route('kasir.orders.show', $p) }}" class="font-medium text-blue-700 hover:underline">Detail</a>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="px-6 py-4 text-center">Belum ada pesanan.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection