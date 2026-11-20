@extends('layout.app')
@section('title', 'Daftar Menu')

@section('content')
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Daftar Menu</h1>
        <p class="mt-1 text-sm text-gray-500">Menu yang dapat dipilih untuk pesanan.</p>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                <tr>
                    <th class="px-5 py-3">Nama</th>
                    <th class="px-5 py-3">Kategori</th>
                    <th class="px-5 py-3 text-right">Harga</th>
                    <th class="px-5 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($items as $m)
                    <tr class="transition-colors hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-gray-900">{{ $m->nama }}</td>
                        <td class="px-5 py-3">{{ $m->kategori ?? '-' }}</td>
                        <td class="px-5 py-3 text-right tabular-nums">Rp{{ number_format($m->harga, 0, ',', '.') }}</td>
                        <td class="px-5 py-3">@include('kasir._menu-status', ['status' => $m->sales_status])</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-10 text-center text-gray-500">Belum ada menu.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection