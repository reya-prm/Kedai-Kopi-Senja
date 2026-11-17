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

    <label class="flex items-center gap-2 text-sm text-gray-900">
        {{-- old() kosong = form baru dibuka (menu baru otomatis aktif).
             old() ada isinya = habis gagal validasi, ikutin pilihan terakhir user --}}
        <input type="checkbox" name="aktif" value="1"
               @checked(old() ? old('aktif') : ($menu->exists ? $menu->aktif : true))
               class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300">
        Menu aktif (bisa dipesan)
    </label>

    <div class="flex items-center gap-3">
        <button class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">Simpan</button>
        <a href="{{ route('manajer.menu.index') }}" class="text-sm text-gray-600 hover:underline">Kembali</a>
    </div>
</form>
@endsection