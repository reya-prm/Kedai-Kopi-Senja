@extends('layout.app')

@section('title', 'Laporan Penjualan per Menu')

@section('content')
<div class="mx-auto max-w-6xl space-y-5">
    {{-- Navigasi tab antar laporan Manajer --}}
    <div class="flex flex-wrap gap-2 border-b border-gray-200 pb-3">
        <a href="{{ route('manajer.laporan.margin') }}"
           class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
            HPP & Margin Menu
        </a>
        <a href="{{ route('manajer.laporan.penjualan') }}"
           class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-medium text-white">
            Penjualan per Menu
        </a>
    </div>

    <div>
        <h1 class="text-2xl font-bold text-gray-900">Laporan Penjualan per Menu</h1>
        <p class="mt-1 text-sm text-gray-600">
            Menghitung penjualan berdasarkan harga saat transaksi terjadi (tidak termasuk pesanan dibatalkan).
        </p>
    </div>

    {{-- Form Filter Periode Tanggal --}}
    <form method="GET" action="{{ route('manajer.laporan.penjualan') }}"
          class="flex flex-wrap items-end gap-4 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <div>
            <label for="tanggal_mulai" class="mb-1 block text-sm font-medium text-gray-700">Tanggal Mulai</label>
            <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ $tanggalMulai }}"
                   class="rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900 focus:border-blue-600 focus:ring-blue-600">
        </div>
        <div>
            <label for="tanggal_selesai" class="mb-1 block text-sm font-medium text-gray-700">Tanggal Selesai</label>
            <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ $tanggalSelesai }}"
                   class="rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900 focus:border-blue-600 focus:ring-blue-600">
        </div>
        <div>
            <button type="submit"
                    class="rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300">
                Tampilkan
            </button>
        </div>
    </form>

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm text-gray-700">
            <thead class="border-b border-gray-200 bg-gray-50 text-xs uppercase text-gray-700">
                <tr>
                    <th class="px-5 py-3">No</th>
                    <th class="px-5 py-3">Nama Menu</th>
                    <th class="px-5 py-3 text-right">Jumlah Terjual (Porsi)</th>
                    <th class="px-5 py-3 text-right">Total Pendapatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($items as $index => $row)
                    <tr class="transition-colors hover:bg-gray-50">
                        <td class="px-5 py-3 text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-5 py-3 font-medium text-gray-900">{{ $row->nama_menu }}</td>
                        <td class="px-5 py-3 text-right tabular-nums">{{ number_format($row->total_terjual, 0, ',', '.') }}</td>
                        <td class="px-5 py-3 text-right font-medium tabular-nums">
                            Rp {{ number_format($row->total_pendapatan, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center text-gray-500">
                            Belum ada transaksi penjualan pada periode {{ $tanggalMulai }} s.d. {{ $tanggalSelesai }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if (count($items) > 0)
                <tfoot class="border-t border-gray-200 bg-gray-50 font-semibold text-gray-900">
                    <tr>
                        <td colspan="2" class="px-5 py-3 text-right">Total Periode Ini</td>
                        <td class="px-5 py-3 text-right tabular-nums">{{ number_format($grandTotalPorsi, 0, ',', '.') }}</td>
                        <td class="px-5 py-3 text-right tabular-nums">Rp {{ number_format($grandTotalPendapatan, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection