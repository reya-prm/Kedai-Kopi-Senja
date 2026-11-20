@extends('layout.app')
@section('title', 'Buat Pesanan')

@section('content')
<form method="POST" action="{{ route('kasir.orders.store') }}" class="space-y-6" data-order-form>
    @csrf

    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-blue-700">Kasir / Pesanan baru</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900">Buat pesanan</h1>
            <p class="mt-1 text-sm text-slate-500">Pilih menu, atur jumlah, lalu lengkapi detail pesanan.</p>
        </div>
        <a href="{{ route('kasir.orders.index') }}" class="text-sm font-medium text-slate-600 hover:text-blue-700">
            Kembali ke daftar pesanan
        </a>
    </div>

    @error('items')
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">{{ $message }}</div>
    @enderror

    <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_20rem]">
        <section>
            <div class="mb-3 flex items-center justify-between">
                <h2 class="font-semibold text-slate-900">Pilih menu</h2>
                <span class="text-sm text-slate-500">{{ count($items) }} menu</span>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                @forelse ($items as $m)
                    @php
                        $status = $m->sales_status;
                        $isAvailable = $status === 'tersedia';
                        $quantity = $isAvailable ? (int) old('items.'.$m->id, 0) : 0;
                    @endphp

                    <article
                        data-order-item
                        data-menu-name="{{ $m->nama }}"
                        data-menu-price="{{ $m->harga }}"
                        @class([
                            'rounded-xl border bg-white p-4 transition',
                            'border-slate-200' => $quantity === 0,
                            'border-blue-300 ring-1 ring-blue-100' => $quantity > 0,
                            'opacity-60' => ! $isAvailable,
                        ])
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="truncate font-semibold text-slate-900">{{ $m->nama }}</h3>
                                <p class="mt-0.5 text-xs text-slate-500">{{ $m->kategori ?: 'Menu kedai' }}</p>
                            </div>
                            @include('kasir._menu-status', ['status' => $status])
                        </div>

                        <div class="mt-4 flex items-center justify-between gap-3">
                            <span class="font-semibold tabular-nums text-slate-900">Rp{{ number_format($m->harga, 0, ',', '.') }}</span>

                            <button
                                type="button"
                                data-add-menu
                                @disabled(! $isAvailable)
                                @class([
                                    'rounded-lg bg-blue-700 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-100 disabled:cursor-not-allowed disabled:bg-slate-300',
                                    'hidden' => $quantity > 0,
                                ])
                            >
                                + Tambah
                            </button>

                            <div @class(['flex items-center gap-2', 'hidden' => $quantity === 0]) data-quantity-controls>
                                <button
                                    type="button"
                                    data-quantity-change="-1"
                                    aria-label="Kurangi {{ $m->nama }}"
                                    @disabled(! $isAvailable)
                                    class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-300 text-lg font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-blue-100 disabled:cursor-not-allowed"
                                >−</button>
                                <input
                                    id="item-{{ $m->id }}"
                                    type="number"
                                    name="items[{{ $m->id }}]"
                                    data-menu-quantity
                                    readonly
                                    min="0"
                                    max="99"
                                    step="1"
                                    value="{{ $quantity }}"
                                    @disabled(! $isAvailable)
                                    aria-label="Jumlah {{ $m->nama }}"
                                    class="w-14 rounded-lg border border-slate-300 px-2 py-2 text-center text-sm font-semibold tabular-nums text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                                >
                                <button
                                    type="button"
                                    data-quantity-change="1"
                                    aria-label="Tambah {{ $m->nama }}"
                                    @disabled(! $isAvailable)
                                    class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-300 text-lg font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-blue-100 disabled:cursor-not-allowed"
                                >+</button>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-xl border border-dashed border-slate-300 bg-white p-8 text-center">
                        <p class="font-medium text-slate-800">Belum ada menu yang bisa dipesan.</p>
                        <p class="mt-1 text-sm text-slate-500">Menu aktif dengan resep akan muncul di sini.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <aside class="space-y-4 lg:sticky lg:top-6">
            <section class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold text-slate-900">Pesanan</h2>
                    <span class="text-sm text-slate-500"><span data-order-count>0</span> porsi</span>
                </div>

                <div class="mt-4 border-y border-slate-100 py-3" aria-live="polite">
                    <p data-order-empty class="text-sm text-slate-500">Belum ada menu dipilih.</p>
                    <ul data-order-summary-list class="hidden space-y-3"></ul>
                </div>

                <div class="flex items-center justify-between py-4">
                    <span class="text-sm text-slate-600">Subtotal sementara</span>
                    <span data-order-subtotal class="font-semibold tabular-nums text-slate-900">Rp0</span>
                </div>
                <p class="text-xs leading-5 text-slate-500">Service dan pajak dihitung saat pesanan disimpan.</p>
            </section>

            <section class="space-y-4 rounded-xl border border-slate-200 bg-white p-5">
                <div>
                    <label for="table_number" class="mb-2 block text-sm font-medium text-slate-800">Nomor meja</label>
                    <select
                        id="table_number"
                        name="table_number"
                        required
                        class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                    >
                        <option value="">Pilih meja</option>
                        @for ($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" @selected(old('table_number') == $i)>Meja {{ $i }}</option>
                        @endfor
                    </select>
                    @error('table_number')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="payment_method" class="mb-2 block text-sm font-medium text-slate-800">Metode pembayaran</label>
                    <select
                        id="payment_method"
                        name="payment_method"
                        required
                        class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                    >
                        <option value="">Pilih metode</option>
                        <option value="tunai" @selected(old('payment_method') === 'tunai')>Tunai</option>
                        <option value="qris" @selected(old('payment_method') === 'qris')>QRIS</option>
                    </select>
                    @error('payment_method')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="w-full rounded-lg bg-blue-700 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-200"
                >
                    Simpan pesanan
                </button>
            </section>
        </aside>
    </div>
</form>
@endsection
