<?php

namespace App\Http\Controllers\Kasir;

use App\Exceptions\OrderException;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // 50 pesanan terakhir
    public function index()
    {
        return view('kasir.orders.index', ['items' => Pesanan::latest()->limit(50)->get()]);
    }

    public function create()
    {
        // sama kayak daftar menu: menu nonaktif nggak ditampilin di form pesanan
        return view('kasir.orders.create', [
            'items' => Menu::forSale()->with('resep.bahan')->orderBy('nama')->get(),
        ]);
    }

    // OrderService di parameter ini diisi otomatis sama Laravel (dependency injection)
    public function store(Request $request, OrderService $orders)
    {
        $request->validate([
            'table_number' => ['required', 'integer', 'between:1,10'],
            'payment_method' => ['required', 'in:tunai,qris'],
            'items' => ['required', 'array'],
            'items.*' => ['nullable', 'integer', 'min:0', 'max:99'],
        ], [
            'items.required' => 'Pilih minimal satu menu.',
        ]);

        // form ngirim semua menu (yang jumlahnya 0 juga), jadi di sini diambil yang lebih dari 0 aja
        $items = [];
        foreach ($request->items as $menuId => $qty) {
            if ((int) $qty > 0) {
                $items[(int) $menuId] = (int) $qty;
            }
        }

        if (empty($items)) {
            return back()->withErrors(['items' => 'Pilih minimal satu menu.'])->withInput();
        }

        try {
            $pesanan = $orders->createOrder(
                $items,
                (int) $request->table_number,
                $request->payment_method,
                $request->user()
            );
        } catch (OrderException $e) {
            // aturan bisnis dilanggar (stok kurang, menu nonaktif, dst): tampilin pesannya ke kasir
            return back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()->route('kasir.orders.show', $pesanan)->with('success', 'Pesanan berhasil dibuat.');
    }

    public function show(Pesanan $pesanan)
    {
        return view('kasir.orders.show', ['pesanan' => $pesanan->load('detail', 'pembuat')]);
    }

        public function updateStatus(Request $request, Pesanan $pesanan, OrderService $orders)
    {
        // dibatalkan sengaja nggak ada di sini, pembatalan punya jalur sendiri
        $request->validate(['status' => ['required', 'in:diproses,selesai']]);

        try {
            $orders->changeStatus($pesanan, $request->status);
        } catch (OrderException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Status pesanan diperbarui.');
    }

    public function cancel(Pesanan $pesanan, OrderService $orders)
    {
        try {
            $orders->cancelOrder($pesanan);
        } catch (OrderException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Pesanan dibatalkan dan stok dikembalikan.');
    }

    // halaman struk yang siap dicetak
    public function receipt(Pesanan $pesanan)
    {
        // load() narik detail item + nama kasir + nama pelanggan (kalau ada) sekalian
        return view('kasir.orders.receipt', [
            'pesanan' => $pesanan->load('detail', 'pembuat', 'pelanggan'),
        ]);
    }
}