@extends('layout.app')
@section('judul', 'Dashboard')
    
@section('isi')
    <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm space-y-3">
        <h1 class="text-xl font-bold">Halo, {{ auth()->user()->nama }}</h1>
        <p class="text-sm text-gray-500">Login berhasil sebagai {{ auth()->user()->peran }}.</p>

        {{-- link uji sementara buat ngecek pembatasan peran, nanti dihapus --}}
        <ul class="text-sm text-blue-700 list-disc pl-5">
            <li><a href="{{ route('manajer.uji') }}" class="hover:underline">Area Manajer</a></li>
            <li><a href="{{ route('kasir.uji') }}" class="hover:underline">Area Kasir</a></li>
        </ul>
    </div>
@endsection