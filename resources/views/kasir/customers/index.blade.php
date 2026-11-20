@extends('layout.app')
@section('title', 'Pelanggan')

@section('content')
<div>
    <h1 class="text-2xl font-bold text-gray-900">Pelanggan</h1>
    <p class="mt-1 text-sm text-gray-500">Cari akun pelanggan dan bantu reset password.</p>
</div>

<form method="GET" action="{{ route('kasir.customers.index') }}" class="flex max-w-xl gap-2 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nomor HP"
           class="min-w-0 flex-1 rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-600 focus:ring-blue-600">
    <button class="rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-800">Cari</button>
</form>

<div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
    <table class="w-full text-left text-sm text-gray-600">
        <thead class="bg-gray-50 text-xs uppercase text-gray-700">
            <tr>
                <th class="px-5 py-3">Nama</th>
                <th class="px-5 py-3">Nomor HP</th>
                <th class="px-5 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse ($items as $u)
            <tr class="transition-colors hover:bg-gray-50">
                <td class="px-5 py-3 font-medium text-gray-900">{{ $u->name }}</td>
                <td class="px-5 py-3">{{ $u->phone }}</td>
                <td class="px-5 py-3 text-right">
                    <form method="POST" action="{{ route('kasir.customers.reset', $u) }}"
                          onsubmit="return confirm('Reset password pelanggan ini?')">
                        @csrf
                        @method('PATCH')
                        <button class="font-medium text-amber-600 hover:underline">Reset password</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="3" class="px-5 py-10 text-center text-gray-500">Belum ada pelanggan.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection