@extends('layout.app')
@section('title', 'Ganti Password')
    
@section('content')
    <form method="POST" action="{{ route('password.update') }}" class="max-w-md p-6 space-y-4 bg-white border border-gray-200 rounded-lg shadow-sm">
        @csrf
        @method('PUT')

        <h1 class="text-xl font-bold">Ganti Password</h1>

        <div>
        <label class="block mb-2 text-sm font-medium text-gray-900">Password lama</label>
        <input type="password" name="current_password" required
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5">
        @error('current_password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block mb-2 text-sm font-medium text-gray-900">Password baru (minimal 8 karakter)</label>
        <input type="password" name="password" required
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5">
        @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block mb-2 text-sm font-medium text-gray-900">Ulangi password baru</label>
        <input type="password" name="password_confirmation" required
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5">
    </div>

    <button class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
        Simpan
    </button>
    </form>
@endsection