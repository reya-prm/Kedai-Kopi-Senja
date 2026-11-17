@extends('layout.app')
@section('title', 'Pelanggan')

@section('content')
<h1 class="text-xl font-bold">Pelanggan</h1>

<form method="GET" action="{{ route('kasir.customers.index') }}" class="flex max-w-md gap-2">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nomor HP"
           class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5">
    <button class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-4 py-2.5">Cari</button>
</form>

<div class="relative overflow-x-auto bg-white border border-gray-200 rounded-lg shadow-sm">
    <table class="w-full text-sm text-left text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
                <th class="px-6 py-3">Nama</th>
                <th class="px-6 py-3">Nomor HP</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody>
        @forelse ($items as $u)
            <tr class="bg-white border-b border-gray-200">
                <td class="px-6 py-3 font-medium text-gray-900">{{ $u->name }}</td>
                <td class="px-6 py-3">{{ $u->phone }}</td>
                <td class="px-6 py-3 text-right">
                    <form method="POST" action="{{ route('kasir.customers.reset', $u) }}"
                          onsubmit="return confirm('Reset password pelanggan ini?')">
                        @csrf
                        @method('PATCH')
                        <button class="font-medium text-amber-600 hover:underline">Reset password</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="3" class="px-6 py-4 text-center">Belum ada pelanggan.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection