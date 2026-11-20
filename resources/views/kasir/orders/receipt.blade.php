<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk {{ $pesanan->receipt_number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 print:bg-white">
    <div class="max-w-sm p-6 mx-auto my-6 font-mono text-sm text-gray-900 bg-white border border-gray-200 rounded-lg shadow-sm print:my-0 print:border-0 print:shadow-none">

        <div class="text-center">
            <h1 class="text-lg font-bold">Kedai Kopi Senja</h1>
            <p class="text-xs text-gray-500">Struk Pembayaran</p>
        </div>

        <hr class="my-3 border-dashed">

        {{-- info pesanan --}}
        <dl class="space-y-1 text-xs">
            <div class="flex justify-between"><dt>No. Struk</dt><dd>{{ $pesanan->receipt_number }}</dd></div>
            <div class="flex justify-between"><dt>Tanggal</dt><dd>{{ $pesanan->created_at->format('d/m/Y H:i') }}</dd></div>
            <div class="flex justify-between"><dt>Meja</dt><dd>{{ $pesanan->nomor_meja }}</dd></div>
            <div class="flex justify-between"><dt>Pembayaran</dt><dd>{{ strtoupper($pesanan->metode_bayar) }}</dd></div>
            {{-- pesanan dari APK punya pelanggan; pesanan dari kasir cuma punya nama kasirnya --}}
            <div class="flex justify-between">
                <dt>{{ $pesanan->pelanggan ? 'Pelanggan' : 'Kasir' }}</dt>
                <dd>{{ $pesanan->pelanggan?->name ?? $pesanan->pembuat->name }}</dd>
            </div>
        </dl>

        @if ($pesanan->status === 'dibatalkan')
            <p class="py-1 mt-3 font-bold text-center text-red-600 border border-red-600 border-dashed">PESANAN DIBATALKAN</p>
        @endif

        <hr class="my-3 border-dashed">

        {{-- rincian item. nama dan harga dari snapshot detail_pesanan, bukan dari tabel menu,
             jadi struk lama nggak ikut berubah kalau menunya diganti harga atau namanya --}}
        @foreach ($pesanan->detail as $d)
            <div class="mb-2">
                <p>{{ $d->nama_menu }}</p>
                <div class="flex justify-between text-xs text-gray-600">
                    <span>{{ $d->jumlah }} x Rp{{ number_format($d->harga_satuan, 0, ',', '.') }}</span>
                    <span>Rp{{ number_format($d->total_baris, 0, ',', '.') }}</span>
                </div>
            </div>
        @endforeach

        <hr class="my-3 border-dashed">

        {{-- semua angka di bawah diambil dari yang tersimpan waktu transaksi, nggak dihitung ulang.
             urutannya: service dari subtotal, lalu pajak dari (subtotal + service) --}}
        <dl class="space-y-1">
            <div class="flex justify-between">
                <dt>Subtotal</dt>
                <dd>Rp{{ number_format($pesanan->subtotal, 0, ',', '.') }}</dd>
            </div>
            <div class="flex justify-between">
                <dt>Service (5%)</dt>
                <dd>Rp{{ number_format($pesanan->service, 0, ',', '.') }}</dd>
            </div>
            <div class="flex justify-between">
                <dt>Pajak (10% dari Rp{{ number_format($pesanan->subtotal + $pesanan->service, 0, ',', '.') }})</dt>
                <dd>Rp{{ number_format($pesanan->pajak, 0, ',', '.') }}</dd>
            </div>
            <div class="flex justify-between pt-2 mt-1 text-base font-bold border-t border-dashed">
                <dt>TOTAL</dt>
                <dd>Rp{{ number_format($pesanan->total, 0, ',', '.') }}</dd>
            </div>
        </dl>

        <p class="mt-4 text-xs text-center text-gray-500">Terima kasih, sampai jumpa lagi!</p>

        {{-- tombol ini nggak ikut tercetak (print:hidden) --}}
        <div class="flex gap-3 mt-5 print:hidden">
            <a href="{{ route('kasir.orders.show', $pesanan) }}"
               class="flex-1 px-4 py-2 text-sm font-medium text-center text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Kembali</a>
        </div>
    </div>
</body>
</html>