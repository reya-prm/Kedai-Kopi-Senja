@php
    //buat ambil tanggal terbaru kapan harganya update di db, disimpen disini biar ga repot di controller.
    $lastUpdate = \App\Models\Bahan::max('harga_diperbarui_pada');
@endphp

<div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
    <p class="text-sm text-gray-500">
        Harga bahan terakhir diperbarui:
        <strong class="text-gray-500">
            {{ $lastUpdate ? \Illuminate\Support\Carbon::parse($lastUpdate)->format('d/m/Y') : 'belum pernah' }}
        </strong>
    </p>

    <form method="POST" action="{{ route('manajer.supplier.sync') }}">
        @csrf
        <button class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2">
            Sinkron Harga dari Supplier
        </button>
    </form>
</div>