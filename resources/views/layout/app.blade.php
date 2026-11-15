<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('judul') - Kedai Kopi Senja</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">
    

    {{-- navbar atas --}}
    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-screen-7xl mx-auto flex items-center gap-6 px-4 py-3 text-sm">
            <a href="{{ route('dashboard') }}" class="text-lg font-semibold">Kedai Kopi Senja</a>

            {{-- tempat buat link menu per peran (bahan, menu, pesanan, dll) --}}

            <div class="ml-auto flex items-center gap-4">
                <span class="text-gray-500">{{ auth()->user()->nama }} | {{ auth()->user()->peran }}</span>
                <form method="POST" action="{{ route('keluar') }}">
                    @csrf
                    <button class="font-medium text-blue-700 hover:underline">Keluar</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="max-w-screen-7xl mx-auto px-4 py-6 space-y-4">
        {{-- pesan sukses/gagal dari controller  --}}
        @if (session('sukses'))
            <div class="p-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">{{ session('sukses') }}</div>
        @endif
        @if (session('eror'))
            <div class="p-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">{{ session('eror') }}</div>
        @endif

        @yield('isi')
    </main>
</body>
</html>