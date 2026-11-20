@extends('layout.app')
@section('title', 'Ganti Password')

@section('content')
    <form method="POST" action="{{ route('password.update') }}" class="max-w-xl space-y-5 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        @csrf
        @method('PUT')

        <div>
            <h1 class="text-2xl font-bold text-gray-900">Ganti Password</h1>
            <p class="mt-1 text-sm text-gray-500">Gunakan password baru yang minimal terdiri dari 8 karakter.</p>
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium text-gray-900">Password lama</label>
            <input type="password" name="current_password" required
                   class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-600 focus:ring-blue-600">
            @error('current_password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium text-gray-900">Password baru</label>
            <input type="password" name="password" required
                   class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-600 focus:ring-blue-600">
            @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium text-gray-900">Ulangi password baru</label>
            <input type="password" name="password_confirmation" required
                   class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-600 focus:ring-blue-600">
        </div>

        <button class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300">
            Simpan Password
        </button>
    </form>
@endsection