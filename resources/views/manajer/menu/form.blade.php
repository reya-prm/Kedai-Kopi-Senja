@extends('layout.app')
@section('title', $menu->exists ? 'Ubah Menu' : 'Tambah Menu')

@section('content')
<form method="POST"
      action="{{ $menu->exists ? route('manajer.menu.update', $menu) : route('manajer.menu.store') }}"
      class="max-w-2xl space-y-5 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
    @csrf
    @if ($menu->exists) @method('PUT') @endif

    <div>
        <h1 class="text-2xl font-bold text-gray-900">{{ $menu->exists ? 'Ubah Menu' : 'Tambah Menu' }}</h1>
        <p class="mt-1 text-sm text-gray-500">Atur informasi dan harga jual menu.</p>
    </div>

    <div>
        <label class="block mb-2 text-sm font-medium text-gray-900">Nama</label>
        <input name="nama" value="{{ old('nama', $menu->nama) }}" required
               class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-600 focus:ring-blue-600">
        @error('nama') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block mb-2 text-sm font-medium text-gray-900">Kategori (boleh kosong)</label>
        <input name="kategori" value="{{ old('kategori', $menu->kategori) }}"
               class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-600 focus:ring-blue-600">
        @error('kategori') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block mb-2 text-sm font-medium text-gray-900">Harga jual (Rp)</label>
        <input name="harga" type="number" min="0" required value="{{ old('harga', $menu->harga) }}"
               class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-600 focus:ring-blue-600">
        @error('harga') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- status nggak diubah lewat form ini, cuma info --}}
    @if ($menu->exists)
        <p class="text-sm text-gray-500">
            Status: <strong>{{ $menu->aktif ? 'Aktif' : 'Nonaktif' }}</strong>
            (diubah lewat tombol di daftar menu)
        </p>
    @else
        <p class="text-sm text-gray-500">Menu baru otomatis nonaktif. Isi resep dulu, lalu aktifkan dari daftar menu.</p>
    @endif

    <div class="flex items-center gap-3">
        <button class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">Simpan</button>
        <a href="{{ route('manajer.menu.index') }}" class="text-sm text-gray-600 hover:underline">Kembali</a>
    </div>
</form>
{{-- bagian resep cuma muncul kalau menunya udah tersimpan (udah punya id) --}}
@if ($menu->exists)
<section class="mt-5 max-w-2xl space-y-5 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
    <div>
        <h2 class="text-lg font-bold text-gray-900">Resep</h2>
        <p class="mt-1 text-sm text-gray-500">Takaran bahan untuk satu porsi.</p>
    </div>

    <div class="overflow-x-auto">
    <table class="w-full text-left text-sm text-gray-600">
        <thead class="bg-gray-50 text-xs uppercase text-gray-700">
            <tr>
                <th class="px-3 py-2">Bahan</th>
                <th class="px-3 py-2 text-right">Takaran</th>
                <th class="px-3 py-2 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse ($menu->resep as $r)
            <tr class="hover:bg-gray-50">
                <td class="px-3 py-2 font-medium text-gray-900">{{ $r->bahan->nama }}</td>
                {{-- (float) biar "18.000" tampil jadi "18" --}}
                <td class="px-3 py-2 text-right tabular-nums">{{ (float) $r->takaran }} {{ $r->bahan->satuan_dasar }}</td>
                <td class="px-3 py-2 text-right">
                    <form method="POST" action="{{ route('manajer.menu.recipe.destroy', [$menu, $r]) }}">
                        @csrf
                        @method('DELETE')
                        <button class="font-medium text-red-600 hover:underline">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="3" class="px-3 py-6 text-center text-amber-700">Belum ada bahan. Menu tanpa resep tidak bisa diaktifkan.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>

    <form method="POST" action="{{ route('manajer.menu.recipe.store', $menu) }}" class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_9rem_auto] sm:items-start">
        @csrf
        <div>
            <select name="bahan_id" required
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-600 focus:ring-blue-600">
                <option value="">Pilih bahan</option>
                @foreach ($ingredients as $b)
                    <option value="{{ $b->id }}">{{ $b->nama }} ({{ $b->satuan_dasar }})</option>
                @endforeach
            </select>
            @error('bahan_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <input name="takaran" type="number" step="0.001" min="0" placeholder="Takaran" required
                   class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-600 focus:ring-blue-600">
            @error('takaran') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <button class="rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-800">Simpan</button>
    </form>
    <p class="text-xs text-gray-500">Memilih bahan yang sudah ada di resep akan mengubah takarannya.</p>
</section>
@endif
@endsection