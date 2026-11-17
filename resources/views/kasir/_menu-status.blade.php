{{-- biar ga nulis banyak @if/@elseif/@else yg banyak  --}}

@php
    $map = [
        'tersedia' => ['Tersedia', 'bg-green-100 text-green-800'],
        'habis' => ['Habis', 'bg-red-100 text-red-800'],
        'nonaktif' => ['Nonaktif', 'bg-gray-100 text-gray-800'],
        'tanpa_resep' => ['Belum ada resep', 'bg-amber-100 text-amber-800'],
    ];
    [$label, $class] = $map[$status];
@endphp
<span class="{{ $class }} text-xs font-medium px-2.5 py-0.5 rounded">{{ $label }}</span>