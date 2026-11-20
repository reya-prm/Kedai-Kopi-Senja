@extends('layout.app')
@section('title', 'Bahan')

@section('content')
<div class="flex flex-wrap items-end justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Bahan</h1>
        <p class="mt-1 text-sm text-gray-500">Kelola stok, harga, dan batas minimum bahan.</p>
    </div>
    <a href="{{ route('manajer.bahan.create') }}"
       class="rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300">
        + Tambah Bahan
    </a>
</div>

@include('manajer._supplier-info')

<div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
    <table class="w-full text-left text-sm text-gray-600">
        <thead class="bg-gray-50 text-xs uppercase text-gray-700">
            <tr>
                <th class="px-5 py-3">Nama</th>
                <th class="px-5 py-3">Satuan</th>
                <th class="px-5 py-3 text-right">Stok</th>
                <th class="px-5 py-3 text-right">Harga/satuan</th>
                <th class="px-5 py-3 text-right">Batas minimum</th>
                <th class="px-5 py-3">Status</th>
                <th class="px-5 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse ($items as $b)
            <tr class="transition-colors hover:bg-gray-50">
                <td class="px-5 py-3 font-medium text-gray-900">{{ $b->nama }}</td>
                <td class="px-5 py-3">{{ $b->satuan_dasar }}</td>
                {{-- (float) dipakai biar "300.000" tampil jadi "300" --}}
                <td class="px-5 py-3 text-right tabular-nums">{{ (float) $b->stok }}</td>
                {{-- harga per satuan dasar: per g, per ml, atau per pcs sesuai kolom Satuan --}}
                <td class="px-5 py-3 text-right tabular-nums">Rp{{ (float) $b->harga_per_satuan }}</td>
                <td class="px-5 py-3 text-right tabular-nums">{{ (float) $b->batas_minimum }}</td>
                <td class="px-5 py-3">
                {{-- is_low_stock datang dari accessor di model Bahan --}}
                    @if ($b->is_low_stock)
                        <span class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-800">Menipis</span>
                    @else
                        <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-800">Aman</span>
                    @endif
                </td>
                <td class="whitespace-nowrap px-5 py-3 text-right">
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
            <tr><td colspan="7" class="px-5 py-10 text-center text-gray-500">Belum ada bahan.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection