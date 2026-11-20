@extends('layout.app')
@section('title', 'Dashboard')

@section('content')
    <div class="p-6 space-y-1 bg-white border border-gray-200 rounded-lg shadow-sm">
        <h1 class="text-xl font-bold">Halo, {{ auth()->user()->name }}</h1>
        <p class="text-sm text-gray-500">Login berhasil sebagai {{ auth()->user()->role }}.</p>
    </div>
@endsection