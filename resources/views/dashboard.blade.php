@extends('layout.app')
@section('title', 'Dashboard')

@section('content')
    <div class="rounded-xl border border-gray-200 bg-white p-8 shadow-sm">
        <p class="text-sm font-medium uppercase tracking-wide text-blue-700">Kedai Kopi Senja</p>
        <h1 class="mt-2 text-2xl font-bold text-gray-900">Halo, {{ auth()->user()->name }}</h1>
        <p class="mt-2 text-gray-600">Login berhasil sebagai {{ auth()->user()->role }}.</p>
    </div>
@endsection