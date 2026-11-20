@extends('layout.app')

@section('title', 'Laporan HPP & Margin Menu')
    
@section('content')
    <div class="max-w-6xl mx-auto">
    {{-- Navigasi tab antar laporan Manajer --}}
    <div class="flex flex-wrap gap-2 mb-6 border-b border-gray-200 pb-3">
        <a href="{{ route('manajer.laporan.margin') }}"
            class="px-4 py-2 text-sm font-medium rounded-lg bg-blue-700 text-white">
            HPP & Margin Menu
        </a>
        <a href="{{ route('manajer.laporan.penjualan') }}"
            class="px-4 py-2 text-sm font-medium rounded-lg bg-white text-gray-700 border border-gray-300 hover:bg-gray-50">
            Penjualan per Menu
        </a>
    </div>

    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Laporan HPP & Margin per Menu</h1>
            <p class="text-sm text-gray-600 mt-1">
                HPP dihitung otomatis dari takaran resep dikali harga bahan per satuan dasar terbaru.
            </p>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-700">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3">Nama Menu</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Harga Jual</th>
                    <th class="px-4 py-3 text-right">HPP</th>
                    <th class="px-4 py-3 text-right">Margin (Rp)</th>
                    <th class="px-4 py-3 text-right">Margin (%)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($items as $item)
                    @php
                        $menu = $item['menu'];
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">
                            {{ $menu->nama }}
                            @if (! $item['punya_resep'])
                                <span class="block text-xs text-amber-600 font-normal">Belum ada resep</span>
                            @elseif ($item['ada_harga_nol'])
                                <span class="block text-xs text-amber-600 font-normal">Ada bahan berharga Rp 0</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $menu->kategori ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @if ($menu->aktif)
                                <span class="px-2.5 py-0.5 text-xs font-medium rounded bg-green-100 text-green-800">Aktif</span>
                            @else
                                <span class="px-2.5 py-0.5 text-xs font-medium rounded bg-red-100 text-red-800">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-medium">
                            Rp {{ number_format($menu->harga, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if ($item['punya_resep'])
                                Rp {{ number_format($item['hpp'], 0, ',', '.') }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-semibold {{ $item['margin'] < 0 ? 'text-red-600' : 'text-green-700' }}">
                            @if ($item['punya_resep'])
                                Rp {{ number_format($item['margin'], 0, ',', '.') }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if ($item['punya_resep'])
                                {{ number_format($item['persen_margin'], 1, ',', '.') }}%
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                            Belum ada data menu.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection