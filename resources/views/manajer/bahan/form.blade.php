@extends('layout.app')
@section('title', $bahan->exists ? 'Ubah Bahan' : 'Tambah Bahan')

@section('content')
{{-- $bahan->exists = true kalau lagi ubah data lama, false kalau lagi tambah baru --}}
<form method="POST"
      action="{{ $bahan->exists ? route('manajer.bahan.update', $bahan) : route('manajer.bahan.store') }}"
      class="max-w-xl space-y-5 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
    @csrf
    {{-- browser cuma ngerti GET dan POST, jadi PUT "dipalsuin" lewat field tersembunyi ini --}}
    @if ($bahan->exists) @method('PUT') @endif

    <div>
        <h1 class="text-2xl font-bold text-gray-900">{{ $bahan->exists ? 'Ubah Bahan' : 'Tambah Bahan' }}</h1>
        <p class="mt-1 text-sm text-gray-500">Satuan dasar bahan tidak dapat diubah setelah disimpan.</p>
    </div>

    <div>
        <label class="block mb-2 text-sm font-medium text-gray-900">Nama</label>
        {{-- old() = isi lama yang diketik user kalau validasi gagal, biar nggak ngetik ulang --}}
        <input name="nama" value="{{ old('nama', $bahan->nama) }}" required
               class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-600 focus:ring-blue-600">
        @error('nama') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block mb-2 text-sm font-medium text-gray-900">Satuan dasar</label>
        @if ($bahan->exists)
            {{-- satuan dikunci pas ubah, cuma ditampilin --}}
            <p class="rounded-lg border border-gray-200 bg-gray-100 p-2.5 text-sm text-gray-900">
                {{ $bahan->satuan_dasar }} <span class="text-gray-500">(tidak bisa diubah)</span>
            </p>
        @else
            <select name="satuan_dasar" required
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-600 focus:ring-blue-600">
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
               class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-600 focus:ring-blue-600">
        @error('batas_minimum') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center gap-3">
        <button class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300">Simpan</button>
        <a href="{{ route('manajer.bahan.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
    </div>
</form>
@endsection