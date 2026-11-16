<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - Kedai Kopi Senja</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto min-h-screen">
        <h1 class="mb-6 text-2xl font-semibold text-gray-900">Kedai Kopi Senja</h1>

        <div class="w-full bg-white border border-gray-200 rounded-lg shadow-sm sm:max-w-md">
            <form method="POST" action="{{ route('login') }}" class="p-6 space-y-4">
                {{-- @csrf wajib ada, kalau lupa Laravel nolak form-nya dengan error 419 --}}
                @csrf

                <h2 class="text-xl font-bold text-gray-900">Masuk ke akun Anda</h2>

                @error('username')
                    <div class="p-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">{{ $message }}</div>
                @enderror

                <div>
                    <label for="username" class="block mb-2 text-sm font-medium text-gray-900">Username</label>
                    <input type="text" name="username" id="username" value="{{ old('username') }}" required autofocus
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5">
                </div>

                <div>
                    <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Password</label>
                    <input type="password" name="password" id="password" required
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5">
                </div>

                <label class="flex items-center gap-2 text-sm text-gray-500">
                    <input type="checkbox" name="remember"
                    class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300">
                    Ingat saya
                </label>

                <button type="submit"
                        class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                    Masuk
                </button>
            </form>
        </div>
    </div>
</body>
</html>