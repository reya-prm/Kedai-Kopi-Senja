@extends('layout.app')
@section('title', 'Menu')

@section('content')
<div class="flex flex-wrap items-end justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Menu</h1>
        <p class="mt-1 text-sm text-gray-500">Atur menu, resep, harga, dan status penjualan.</p>
    </div>
    <a href="{{ route('manajer.menu.create') }}"
       class="rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300">+ Tambah Menu</a>
</div>

<div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
    <table class="w-full text-left text-sm text-gray-600">
        <thead class="bg-gray-50 text-xs uppercase text-gray-700">
            <tr>
                <th class="px-5 py-3">Nama</th>
                <th class="px-5 py-3">Kategori</th>
                <th class="px-5 py-3 text-right">Harga</th>
                <th class="px-5 py-3">Resep</th>
                <th class="px-5 py-3">Status</th>
                <th class="px-5 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse ($items as $m)
            <tr class="transition-colors hover:bg-gray-50">
                <td class="px-5 py-3 font-medium text-gray-900">{{ $m->nama }}</td>
                <td class="px-5 py-3">{{ $m->kategori ?? '-' }}</td>
                <td class="px-5 py-3 text-right tabular-nums">Rp{{ number_format($m->harga, 0, ',', '.') }}</td>
                <td class="px-5 py-3">
                    {{-- menu tanpa resep nggak bisa dipesan, jadi dikasih tanda merah --}}
                    @if ($m->resep_count === 0)
                        <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-800">Belum ada resep</span>
                    @else
                        {{ $m->resep_count }} bahan
                    @endif
                </td>
                <td class="px-5 py-3">
                    @if ($m->aktif)
                        <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-800">Aktif</span>
                    @else
                        <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700">Nonaktif</span>
                    @endif
                </td>
                <td class="whitespace-nowrap px-5 py-3 text-right">
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
            <tr><td colspan="6" class="px-5 py-10 text-center text-gray-500">Belum ada menu.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection