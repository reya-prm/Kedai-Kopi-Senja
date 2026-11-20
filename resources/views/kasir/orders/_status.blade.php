@php
    $map = [
        'baru' => 'bg-blue-100 text-blue-800',
        'diproses' => 'bg-amber-100 text-amber-800',
        'selesai' => 'bg-green-100 text-green-800',
        'dibatalkan' => 'bg-red-100 text-red-800',
    ];
@endphp
<span class="{{ $map[$status] }} inline-flex rounded-full px-2.5 py-1 text-xs font-medium">{{ ucfirst($status) }}</span>