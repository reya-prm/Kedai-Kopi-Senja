@extends('layout.app')
@section('title', 'Penyesuaian Stok')

@section('content')
<div class="space-y-5">
    <div>
        <p class="text-sm font-medium text-blue-700">Kelola Stok</p>
        <h1 class="mt-1 text-2xl font-bold text-slate-900">Penyesuaian Stok</h1>
        <p class="mt-1 text-sm text-slate-600">Catat hasil stock opname atau stok bahan yang rusak.</p>
    </div>

    <nav aria-label="Navigasi kelola stok" class="inline-flex flex-wrap gap-2 rounded-xl border border-slate-200 bg-white p-2 shadow-sm">
        <a href="{{ route('manajer.stok.index') }}" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-blue-700">Stok Masuk</a>
        <a href="{{ route('manajer.penyesuaian-stok.index') }}" aria-current="page" class="rounded-lg bg-blue-50 px-4 py-2 text-sm font-medium text-blue-800">Penyesuaian Stok</a>
    </nav>

    <div class="grid gap-3 md:grid-cols-2">
        <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">
            <h2 class="text-sm font-semibold text-blue-950">Stock opname</h2>
            <p class="mt-1 text-sm leading-5 text-blue-800">Masukkan jumlah fisik hasil hitung. Stok sistem akan disesuaikan ke jumlah tersebut.</p>
        </div>
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
            <h2 class="text-sm font-semibold text-amber-950">Barang rusak</h2>
            <p class="mt-1 text-sm leading-5 text-amber-800">Masukkan jumlah bahan rusak yang dibuang. Jumlah tersebut akan dikurangi dari stok.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('manajer.penyesuaian-stok.store') }}" class="space-y-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        @csrf

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="bahan_id" class="mb-2 block text-sm font-medium text-slate-800">Bahan</label>
                <select
                    id="bahan_id"
                    name="bahan_id"
                    required
                    class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                >
                    <option value="">Pilih bahan</option>
                    @foreach ($ingredients as $bahan)
                        <option value="{{ $bahan->id }}" @selected(old('bahan_id') == $bahan->id)>
                            {{ $bahan->nama }} — stok {{ (float) $bahan->stok }} {{ $bahan->satuan_dasar }}
                        </option>
                    @endforeach
                </select>
                @error('bahan_id')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="jenis" class="mb-2 block text-sm font-medium text-slate-800">Jenis penyesuaian</label>
                <select
                    id="jenis"
                    name="jenis"
                    required
                    class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                >
                    <option value="">Pilih jenis</option>
                    <option value="opname" @selected(old('jenis') === 'opname')>Stock opname — jumlah fisik aktual</option>
                    <option value="rusak" @selected(old('jenis') === 'rusak')>Barang rusak — jumlah yang dibuang</option>
                </select>
                @error('jenis')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="jumlah" class="mb-2 block text-sm font-medium text-slate-800">Jumlah (satuan dasar)</label>
                <input
                    id="jumlah"
                    name="jumlah"
                    type="number"
                    min="0"
                    max="99999999"
                    step="0.001"
                    value="{{ old('jumlah') }}"
                    required
                    class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                >
                <p class="mt-1.5 text-xs text-slate-500">Opname boleh diisi 0 jika bahan habis. Jumlah rusak harus lebih dari 0 dan tidak boleh melebihi stok.</p>
                @error('jumlah')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="catatan" class="mb-2 block text-sm font-medium text-slate-800">Catatan <span class="font-normal text-slate-500">(opsional)</span></label>
                <input
                    id="catatan"
                    name="catatan"
                    type="text"
                    maxlength="255"
                    value="{{ old('catatan') }}"
                    placeholder="Contoh: hasil hitung rak penyimpanan"
                    class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                >
                @error('catatan')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <button type="submit" class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-200">
            Simpan penyesuaian
        </button>
    </form>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4">
            <h2 class="font-semibold text-slate-900">Riwayat penyesuaian</h2>
            <p class="mt-1 text-sm text-slate-500">Menampilkan hingga 50 pencatatan terbaru.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase text-slate-700">
                    <tr>
                        <th class="px-5 py-3">Waktu</th>
                        <th class="px-5 py-3">Bahan</th>
                        <th class="px-5 py-3">Jenis</th>
                        <th class="px-5 py-3 text-right">Jumlah</th>
                        <th class="px-5 py-3">Stok sebelum → sesudah</th>
                        <th class="px-5 py-3">Catatan</th>
                        <th class="px-5 py-3">Dicatat oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($items as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="whitespace-nowrap px-5 py-3">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-5 py-3 font-medium text-slate-900">{{ $item->bahan->nama }}</td>
                            <td class="px-5 py-3">
                                @if ($item->jenis === 'opname')
                                    <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-800">Stock opname</span>
                                @else
                                    <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-800">Barang rusak</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-3 text-right tabular-nums">
                                {{ number_format((float) $item->jumlah, 3, ',', '.') }} {{ $item->bahan->satuan_dasar }}
                            </td>
                            <td class="whitespace-nowrap px-5 py-3 tabular-nums">
                                {{ number_format((float) $item->stok_sebelum, 3, ',', '.') }}
                                <span class="mx-1 text-slate-400">→</span>
                                {{ number_format((float) $item->stok_sesudah, 3, ',', '.') }}
                                {{ $item->bahan->satuan_dasar }}
                            </td>
                            <td class="px-5 py-3">{{ $item->catatan ?: '—' }}</td>
                            <td class="whitespace-nowrap px-5 py-3">{{ $item->user->name }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-slate-500">Belum ada penyesuaian stok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
