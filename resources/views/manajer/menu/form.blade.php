@extends('layout.app')
@section('title', $menu->exists ? 'Ubah Menu' : 'Tambah Menu')

@section('content')
<form method="POST"
      action="{{ $menu->exists ? route('manajer.menu.update', $menu) : route('manajer.menu.store') }}"
      class="max-w-lg p-6 space-y-4 bg-white border border-gray-200 rounded-lg shadow-sm">
    @csrf
    @if ($menu->exists) @method('PUT') @endif

    <h1 class="text-xl font-bold">{{ $menu->exists ? 'Ubah Menu' : 'Tambah Menu' }}</h1>

    <div>
        <label class="block mb-2 text-sm font-medium text-gray-900">Nama</label>
        <input name="nama" value="{{ old('nama', $menu->nama) }}" required
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5">
        @error('nama') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block mb-2 text-sm font-medium text-gray-900">Kategori (boleh kosong)</label>
        <input name="kategori" value="{{ old('kategori', $menu->kategori) }}"
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5">
        @error('kategori') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block mb-2 text-sm font-medium text-gray-900">Harga jual (Rp)</label>
        <input name="harga" type="number" min="0" required value="{{ old('harga', $menu->harga) }}"
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5">
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
<section class="max-w-lg p-6 space-y-4 bg-white border border-gray-200 rounded-lg shadow-sm">
    <h2 class="text-lg font-bold">Resep <span class="text-sm font-normal text-gray-500">(takaran per 1 porsi)</span></h2>

    <table class="w-full text-sm text-left text-gray-500">
        @forelse ($menu->resep as $r)
            <tr class="border-b border-gray-200">
                <td class="py-2 font-medium text-gray-900">{{ $r->bahan->nama }}</td>
                {{-- (float) biar "18.000" tampil jadi "18" --}}
                <td class="py-2 text-right">{{ (float) $r->takaran }} {{ $r->bahan->satuan_dasar }}</td>
                <td class="py-2 text-right">
                    <form method="POST" action="{{ route('manajer.menu.recipe.destroy', [$menu, $r]) }}">
                        @csrf
                        @method('DELETE')
                        <button class="font-medium text-red-600 hover:underline">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td class="py-2 text-red-600">Belum ada bahan. Menu tanpa resep tidak bisa diaktifkan.</td></tr>
        @endforelse
    </table>

    <form method="POST" action="{{ route('manajer.menu.recipe.store', $menu) }}" class="flex items-start gap-2">
        @csrf
        <div class="flex-1">
            <select name="bahan_id" required
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5">
                <option value="">Pilih bahan</option>
                @foreach ($ingredients as $b)
                    <option value="{{ $b->id }}">{{ $b->nama }} ({{ $b->satuan_dasar }})</option>
                @endforeach
            </select>
            @error('bahan_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <input name="takaran" type="number" step="0.001" min="0" placeholder="Takaran" required
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-28 p-2.5">
            @error('takaran') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <button class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-4 py-2.5">Simpan</button>
    </form>
    <p class="text-xs text-gray-500">Memilih bahan yang sudah ada di resep akan mengubah takarannya.</p>
</section>
@endif
@endsection