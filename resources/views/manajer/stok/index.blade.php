@extends('layout.app')
@section('title', 'Stok Masuk')

@section('content')
<form method="POST" action="{{ route('manajer.stok.store') }}"
      class="p-6 space-y-4 bg-white border border-gray-200 rounded-lg shadow-sm">
    @csrf
    <h1 class="text-xl font-bold">Catat Stok Masuk</h1>

    <div class="grid gap-4 md:grid-cols-3">
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-900">Bahan</label>
            <select name="bahan_id" required
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5">
                <option value="">Pilih bahan</option>
                @foreach ($ingredients as $b)
                    <option value="{{ $b->id }}" @selected(old('bahan_id') == $b->id)>{{ $b->nama }} ({{ $b->satuan_dasar }})</option>
                @endforeach
            </select>
            @error('bahan_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block mb-2 text-sm font-medium text-gray-900">Jumlah (satuan dasar bahan)</label>
            <input name="jumlah" type="number" step="0.001" min="0" required value="{{ old('jumlah') }}"
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5">
            @error('jumlah') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block mb-2 text-sm font-medium text-gray-900">Tanggal</label>
            {{-- default hari ini, max hari ini biar nggak bisa pilih tanggal masa depan --}}
            <input name="tanggal" type="date" required max="{{ now()->toDateString() }}"
                   value="{{ old('tanggal', now()->toDateString()) }}"
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5">
            @error('tanggal') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <button class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
        Catat
    </button>
</form>

<div class="relative overflow-x-auto bg-white border border-gray-200 rounded-lg shadow-sm">
    <table class="w-full text-sm text-left text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
                <th class="px-6 py-3">Tanggal</th>
                <th class="px-6 py-3">Bahan</th>
                <th class="px-6 py-3 text-right">Jumlah</th>
                <th class="px-6 py-3">Dicatat oleh</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($items as $r)
            <tr class="bg-white border-b border-gray-200">
                <td class="px-6 py-3">{{ $r->tanggal->format('d/m/Y') }}</td>
                <td class="px-6 py-3 font-medium text-gray-900">{{ $r->bahan->nama }}</td>
                <td class="px-6 py-3 text-right">{{ (float) $r->jumlah }} {{ $r->bahan->satuan_dasar }}</td>
                <td class="px-6 py-3">{{ $r->user->name }}</td>
            </tr>
        @empty
            <tr><td colspan="4" class="px-6 py-4 text-center">Belum ada pencatatan.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection