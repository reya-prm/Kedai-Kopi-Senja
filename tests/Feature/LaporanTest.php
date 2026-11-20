<?php

use App\Models\Menu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('shows overall completed-order totals and excludes unfinished orders', function () {
    $manager = User::create([
        'name' => 'Manager',
        'role' => 'manajer',
        'username' => 'manager',
        'password' => 'password',
    ]);

    $menu = Menu::create([
        'nama' => 'Latte',
        'harga' => 25000,
        'aktif' => true,
    ]);

    $createdAt = '2026-11-18 12:00:00';
    $completedOrderId = DB::table('pesanan')->insertGetId([
        'dibuat_oleh' => $manager->id,
        'nomor_meja' => 1,
        'metode_bayar' => 'tunai',
        'status' => 'selesai',
        'subtotal' => 70000,
        'service' => 3500,
        'pajak' => 7350,
        'total' => 80850,
        'created_at' => $createdAt,
        'updated_at' => $createdAt,
    ]);

    DB::table('detail_pesanan')->insert([
        [
            'pesanan_id' => $completedOrderId,
            'menu_id' => $menu->id,
            'nama_menu' => 'Latte',
            'harga_satuan' => 25000,
            'jumlah' => 2,
            'total_baris' => 50000,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ],
        [
            'pesanan_id' => $completedOrderId,
            'menu_id' => $menu->id,
            'nama_menu' => 'Latte',
            'harga_satuan' => 20000,
            'jumlah' => 1,
            'total_baris' => 20000,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ],
    ]);

    $unfinishedOrderId = DB::table('pesanan')->insertGetId([
        'dibuat_oleh' => $manager->id,
        'nomor_meja' => 2,
        'metode_bayar' => 'tunai',
        'status' => 'diproses',
        'subtotal' => 100000,
        'service' => 5000,
        'pajak' => 10500,
        'total' => 115500,
        'created_at' => $createdAt,
        'updated_at' => $createdAt,
    ]);

    DB::table('detail_pesanan')->insert([
        'pesanan_id' => $unfinishedOrderId,
        'menu_id' => $menu->id,
        'nama_menu' => 'Latte',
        'harga_satuan' => 25000,
        'jumlah' => 4,
        'total_baris' => 100000,
        'created_at' => $createdAt,
        'updated_at' => $createdAt,
    ]);

    $this->actingAs($manager)
        ->get(route('manajer.laporan.index', ['from' => '2026-11-18', 'to' => '2026-11-18']))
        ->assertOk()
        ->assertViewHas('summary', fn ($summary) => $summary->total_orders === 1
            && $summary->total_billed === 80850
            && $summary->total_portions === 3
            && $summary->total_menu_revenue === 70000)
        ->assertSee('Rp 80.850')
        ->assertSee('Rp 70.000');
});
