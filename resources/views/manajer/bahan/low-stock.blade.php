@extends('layout.app')
@section('title', 'Bahan Menipis')

@section('content')
    <div>
        <h1 class="text-x1 font-bold">Bahan Menipis</h1>
        <p class="text-sm text-gray-500">Bahan yang stoknya sama dengan atau di bawah batas minimum.</p>
    </div>

    <div class="relative overflow-x-auto bg-white border border-gray-200 rounded-lg shadow-sm">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-6 py-3">Nama</th>
                    <th class="px-6 py-3">Satuan</th>
                    <th class="px-6 py-3 text-right">Stok</th>
                    <th class="px-6 py-3 text-right">Batas minimum</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($items as $b)
                <tr class="bg-white border-b border-gray-200">
                    <td class="px-6 py-3 font-medium text-gray-900">{{ $b->nama }}</td>
                    <td class="px-6 py-3">{{ $b->satuan_dasar }}</td>
                    <td class="px-6 py-3 text-right font-medium text-red-600">{{ (float) $b->stok }}</td>
                    <td class="px-6 py-3 text-right">{{ (float) $b->batas_minimum }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-6 py-4 text-center">Semua bahan aman.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection