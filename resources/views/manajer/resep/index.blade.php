@extends('layout.app')
@section('title', 'Resep')

@section('content')
<div>
    <h1 class="text-xl font-bold">Resep per Menu</h1>
</div>

<div class="relative overflow-x-auto bg-white border border-gray-200 rounded-lg shadow-sm">
    <table class="w-full text-sm text-left text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
                <th class="px-6 py-3">Menu</th>
                <th class="px-6 py-3">Bahan</th>
                <th class="px-6 py-3 text-right">Takaran / porsi</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody>
        @foreach ($items as $m)
            @forelse ($m->resep as $r)
                <tr class="bg-white border-b border-gray-200">
                    @if ($loop->first)
                        {{-- sel menu cuma ditulis di baris pertama. rowspan bikin dia "melebar"
                             sepanjang jumlah bahannya, jadi satu menu = satu blok --}}
                        <td rowspan="{{ $m->resep->count() }}" class="px-6 py-3 align-top bg-gray-50 border-r border-gray-200">
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

                    <td class="px-6 py-2 text-gray-900">{{ $r->bahan->nama }}</td>
                    {{-- (float) biar "18.000" tampil jadi "18" --}}
                    <td class="px-6 py-2 text-right">{{ (float) $r->takaran }} {{ $r->bahan->satuan_dasar }}</td>
                    <td class="px-6 py-2 text-right">
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
                <tr class="bg-white border-b border-gray-200">
                    <td class="px-6 py-3 align-top bg-gray-50 border-r border-gray-200">
                        <p class="font-medium text-gray-900">{{ $m->nama }}</p>
                        <p class="text-xs">{{ $m->kategori ?? '-' }} · {{ $m->aktif ? 'Aktif' : 'Nonaktif' }}</p>
                        <a href="{{ route('manajer.menu.edit', $m) }}" class="text-xs font-medium text-blue-700 hover:underline">Isi resep</a>
                    </td>
                    <td colspan="3" class="px-6 py-3 text-red-600">Belum ada resep. Menu ini belum bisa dipesan.</td>
                </tr>
            @endforelse
        @endforeach
        </tbody>
    </table>
</div>
@endsection