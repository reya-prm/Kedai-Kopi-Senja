@extends('layout.app')
@section('title', 'Buat Pesanan')

@section('content')
<form method="POST" action="{{ route('kasir.orders.store') }}" class="space-y-4">
    @csrf
    <h1 class="text-xl font-bold">Buat Pesanan</h1>

    @error('items') <div class="p-4 text-sm text-red-800 rounded-lg bg-red-50">{{ $message }}</div> @enderror

    <div class="relative overflow-x-auto bg-white border border-gray-200 rounded-lg shadow-sm">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-6 py-3">Menu</th>
                    <th class="px-6 py-3 text-right">Harga</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Jumlah</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($items as $m)
                <tr class="bg-white border-b border-gray-200">
                    <td class="px-6 py-3 font-medium text-gray-900">{{ $m->nama }}</td>
                    <td class="px-6 py-3 text-right">Rp{{ number_format($m->harga, 0, ',', '.') }}</td>
                    <td class="px-6 py-3">@include('kasir._menu-status', ['status' => $m->sales_status])</td>
                    <td class="px-6 py-3">
                        {{-- menu yang nggak tersedia inputnya dimatiin (nggak ikut terkirim).
                             servernya tetap ngecek ulang, jadi aman walau ada yang akal-akalan --}}
                        <input type="number" name="items[{{ $m->id }}]" min="0" max="99"
                               value="{{ old('items.'.$m->id, 0) }}" @disabled($m->sales_status !== 'tersedia')
                               class="w-20 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2 disabled:opacity-40">
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="grid max-w-lg gap-4 md:grid-cols-2">
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-900">Nomor meja</label>
            <select name="table_number" required
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
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
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                <option value="">Pilih metode</option>
                <option value="tunai" @selected(old('payment_method') === 'tunai')>Tunai</option>
                <option value="qris" @selected(old('payment_method') === 'qris')>QRIS</option>
            </select>
            @error('payment_method') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <button class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
        Buat Pesanan
    </button>
</form>
@endsection