@extends('layout.app')

@section('title', 'Laporan Keseluruhan')

@section('content')
<div class="space-y-5">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Laporan Keseluruhan</h1>
        <p class="mt-1 text-sm text-gray-600">
            Ringkasan penjualan untuk pesanan.
        </p>
    </div>

    <form method="GET" action="{{ route('manajer.laporan.index') }}"
          class="flex flex-wrap items-end gap-4 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <div>
            <label for="from" class="mb-1 block text-sm font-medium text-gray-700">Dari tanggal</label>
            <input id="from" type="date" name="from" value="{{ $from }}"
                   class="rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900 focus:border-blue-600 focus:ring-blue-600">
            @error('from') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="to" class="mb-1 block text-sm font-medium text-gray-700">Sampai tanggal</label>
            <input id="to" type="date" name="to" value="{{ $to }}"
                   class="rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900 focus:border-blue-600 focus:ring-blue-600">
            @error('to') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <button type="submit"
                class="rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300">
            Tampilkan Laporan
        </button>
    </form>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-xl border border-blue-200 bg-blue-50 p-5">
            <p class="text-sm font-medium text-blue-800">Pendapatan Menu</p>
            <p class="mt-2 text-2xl font-bold tabular-nums text-blue-950">Rp {{ number_format($summary->total_menu_revenue, 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-blue-800">Belum termasuk service dan pajak</p>
        </article>
        <article class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-600">Total Pendapatan</p>
            <p class="mt-2 text-2xl font-bold tabular-nums text-gray-900">Rp {{ number_format($summary->total_billed, 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-gray-500">Termasuk service dan pajak</p>
        </article>
        <article class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-600">Pesanan Selesai</p>
            <p class="mt-2 text-2xl font-bold tabular-nums text-gray-900">{{ number_format($summary->total_orders, 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-gray-500">Jumlah transaksi selesai</p>
        </article>
        <article class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-600">Porsi Terjual</p>
            <p class="mt-2 text-2xl font-bold tabular-nums text-gray-900">{{ number_format($summary->total_portions, 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-gray-500">Total menu pada pesanan selesai</p>
        </article>
    </div>

    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 px-5 py-4">
            <h2 class="font-semibold text-gray-900">Rincian Penjualan per Menu</h2>
            <p class="mt-1 text-sm text-gray-500">Pendapatan memakai harga saat transaksi dan tidak termasuk service atau pajak.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700">
                <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                    <tr>
                        <th class="px-5 py-3">Menu</th>
                        <th class="px-5 py-3 text-right">Porsi terjual</th>
                        <th class="px-5 py-3 text-right">Pendapatan menu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($rows as $row)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $row->nama_menu }}</td>
                            <td class="px-5 py-3 text-right tabular-nums">{{ number_format($row->total_qty, 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-right tabular-nums">Rp {{ number_format($row->total_revenue, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-10 text-center text-gray-500">
                                Belum ada pesanan selesai pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($rows->isNotEmpty())
                    <tfoot class="border-t border-gray-200 bg-gray-50 font-semibold text-gray-900">
                        <tr>
                            <td class="px-5 py-3">Total</td>
                            <td class="px-5 py-3 text-right tabular-nums">{{ number_format($summary->total_portions, 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-right tabular-nums">Rp {{ number_format($summary->total_menu_revenue, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </section>

    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 px-5 py-4">
            <h2 class="font-semibold text-gray-900">HPP dan Margin Menu Saat Ini</h2>
            <p class="mt-1 text-sm text-gray-500">Dihitung per porsi dari resep dan harga bahan terbaru, bukan dari periode penjualan di atas.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700">
                <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                    <tr>
                        <th class="px-5 py-3">Menu</th>
                        <th class="px-5 py-3 text-right">Harga jual</th>
                        <th class="px-5 py-3 text-right">HPP per porsi</th>
                        <th class="px-5 py-3 text-right">Margin per porsi</th>
                        <th class="px-5 py-3 text-right">Margin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($marginItems as $item)
                        @php($menu = $item['menu'])
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 font-medium text-gray-900">
                                {{ $menu->nama }}
                                @if (! $item['punya_resep'])
                                    <span class="mt-1 block text-xs font-normal text-amber-700">Belum ada resep</span>
                                @elseif ($item['ada_harga_nol'])
                                    <span class="mt-1 block text-xs font-normal text-amber-700">Ada bahan dengan harga Rp 0</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right tabular-nums">Rp {{ number_format($menu->harga, 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-right tabular-nums">
                                {{ $item['punya_resep'] ? 'Rp ' . number_format($item['hpp'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-5 py-3 text-right tabular-nums {{ $item['margin'] < 0 ? 'text-red-600' : 'text-green-700' }}">
                                {{ $item['punya_resep'] ? 'Rp ' . number_format($item['margin'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-5 py-3 text-right tabular-nums">
                                {{ $item['punya_resep'] ? number_format($item['persen_margin'], 1, ',', '.') . '%' : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-gray-500">Belum ada data menu.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
