@extends('layout.app')
@section('title', 'Pesanan #'.$pesanan->id)

@section('content')
<div class="max-w-2xl p-6 space-y-4 bg-white border border-gray-200 rounded-lg shadow-sm">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold">Pesanan #{{ $pesanan->id }}</h1>
        @include('kasir.orders._status', ['status' => $pesanan->status])
    </div>

    <p class="text-sm text-gray-500">
        {{ $pesanan->created_at->format('d/m/Y H:i') }} · Meja {{ $pesanan->nomor_meja }} ·
        {{ strtoupper($pesanan->metode_bayar) }} · Dibuat oleh {{ $pesanan->pembuat->name }}
    </p>

    <table class="w-full text-sm text-left text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
                <th class="px-3 py-2">Menu</th>
                <th class="px-3 py-2 text-right">Harga</th>
                <th class="px-3 py-2 text-right">Jumlah</th>
                <th class="px-3 py-2 text-right">Total</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($pesanan->detail as $d)
            <tr class="border-b border-gray-200">
                {{-- nama dan harga dari snapshot, bukan dari tabel menu --}}
                <td class="px-3 py-2 font-medium text-gray-900">{{ $d->nama_menu }}</td>
                <td class="px-3 py-2 text-right">Rp{{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                <td class="px-3 py-2 text-right">{{ $d->jumlah }}</td>
                <td class="px-3 py-2 text-right">Rp{{ number_format($d->total_baris, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <dl class="ml-auto space-y-1 text-sm text-right w-60">
        <div class="flex justify-between"><dt>Subtotal</dt><dd>Rp{{ number_format($pesanan->subtotal, 0, ',', '.') }}</dd></div>
        <div class="flex justify-between"><dt>Service (5%)</dt><dd>Rp{{ number_format($pesanan->service, 0, ',', '.') }}</dd></div>
        <div class="flex justify-between"><dt>Pajak (10%)</dt><dd>Rp{{ number_format($pesanan->pajak, 0, ',', '.') }}</dd></div>
        <div class="flex justify-between pt-1 font-bold text-gray-900 border-t"><dt>Total</dt><dd>Rp{{ number_format($pesanan->total, 0, ',', '.') }}</dd></div>
    </dl>

    {{-- tombol aksi status ditambah di fitur K3 --}}

    <a href="{{ route('kasir.orders.index') }}" class="text-sm text-gray-600 hover:underline">Kembali ke daftar</a>
</div>
@endsection