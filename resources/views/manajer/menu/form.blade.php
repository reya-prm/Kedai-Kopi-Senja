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
        <h2 class="text-lg font-bold text-gray-900">Atur Resep</h2>
        <p class="mt-1 text-sm text-gray-500">Masukkan bahan dan takaran yang digunakan untuk membuat satu porsi menu ini.</p>
    </div>

    <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-blue-900">
        Satu resep dihitung untuk satu porsi. Satuan mengikuti satuan dasar bahan, misalnya gram, ml, atau pcs.
    </div>

    @if ($menu->resep->isNotEmpty())
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                    <tr>
                        <th class="px-4 py-3">Bahan</th>
                        <th class="px-4 py-3 text-right">Takaran per porsi</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                @foreach ($menu->resep as $r)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $r->bahan->nama }}</td>
                        {{-- (float) biar "18.000" tampil jadi "18" --}}
                        <td class="px-4 py-3 text-right tabular-nums">{{ (float) $r->takaran }} {{ $r->bahan->satuan_dasar }}</td>
                        <td class="px-4 py-3 text-right">
                            <form method="POST" action="{{ route('manajer.menu.recipe.destroy', [$menu, $r]) }}">
                                @csrf
                                @method('DELETE')
                                <button class="font-medium text-red-700 hover:underline">Hapus dari resep</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
            <p class="font-medium text-amber-900">Belum ada bahan dalam resep.</p>
            <p class="mt-1 text-sm text-amber-800">Tambahkan setidaknya satu bahan agar menu bisa diaktifkan.</p>
        </div>
    @endif

    <form method="POST" action="{{ route('manajer.menu.recipe.store', $menu) }}" class="grid gap-4 rounded-lg border border-gray-200 p-4 sm:grid-cols-[minmax(0,1fr)_10rem_auto] sm:items-end">
        @csrf
        <div>
            <label for="bahan_id" class="mb-1.5 block text-sm font-medium text-gray-900">Pilih bahan</label>
            <select name="bahan_id" required
                    id="bahan_id"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-600 focus:ring-blue-600">
                <option value="">Pilih bahan</option>
                @foreach ($ingredients as $b)
                    <option value="{{ $b->id }}">{{ $b->nama }} ({{ $b->satuan_dasar }})</option>
                @endforeach
            </select>
            @error('bahan_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="takaran" class="mb-1.5 block text-sm font-medium text-gray-900">Takaran per porsi</label>
            <input id="takaran" name="takaran" type="number" step="0.001" min="0" placeholder="Contoh: 18" required
                   class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-600 focus:ring-blue-600">
            @error('takaran') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <button class="rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-800">Simpan bahan</button>
    </form>
    <p class="-mt-3 text-xs text-gray-500">Jika bahan sudah ada di resep, menyimpan ulang akan memperbarui takarannya, bukan membuat baris baru.</p>
</section>
@endif
@endsection