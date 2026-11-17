<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Kedai Kopi Senja</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">
    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-screen-7xl mx-auto flex items-center gap-4 px-4 py-3 text-sm">
            <a href="{{ route('dashboard') }}" class="text-lg font-semibold">Kedai Kopi Senja</a>

            {{-- link buat manajer misal bahan, resep, dll --}}
            @if (auth()->user()->role === 'manajer')
                <a href="{{ route('manajer.bahan.index') }}" class="text-gray-700 hover:text-blue-700">Bahan</a>
                <a href="{{ route('manajer.menu.index') }}" class="text-gray-700 hover:text-blue-700">Menu</a>
                <a href="{{ route('manajer.stok.index') }}" class="text-gray-700 hover:text-blue-700">Stok Masuk</a>
                <a href="{{ route('password.edit') }}" class="text-gray-700 hover:text-blue-700">Ganti Password</a>
            @endif
            

            <div class="ml-auto flex items-center gap-4">
                <span class="text-gray-500">{{ auth()->user()->name }} · {{ auth()->user()->role }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="font-medium text-blue-700 hover:underline">Keluar</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="max-w-screen-7xl mx-auto px-4 py-6 space-y-4">
        @if (session('success'))
            <div class="p-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="p-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>
</body>
</html>
