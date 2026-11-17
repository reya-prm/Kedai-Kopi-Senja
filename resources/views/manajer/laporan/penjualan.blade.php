@extends('layout.app')

@section('title', 'Laporan Penjualan per Menu')

@section('content')
<div class="max-w-6xl mx-auto">
    {{-- Navigasi tab antar laporan Manajer --}}
    <div class="flex flex-wrap gap-2 mb-6 border-b border-gray-200 pb-3">
        <a href="{{ route('manajer.laporan.margin') }}"
           class="px-4 py-2 text-sm font-medium rounded-lg bg-white text-gray-700 border border-gray-300 hover:bg-gray-50">
            HPP & Margin Menu
        </a>
        <a href="{{ route('manajer.laporan.penjualan') }}"
           class="px-4 py-2 text-sm font-medium rounded-lg bg-blue-700 text-white">
            Penjualan per Menu
        </a>
        <a href="{{ route('manajer.bahan.low-stock') }}"
           class="px-4 py-2 text-sm font-medium rounded-lg bg-white text-gray-700 border border-gray-300 hover:bg-gray-50">
            Bahan Menipis
        </a>
    </div>

    <div class="mb-4">
        <h1 class="text-2xl font-bold text-gray-900">Laporan Penjualan per Menu</h1>
        <p class="text-sm text-gray-600 mt-1">
            Menghitung penjualan berdasarkan harga saat transaksi terjadi (tidak termasuk pesanan dibatalkan).
        </p>
    </div>

    {{-- Form Filter Periode Tanggal --}}
    <form method="GET" action="{{ route('manajer.laporan.penjualan') }}"
          class="bg-white border border-gray-200 rounded-lg p-4 mb-6 flex flex-wrap items-end gap-4">
        <div>
            <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
            <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ $tanggalMulai }}"
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900">
        </div>
        <div>
            <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
            <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ $tanggalSelesai }}"
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900">
        </div>
        <div>
            <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800">
                Tampilkan
            </button>
        </div>
    </form>

    @if ($errors->any())
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-100">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-lg overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-700">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Nama Menu</th>
                    <th class="px-4 py-3 text-right">Jumlah Terjual (Porsi)</th>
                    <th class="px-4 py-3 text-right">Total Pendapatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($items as $index => $row)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $row->nama_menu }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($row->total_terjual, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-medium">
                            Rp {{ number_format($row->total_pendapatan, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                            Belum ada transaksi penjualan pada periode {{ $tanggalMulai }} s.d. {{ $tanggalSelesai }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if (count($items) > 0)
                <tfoot class="bg-gray-50 border-t border-gray-200 font-semibold text-gray-900">
                    <tr>
                        <td colspan="2" class="px-4 py-3 text-right">Total Periode Ini</td>
                        <td class="px-4 py-3 text-right">{{ number_format($grandTotalPorsi, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format($grandTotalPendapatan, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection