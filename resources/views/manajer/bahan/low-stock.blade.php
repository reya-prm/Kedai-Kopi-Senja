@extends('layout.app')
@section('title', 'Bahan Menipis')

@section('content')
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Bahan Menipis</h1>
        <p class="mt-1 text-sm text-gray-500">Bahan yang stoknya sama dengan atau di bawah batas minimum.</p>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                <tr>
                    <th class="px-5 py-3">Nama</th>
                    <th class="px-5 py-3">Satuan</th>
                    <th class="px-5 py-3 text-right">Stok</th>
                    <th class="px-5 py-3 text-right">Batas minimum</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse ($items as $b)
                <tr class="transition-colors hover:bg-gray-50">
                    <td class="px-5 py-3 font-medium text-gray-900">{{ $b->nama }}</td>
                    <td class="px-5 py-3">{{ $b->satuan_dasar }}</td>
                    <td class="px-5 py-3 text-right font-medium tabular-nums text-red-600">{{ (float) $b->stok }}</td>
                    <td class="px-5 py-3 text-right tabular-nums">{{ (float) $b->batas_minimum }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-5 py-10 text-center text-gray-500">Semua bahan aman.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection