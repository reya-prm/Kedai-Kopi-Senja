@extends('layout.app')
@section('title', 'Bahan')

@section('content')
<div class="flex items-center justify-between">
    <h1 class="text-xl font-bold">Bahan</h1>
    <a href="{{ route('manajer.bahan.create') }}"
       class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-4 py-2">+ Tambah Bahan</a>
</div>

<div class="relative overflow-x-auto bg-white border border-gray-200 rounded-lg shadow-sm">
    <table class="w-full text-sm text-left text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
                <th class="px-6 py-3">Nama</th>
                <th class="px-6 py-3">Satuan</th>
                <th class="px-6 py-3 text-right">Stok</th>
                <th class="px-6 py-3 text-right">Batas minimum</th>
                <th class="px-6 py-3">Status</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($items as $b)
            <tr class="bg-white border-b border-gray-200">
                <td class="px-6 py-3 font-medium text-gray-900">{{ $b->nama }}</td>
                <td class="px-6 py-3">{{ $b->satuan_dasar }}</td>
                {{-- (float) dipakai biar "300.000" tampil jadi "300" --}}
                <td class="px-6 py-3 text-right">{{ (float) $b->stok }}</td>
                <td class="px-6 py-3 text-right">{{ (float) $b->batas_minimum }}</td>
                <td class="px-6 py-3">
                    
                    @if ($b->is_low_stock)
                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Menipis</span>
                    @endif
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Aman</span>
                </td>
                <td class="px-6 py-3 text-right whitespace-nowrap">
                    <a href="{{ route('manajer.bahan.edit', $b) }}" class="font-medium text-blue-700 hover:underline">Ubah</a>
                    <form method="POST" action="{{ route('manajer.bahan.destroy', $b) }}" class="inline ml-3"
                        onsubmit="return confirm('Hapus bahan ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="font-medium text-red-600 hover:underline">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-6 py-4 text-center">Belum ada bahan.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection