@extends('layout.app')
@section('tittle', 'Daftar Menu')

@section('content')
    <h1 class="text-xl font-bold">Daftar Menu</h1>

    <div class="relative overflow-x-auto bg-white border border-gray-200 rounded-lg shadow-sm">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-6 py-3">Nama</th>
                    <th class="px-6 py-3">Kategori</th>
                    <th class="px-6 py-3 text-right">Harga</th>
                    <th class="px-6 py-3">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $m)
                    <tr class="bg-white border-b border-gray-200">
                        <td class="px-6 py-3 font-medium text-gray-900">{{ $m->nama }}</td>
                        <td class="px-6 py-3">{{ $m->kategori ?? '-' }}</td>
                        <td class="px-6 py-3 text-right">Rp{{ number_format($m->harga, 0, ',', '.') }}</td>
                        <td class="px-6 py-3">@include('kasir._menu-status', ['status' => $m->sales_status])</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-4 text-center">Belum ada menu.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection