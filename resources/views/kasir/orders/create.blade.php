@extends('layout.app')
@section('title', 'Buat Pesanan')

@section('content')
<form method="POST" action="{{ route('kasir.orders.store') }}" class="space-y-5">
    @csrf
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Buat Pesanan</h1>
        <p class="mt-1 text-sm text-gray-500">Pilih jumlah menu, nomor meja, dan metode pembayaran.</p>
    </div>

    @error('items')
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">{{ $message }}</div>
    @enderror

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                <tr>
                    <th class="px-5 py-3">Menu</th>
                    <th class="px-5 py-3 text-right">Harga</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Jumlah</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @foreach ($items as $m)
                <tr class="transition-colors hover:bg-gray-50">
                    <td class="px-5 py-3 font-medium text-gray-900">{{ $m->nama }}</td>
                    <td class="px-5 py-3 text-right tabular-nums">Rp{{ number_format($m->harga, 0, ',', '.') }}</td>
                    <td class="px-5 py-3">@include('kasir._menu-status', ['status' => $m->sales_status])</td>
                    <td class="px-5 py-3">
                        {{-- menu yang nggak tersedia inputnya dimatiin (nggak ikut terkirim).
                             servernya tetap ngecek ulang, jadi aman walau ada yang akal-akalan --}}
                        <input type="number" name="items[{{ $m->id }}]" min="0" max="99"
                               value="{{ old('items.'.$m->id, 0) }}" @disabled($m->sales_status !== 'tersedia')
                               class="w-24 rounded-lg border border-gray-300 bg-gray-50 p-2 text-sm text-gray-900 focus:border-blue-600 focus:ring-blue-600 disabled:cursor-not-allowed disabled:opacity-40">
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="grid max-w-2xl gap-4 rounded-xl border border-gray-200 bg-white p-5 shadow-sm md:grid-cols-2">
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-900">Nomor meja</label>
            <select name="table_number" required
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-600 focus:ring-blue-600">
                <option value="">Pilih meja</option>
                @for ($i = 1; $i <= 10; $i++)
                    <option value="{{ $i }}" @selected(old('table_number') == $i)>{{ $i }}</option>
                @endfor
            </select>
            @error('table_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block mb-2 text-sm font-medium text-gray-900">Metode bayar</label>
            <select name="payment_method" required
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-600 focus:ring-blue-600">
                <option value="">Pilih metode</option>
                <option value="tunai" @selected(old('payment_method') === 'tunai')>Tunai</option>
                <option value="qris" @selected(old('payment_method') === 'qris')>QRIS</option>
            </select>
            @error('payment_method') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <button class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300">
        Buat Pesanan
    </button>
</form>
@endsection