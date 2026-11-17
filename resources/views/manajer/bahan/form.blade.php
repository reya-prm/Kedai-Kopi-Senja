@extends('layout.app')
@section('title', $bahan->exists ? 'Ubah Bahan' : 'Tambah Bahan')

@section('content')
{{-- $bahan->exists = true kalau lagi ubah data lama, false kalau lagi tambah baru --}}
<form method="POST"
      action="{{ $bahan->exists ? route('manajer.bahan.update', $bahan) : route('manajer.bahan.store') }}"
      class="max-w-lg p-6 space-y-4 bg-white border border-gray-200 rounded-lg shadow-sm">
    @csrf
    {{-- browser cuma ngerti GET dan POST, jadi PUT "dipalsuin" lewat field tersembunyi ini --}}
    @if ($bahan->exists) @method('PUT') @endif

    <h1 class="text-xl font-bold">{{ $bahan->exists ? 'Ubah Bahan' : 'Tambah Bahan' }}</h1>
    
    <div>
        <label class="block mb-2 text-sm font-medium text-gray-900">Nama</label>
        {{-- old() = isi lama yang diketik user kalau validasi gagal, biar nggak ngetik ulang --}}
        <input name="nama" value="{{ old('nama', $bahan->nama) }}" required
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5">
        @error('nama') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block mb-2 text-sm font-medium text-gray-900">Satuan dasar</label>
        @if ($bahan->exists)
            {{-- satuan dikunci pas ubah, cuma ditampilin --}}
            <p class="p-2.5 text-sm text-gray-900 bg-gray-100 border border-gray-200 rounded-lg">
                {{ $bahan->satuan_dasar }} <span class="text-gray-500">(tidak bisa diubah)</span>
            </p>
        @else
            <select name="satuan_dasar" required
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5">
                <option value="">Pilih satuan</option>
                @foreach (['g', 'ml', 'pcs'] as $s)
                    <option value="{{ $s }}" @selected(old('satuan_dasar') === $s)>{{ $s }}</option>
                @endforeach
            </select>
            @error('satuan_dasar') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        @endif
    </div>

    <div>
        <label class="block mb-2 text-sm font-medium text-gray-900">Batas minimum</label>
        <input name="batas_minimum" type="number" step="0.001" min="0" required
               value="{{ old('batas_minimum', (float) $bahan->batas_minimum) }}"
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5">
        @error('batas_minimum') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center gap-3">
        <button class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">Simpan</button>
        <a href="{{ route('manajer.bahan.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
    </div>
</form>
@endsection