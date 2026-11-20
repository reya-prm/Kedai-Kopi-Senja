<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - Kedai Kopi Senja</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="mx-auto flex min-h-screen flex-col items-center justify-center px-6 py-10">
        <h1 class="mb-6 text-3xl font-bold tracking-tight text-gray-900">Kedai Kopi Senja</h1>

        <div class="w-full rounded-xl border border-gray-200 bg-white shadow-sm sm:max-w-md">
            <form method="POST" action="{{ route('login') }}" class="space-y-5 p-8">
                {{-- @csrf wajib ada, kalau lupa Laravel nolak form-nya dengan error 419 --}}
                @csrf

                <div>
                    <h2 class="text-xl font-bold text-gray-900">Masuk ke akun Anda</h2>
                    <p class="mt-1 text-sm text-gray-500">Gunakan akun staf yang telah diberikan.</p>
                </div>

                @error('username')
                    <div class="p-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">{{ $message }}</div>
                @enderror

                <div>
                    <label for="username" class="block mb-2 text-sm font-medium text-gray-900">Username</label>
                    <input type="text" name="username" id="username" value="{{ old('username') }}" required autofocus
                           class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-600 focus:ring-blue-600">
                </div>

                <div>
                    <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Password</label>
                    <input type="password" name="password" id="password" required
                           class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-600 focus:ring-blue-600">
                </div>

                <label class="flex items-center gap-2 text-sm text-gray-500">
                    <input type="checkbox" name="remember"
                           class="h-4 w-4 rounded border-gray-300 bg-gray-50 text-blue-700 focus:ring-blue-300">
                    Ingat saya
                </label>

                <button type="submit"
                        class="w-full rounded-lg bg-blue-700 px-5 py-3 text-center text-sm font-semibold text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300">
                    Masuk
                </button>
            </form>
        </div>
    </div>
</body>
</html>