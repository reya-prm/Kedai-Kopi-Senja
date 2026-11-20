<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Kedai Kopi Senja</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">
    <nav class="border-b border-gray-200 bg-white">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center gap-x-6 gap-y-3 px-4 py-3 text-sm">
            <a href="{{ route('home') }}" class="shrink-0 text-lg font-bold text-gray-900">
                Kedai Kopi Senja
            </a>

            <div class="flex flex-wrap items-center gap-1">
                {{-- link buat manajer misal bahan, resep, dll --}}
                @if (auth()->user()->role === 'manajer')
                    <a href="{{ route('manajer.bahan.index') }}" class="rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-700">Bahan</a>
                    <a href="{{ route('manajer.menu.index') }}" class="rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-700">Menu</a>
                    <a href="{{ route('manajer.resep.index') }}" class="rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-700">Resep</a>
                    <a href="{{ route('manajer.stok.index') }}" class="rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-700">Stok Masuk</a>
                    <a href="{{ route('manajer.laporan.index') }}" class="rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-700">Laporan</a>
                    <a href="{{ route('manajer.bahan.low-stock') }}" class="rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-700">Bahan Menipis</a>
                    <a href="{{ route('password.edit') }}" class="rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-700">Ganti Password</a>
                @endif

                {{-- link buat kasir membuat pesanan, ubah status, dll --}}
                @if (auth()->user()->role === 'kasir')
                    <a href="{{ route('kasir.menu.index') }}" class="rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-700">Daftar Menu</a>
                    <a href="{{ route('kasir.orders.create') }}" class="rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-700">Buat Pesanan</a>
                    <a href="{{ route('kasir.orders.index') }}" class="rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-700">Pesanan</a>
                    <a href="{{ route('kasir.customers.index') }}" class="rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-700">Pelanggan</a>
                @endif
            </div>

            <div class="ml-auto flex items-center gap-3">
                <span class="text-right text-gray-500">
                    <span class="font-medium text-gray-800">{{ auth()->user()->name }}</span>
                    <span class="text-gray-400">·</span>
                    {{ auth()->user()->role }}
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-lg px-3 py-2 font-medium text-blue-700 hover:bg-blue-50">Keluar</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="mx-auto max-w-7xl space-y-5 px-4 py-6">
        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800" role="alert">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>
</body>
</html>
