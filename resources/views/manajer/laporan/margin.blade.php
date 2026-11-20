@extends('layout.app')

@section('title', 'Laporan HPP & Margin Menu')

@section('content')
    <div class="mx-auto max-w-6xl space-y-5">
    {{-- Navigasi tab antar laporan Manajer --}}
    <div class="flex flex-wrap gap-2 border-b border-gray-200 pb-3">
        <a href="{{ route('manajer.laporan.margin') }}"
            class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-medium text-white">
            HPP & Margin Menu
        </a>
        <a href="{{ route('manajer.laporan.penjualan') }}"
            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
            Penjualan per Menu
        </a>
    </div>

    <div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Laporan HPP & Margin per Menu</h1>
            <p class="mt-1 text-sm text-gray-600">
                HPP dihitung otomatis dari takaran resep dikali harga bahan per satuan dasar terbaru.
            </p>
        </div>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm text-gray-700">
            <thead class="border-b border-gray-200 bg-gray-50 text-xs uppercase text-gray-700">
                <tr>
                    <th class="px-5 py-3">Nama Menu</th>
                    <th class="px-5 py-3">Kategori</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Harga Jual</th>
                    <th class="px-5 py-3 text-right">HPP</th>
                    <th class="px-5 py-3 text-right">Margin (Rp)</th>
                    <th class="px-5 py-3 text-right">Margin (%)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($items as $item)
                    @php
                        $menu = $item['menu'];
                    @endphp
                    <tr class="transition-colors hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-gray-900">
                            {{ $menu->nama }}
                            @if (! $item['punya_resep'])
                                <span class="block text-xs text-amber-600 font-normal">Belum ada resep</span>
                            @elseif ($item['ada_harga_nol'])
                                <span class="block text-xs text-amber-600 font-normal">Ada bahan berharga Rp 0</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">{{ $menu->kategori ?? '-' }}</td>
                        <td class="px-5 py-3">
                            @if ($menu->aktif)
                                <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-800">Aktif</span>
                            @else
                                <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right font-medium tabular-nums">
                            Rp {{ number_format($menu->harga, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-3 text-right tabular-nums">
                            @if ($item['punya_resep'])
                                Rp {{ number_format($item['hpp'], 0, ',', '.') }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right font-semibold tabular-nums {{ $item['margin'] < 0 ? 'text-red-600' : 'text-green-700' }}">
                            @if ($item['punya_resep'])
                                Rp {{ number_format($item['margin'], 0, ',', '.') }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right tabular-nums">
                            @if ($item['punya_resep'])
                                {{ number_format($item['persen_margin'], 1, ',', '.') }}%
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-gray-500">
                            Belum ada data menu.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection