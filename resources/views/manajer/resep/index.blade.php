@extends('layout.app')
@section('title', 'Resep')

@section('content')
<div>
    <h1 class="text-2xl font-bold text-gray-900">Resep per Menu</h1>
    <p class="mt-1 text-sm text-gray-500">Komposisi bahan dan takaran untuk setiap menu.</p>
</div>

<div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
    <table class="w-full text-left text-sm text-gray-600">
        <thead class="bg-gray-50 text-xs uppercase text-gray-700">
            <tr>
                <th class="px-5 py-3">Menu</th>
                <th class="px-5 py-3">Bahan</th>
                <th class="px-5 py-3 text-right">Takaran / porsi</th>
                <th class="px-5 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @foreach ($items as $m)
            @forelse ($m->resep as $r)
                <tr class="transition-colors hover:bg-gray-50">
                    @if ($loop->first)
                        {{-- sel menu cuma ditulis di baris pertama. rowspan bikin dia "melebar"
                             sepanjang jumlah bahannya, jadi satu menu = satu blok --}}
                        <td rowspan="{{ $m->resep->count() }}" class="border-r border-gray-200 bg-gray-50 px-5 py-3 align-top">
                            <p class="font-medium text-gray-900">{{ $m->nama }}</p>
                            <p class="text-xs">{{ $m->kategori ?? '-' }} · {{ $m->aktif ? 'Aktif' : 'Nonaktif' }}</p>
                            <div class="flex items-center gap-3 mt-1 text-xs">
                                <a href="{{ route('manajer.menu.edit', $m) }}" class="font-medium text-blue-700 hover:underline">Ubah resep</a>
                                <form method="POST" action="{{ route('manajer.menu.recipe.clear', $m) }}"
                                      onsubmit="return confirm('Hapus SEMUA bahan di resep menu ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="font-medium text-red-600 hover:underline">Hapus resep</button>
                                </form>
                            </div>
                        </td>
                    @endif

                    <td class="px-5 py-2 text-gray-900">{{ $r->bahan->nama }}</td>
                    {{-- (float) biar "18.000" tampil jadi "18" --}}
                    <td class="px-5 py-2 text-right tabular-nums">{{ (float) $r->takaran }} {{ $r->bahan->satuan_dasar }}</td>
                    <td class="px-5 py-2 text-right">
                        <form method="POST" action="{{ route('manajer.menu.recipe.destroy', [$m, $r]) }}"
                              onsubmit="return confirm('Hapus bahan ini dari resep?')">
                            @csrf
                            @method('DELETE')
                            <button class="font-medium text-red-600 hover:underline">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                {{-- menu tanpa resep tetap tampil, biar manajer tau mana yang belum diisi --}}
                <tr class="transition-colors hover:bg-gray-50">
                    <td class="border-r border-gray-200 bg-gray-50 px-5 py-3 align-top">
                        <p class="font-medium text-gray-900">{{ $m->nama }}</p>
                        <p class="text-xs">{{ $m->kategori ?? '-' }} · {{ $m->aktif ? 'Aktif' : 'Nonaktif' }}</p>
                        <a href="{{ route('manajer.menu.edit', $m) }}" class="text-xs font-medium text-blue-700 hover:underline">Isi resep</a>
                    </td>
                    <td colspan="3" class="px-5 py-3 text-amber-700">Belum ada resep. Menu ini belum bisa dipesan.</td>
                </tr>
            @endforelse
        @endforeach
        </tbody>
    </table>
</div>
@endsection