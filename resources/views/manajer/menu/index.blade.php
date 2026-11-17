@extends('layout.app')
@section('title', 'Menu')

@section('content')
<div class="flex items-center justify-between">
    <h1 class="text-xl font-bold">Menu</h1>
    <a href="{{ route('manajer.menu.create') }}"
       class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-4 py-2">+ Tambah Menu</a>
</div>

<div class="relative overflow-x-auto bg-white border border-gray-200 rounded-lg shadow-sm">
    <table class="w-full text-sm text-left text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
                <th class="px-6 py-3">Nama</th>
                <th class="px-6 py-3">Kategori</th>
                <th class="px-6 py-3 text-right">Harga</th>
                <th class="px-6 py-3">Resep</th>
                <th class="px-6 py-3">Status</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody>
        @forelse ($items as $m)
            <tr class="bg-white border-b border-gray-200">
                <td class="px-6 py-3 font-medium text-gray-900">{{ $m->nama }}</td>
                <td class="px-6 py-3">{{ $m->kategori ?? '-' }}</td>
                <td class="px-6 py-3 text-right">Rp{{ number_format($m->harga, 0, ',', '.') }}</td>
                <td class="px-6 py-3">
                    {{-- menu tanpa resep nggak bisa dipesan, jadi dikasih tanda merah --}}
                    @if ($m->resep_count === 0)
                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Belum ada resep</span>
                    @else
                        {{ $m->resep_count }} bahan
                    @endif
                </td>
                <td class="px-6 py-3">
                    @if ($m->aktif)
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Aktif</span>
                    @else
                        <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">Nonaktif</span>
                    @endif
                </td>
                <td class="px-6 py-3 text-right whitespace-nowrap">
                    <a href="{{ route('manajer.menu.edit', $m) }}" class="font-medium text-blue-700 hover:underline">Ubah</a>

                    <form method="POST" action="{{ route('manajer.menu.toggle', $m) }}" class="inline ml-3">
                        @csrf
                        @method('PATCH')
                        <button class="font-medium text-amber-600 hover:underline">{{ $m->aktif ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                    </form>

                    <form method="POST" action="{{ route('manajer.menu.destroy', $m) }}" class="inline ml-3"
                          onsubmit="return confirm('Hapus menu ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="font-medium text-red-600 hover:underline">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-6 py-4 text-center">Belum ada menu.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection