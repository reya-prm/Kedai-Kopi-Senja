@extends('layout.app')
@section('title', 'Kelola Stok')

@section('content')
<div class="space-y-5">
<div>
    <h1 class="text-2xl font-bold text-gray-900">Kelola Stok</h1>
    <p class="mt-1 text-sm text-gray-500">Catat stok masuk, stock opname, atau bahan rusak.</p>
</div>

<nav aria-label="Navigasi kelola stok" class="inline-flex flex-wrap gap-2 rounded-xl border border-gray-200 bg-white p-2 shadow-sm">
    <a href="{{ route('manajer.stok.index') }}" aria-current="page" class="rounded-lg bg-blue-50 px-4 py-2 text-sm font-medium text-blue-800">Stok Masuk</a>
    <a href="{{ route('manajer.penyesuaian-stok.index') }}" class="rounded-lg px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-blue-700">Penyesuaian Stok</a>
</nav>

<form method="POST" action="{{ route('manajer.stok.store') }}"
      class="space-y-5 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
    @csrf
    <div>
        <h2 class="text-xl font-bold text-gray-900">Catat Stok Masuk</h2>
        <p class="mt-1 text-sm text-gray-500">Stok akan bertambah sesuai jumlah yang dicatat.</p>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-900">Bahan</label>
            <select name="bahan_id" required
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-600 focus:ring-blue-600">
                <option value="">Pilih bahan</option>
                @foreach ($ingredients as $b)
                    <option value="{{ $b->id }}" @selected(old('bahan_id') == $b->id)>{{ $b->nama }} ({{ $b->satuan_dasar }})</option>
                @endforeach
            </select>
            @error('bahan_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block mb-2 text-sm font-medium text-gray-900">Jumlah (satuan dasar bahan)</label>
            <input name="jumlah" type="number" step="0.001" min="0" required value="{{ old('jumlah') }}"
                   class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-600 focus:ring-blue-600">
            @error('jumlah') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block mb-2 text-sm font-medium text-gray-900">Tanggal</label>
            {{-- default hari ini, max hari ini biar nggak bisa pilih tanggal masa depan --}}
            <input name="tanggal" type="date" required max="{{ now()->toDateString() }}"
                   value="{{ old('tanggal', now()->toDateString()) }}"
                   class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-600 focus:ring-blue-600">
            @error('tanggal') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <button class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300">
        Catat
    </button>
</form>

<section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-5 py-4">
        <h2 class="font-semibold text-gray-900">Riwayat Stok Masuk</h2>
        <p class="mt-1 text-sm text-gray-500">Menampilkan hingga 50 pencatatan terbaru.</p>
    </div>
    <div class="overflow-x-auto">
    <table class="w-full text-left text-sm text-gray-600">
        <thead class="bg-gray-50 text-xs uppercase text-gray-700">
            <tr>
                <th class="px-5 py-3">Tanggal</th>
                <th class="px-5 py-3">Bahan</th>
                <th class="px-5 py-3 text-right">Jumlah</th>
                <th class="px-5 py-3">Dicatat oleh</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse ($items as $r)
            <tr class="transition-colors hover:bg-gray-50">
                <td class="px-5 py-3">{{ $r->tanggal->format('d/m/Y') }}</td>
                <td class="px-5 py-3 font-medium text-gray-900">{{ $r->bahan->nama }}</td>
                <td class="px-5 py-3 text-right tabular-nums">{{ (float) $r->jumlah }} {{ $r->bahan->satuan_dasar }}</td>
                <td class="px-5 py-3">{{ $r->user->name }}</td>
            </tr>
        @empty
            <tr><td colspan="4" class="px-5 py-10 text-center text-gray-500">Belum ada pencatatan.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
</section>
</div>
@endsection