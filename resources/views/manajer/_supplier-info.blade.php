@php
    //buat ambil tanggal terbaru kapan harganya update di db, disimpen disini biar ga repot di controller.
    $lastUpdate = \App\Models\Bahan::max('harga_diperbarui_pada');
@endphp

<div class="flex flex-wrap items-center justify-between gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
    <div>
        <p class="text-sm font-medium text-gray-900">Sinkronisasi Harga Supplier</p>
        <p class="mt-1 text-sm text-gray-500">
            Terakhir diperbarui:
            <strong class="font-medium text-gray-700">
                {{ $lastUpdate ? \Illuminate\Support\Carbon::parse($lastUpdate)->format('d/m/Y') : 'belum pernah' }}
            </strong>
        </p>
    </div>

    <form method="POST" action="{{ route('manajer.supplier.sync') }}">
        @csrf
        <button class="rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300">
            Sinkron Harga dari Supplier
        </button>
    </form>
</div>