@extends('layout.app')
@section('title', 'Resep')

@section('content')
<div>
    <h1 class="text-2xl font-bold text-gray-900">Resep Menu</h1>
    <p class="mt-1 text-sm text-gray-500">Takaran bahan di bawah ini berlaku untuk satu porsi. Menu tanpa resep belum bisa diaktifkan.</p>
</div>

<div class="space-y-4">
    @forelse ($items as $m)
        <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4 p-5">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-lg font-semibold text-gray-900">{{ $m->nama }}</h2>
                        @if ($m->aktif)
                            <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-800">Aktif</span>
                        @else
                            <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700">Nonaktif</span>
                        @endif
                    </div>
                    <p class="mt-1 text-sm text-gray-500">{{ $m->kategori ?? 'Tanpa kategori' }}</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    @if ($m->resep->isNotEmpty())
                        <span class="text-sm text-gray-600">{{ $m->resep->count() }} bahan</span>
                    @endif
                    <a href="{{ route('manajer.menu.edit', $m) }}"
                       class="rounded-lg border border-blue-200 px-3 py-2 text-sm font-medium text-blue-700 hover:bg-blue-50">
                        {{ $m->resep->isEmpty() ? 'Isi resep' : 'Atur resep' }}
                    </a>
                </div>
            </div>

            @if ($m->resep->isNotEmpty())
                <div class="border-t border-gray-200">
                    <div class="flex flex-wrap items-center justify-between gap-2 bg-gray-50 px-5 py-3">
                        <h3 class="text-sm font-semibold text-gray-800">Bahan untuk satu porsi</h3>
                        <span class="text-xs text-gray-500">Takaran ditampilkan bersama satuannya</span>
                    </div>

                    <ul class="divide-y divide-gray-100">
                        @foreach ($m->resep as $r)
                            <li class="flex flex-wrap items-center justify-between gap-3 px-5 py-3">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $r->bahan->nama }}</p>
                                    <p class="mt-0.5 text-sm text-gray-600">
                                        {{ (float) $r->takaran }} {{ $r->bahan->satuan_dasar }} per porsi
                                    </p>
                                </div>
                                <form method="POST" action="{{ route('manajer.menu.recipe.destroy', [$m, $r]) }}"
                                      onsubmit="return confirm('Hapus bahan ini dari resep?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-lg px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-50">
                                        Hapus dari resep
                                    </button>
                                </form>
                            </li>
                        @endforeach
                    </ul>

                    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-200 bg-gray-50 px-5 py-4">
                        <p class="text-xs text-gray-500">Jika semua bahan dihapus, menu yang aktif akan dinonaktifkan.</p>
                        <form method="POST" action="{{ route('manajer.menu.recipe.clear', $m) }}"
                              onsubmit="return confirm('Kosongkan semua bahan dari resep ini? Menu aktif akan dinonaktifkan.')">
                            @csrf
                            @method('DELETE')
                            <button class="rounded-lg border border-red-200 px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-50">
                                Kosongkan resep
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="border-t border-amber-200 bg-amber-50 px-5 py-4">
                    <p class="font-medium text-amber-900">Resep belum memiliki bahan.</p>
                    <p class="mt-1 text-sm text-amber-800">Tambahkan bahan dan takaran per porsi agar menu dapat diaktifkan.</p>
                </div>
            @endif
        </section>
    @empty
        <div class="rounded-xl border border-gray-200 bg-white px-5 py-10 text-center shadow-sm">
            <p class="font-medium text-gray-900">Belum ada menu untuk ditampilkan.</p>
            <p class="mt-1 text-sm text-gray-500">Buat menu terlebih dahulu, lalu tambahkan bahan resepnya.</p>
        </div>
    @endforelse
</div>
@endsection
