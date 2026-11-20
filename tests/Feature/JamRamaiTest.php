<?php

use App\Models\Menu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('groups completed order counts, portions, and billed totals by creation hour', function () {
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

    $createOrder = function (string $status, string $createdAt, int $total, int $quantity) use ($manager, $menu): void {
        $orderId = DB::table('pesanan')->insertGetId([
            'dibuat_oleh' => $manager->id,
            'nomor_meja' => 1,
            'metode_bayar' => 'tunai',
            'status' => $status,
            'subtotal' => $total,
            'service' => 0,
            'pajak' => 0,
            'total' => $total,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        DB::table('detail_pesanan')->insert([
            'pesanan_id' => $orderId,
            'menu_id' => $menu->id,
            'nama_menu' => 'Latte',
            'harga_satuan' => $total,
            'jumlah' => $quantity,
            'total_baris' => $total,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);
    };

    $createOrder('selesai', '2026-11-18 09:10:00', 30000, 2);
    $createOrder('selesai', '2026-11-18 09:50:00', 40000, 3);
    $createOrder('diproses', '2026-11-18 09:30:00', 90000, 6);
    $createOrder('selesai', '2026-11-19 09:30:00', 50000, 2);

    $this->actingAs($manager)
        ->get(route('manajer.laporan.jam-ramai', [
            'from' => '2026-11-18',
            'to' => '2026-11-18',
        ]))
        ->assertOk()
        ->assertViewHas('rows', function ($rows) {
            $nineOClock = $rows->firstWhere('hour', 9);
            $tenOClock = $rows->firstWhere('hour', 10);

            return $nineOClock['order_count'] === 2
                && $nineOClock['portion_count'] === 5
                && $nineOClock['billed_total'] === 70000
                && $tenOClock['order_count'] === 0;
        })
        ->assertViewHas('summary', fn (array $summary) => $summary['total_orders'] === 2
            && $summary['total_portions'] === 5
            && $summary['total_billed'] === 70000)
        ->assertViewHas('peakHour', fn (array $peakHour) => $peakHour['hour'] === 9);
});

it('shows zero values when there are no completed orders in the selected period', function () {
    $manager = User::create([
        'name' => 'Manager',
        'role' => 'manajer',
        'username' => 'manager',
        'password' => 'password',
    ]);

    $this->actingAs($manager)
        ->get(route('manajer.laporan.jam-ramai', [
            'from' => '2026-11-18',
            'to' => '2026-11-18',
        ]))
        ->assertOk()
        ->assertViewHas('summary', fn (array $summary) => $summary['total_orders'] === 0
            && $summary['total_portions'] === 0
            && $summary['total_billed'] === 0)
        ->assertViewHas('peakHour', null);
});
