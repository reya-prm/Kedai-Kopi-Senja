@extends('layout.app')
@section('title', 'Pesanan #'.$pesanan->id)

@section('content')
<div class="mx-auto max-w-4xl space-y-5 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Pesanan #{{ $pesanan->id }}</h1>
        @include('kasir.orders._status', ['status' => $pesanan->status])
    </div>

    <p class="rounded-lg bg-gray-50 p-3 text-sm text-gray-600">
        {{ $pesanan->created_at->format('d/m/Y H:i') }} · Meja {{ $pesanan->nomor_meja }} ·
        {{ strtoupper($pesanan->metode_bayar) }} · Dibuat oleh {{ $pesanan->pembuat->name }}
    </p>

    <div class="overflow-x-auto">
    <table class="w-full text-left text-sm text-gray-600">
        <thead class="bg-gray-50 text-xs uppercase text-gray-700">
            <tr>
                <th class="px-4 py-3">Menu</th>
                <th class="px-4 py-3 text-right">Harga</th>
                <th class="px-4 py-3 text-right">Jumlah</th>
                <th class="px-4 py-3 text-right">Total</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($pesanan->detail as $d)
            <tr class="border-b border-gray-100">
                {{-- nama dan harga dari snapshot, bukan dari tabel menu --}}
                <td class="px-4 py-3 font-medium text-gray-900">{{ $d->nama_menu }}</td>
                <td class="px-4 py-3 text-right tabular-nums">Rp{{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-right tabular-nums">{{ $d->jumlah }}</td>
                <td class="px-4 py-3 text-right tabular-nums">Rp{{ number_format($d->total_baris, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>

    <dl class="ml-auto w-full max-w-sm space-y-2 rounded-lg bg-gray-50 p-4 text-right text-sm">
        <div class="flex justify-between"><dt>Subtotal</dt><dd>Rp{{ number_format($pesanan->subtotal, 0, ',', '.') }}</dd></div>
        <div class="flex justify-between"><dt>Service (5%)</dt><dd>Rp{{ number_format($pesanan->service, 0, ',', '.') }}</dd></div>
        <div class="flex justify-between"><dt>Pajak (10%)</dt><dd>Rp{{ number_format($pesanan->pajak, 0, ',', '.') }}</dd></div>
        <div class="flex justify-between pt-1 font-bold text-gray-900 border-t"><dt>Total</dt><dd>Rp{{ number_format($pesanan->total, 0, ',', '.') }}</dd></div>
    </dl>

    {{-- tombol yang tampil cuma langkah yang valid. servernya tetap ngecek ulang --}}
    @if ($pesanan->status === 'baru')
        <div class="flex gap-3">
            <form method="POST" action="{{ route('kasir.orders.status', $pesanan) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="diproses">
                <button class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800">Proses</button>
            </form>

            <form method="POST" action="{{ route('kasir.orders.cancel', $pesanan) }}"
                onsubmit="return confirm('Batalkan pesanan ini? Stok akan dikembalikan.')">
                @csrf
                @method('PATCH')
                <button class="rounded-lg bg-red-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-red-700">Batalkan</button>
            </form>
        </div>
    @elseif ($pesanan->status === 'diproses')
        <form method="POST" action="{{ route('kasir.orders.status', $pesanan) }}">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="selesai">
            <button class="rounded-lg bg-green-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-green-800">Selesai</button>
        </form>
    @elseif ($pesanan->status === 'selesai')
        <a href="{{ route('kasir.orders.receipt', $pesanan) }}" target="_self"
        class="inline-block rounded-lg border border-blue-700 px-5 py-2.5 text-sm font-medium text-blue-700 hover:bg-blue-50">
        Lihat Struk
    </a>    
    @endif

    <a href="{{ route('kasir.orders.index') }}" class="text-sm text-gray-600 hover:underline">Kembali ke daftar</a>
</div>
@endsection