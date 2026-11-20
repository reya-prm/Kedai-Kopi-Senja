<?php

use App\Models\Bahan;
use App\Models\PenyesuaianStok;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('sets stock to the physical count and records the before and after values', function () {
    $manager = User::create([
        'name' => 'Manager',
        'role' => 'manajer',
        'username' => 'manager',
        'password' => 'password',
    ]);

    $bahan = Bahan::create([
        'nama' => 'Susu',
        'satuan_dasar' => 'ml',
        'stok' => 8.5,
        'batas_minimum' => 0,
    ]);

    $this->actingAs($manager)
        ->post(route('manajer.penyesuaian-stok.store'), [
            'bahan_id' => $bahan->id,
            'jenis' => 'opname',
            'jumlah' => '12.375',
            'catatan' => 'Hitung rak utama',
        ])
        ->assertRedirect(route('manajer.penyesuaian-stok.index'))
        ->assertSessionHas('success');

    expect((float) $bahan->fresh()->stok)->toBe(12.375);

    $record = PenyesuaianStok::firstOrFail();
    expect($record->jenis)->toBe('opname')
        ->and((float) $record->stok_sebelum)->toBe(8.5)
        ->and((float) $record->stok_sesudah)->toBe(12.375)
        ->and($record->catatan)->toBe('Hitung rak utama')
        ->and($record->user_id)->toBe($manager->id);
});

it('deducts damaged stock and rejects a deduction larger than available stock', function () {
    $manager = User::create([
        'name' => 'Manager',
        'role' => 'manajer',
        'username' => 'manager',
        'password' => 'password',
    ]);

    $bahan = Bahan::create([
        'nama' => 'Kopi',
        'satuan_dasar' => 'g',
        'stok' => 5,
        'batas_minimum' => 0,
    ]);

    $this->actingAs($manager)
        ->post(route('manajer.penyesuaian-stok.store'), [
            'bahan_id' => $bahan->id,
            'jenis' => 'rusak',
            'jumlah' => '1.250',
        ])
        ->assertRedirect(route('manajer.penyesuaian-stok.index'));

    expect((float) $bahan->fresh()->stok)->toBe(3.75);
    expect(PenyesuaianStok::count())->toBe(1);

    $this->from(route('manajer.penyesuaian-stok.index'))
        ->post(route('manajer.penyesuaian-stok.store'), [
            'bahan_id' => $bahan->id,
            'jenis' => 'rusak',
            'jumlah' => '4',
        ])
        ->assertRedirect(route('manajer.penyesuaian-stok.index'))
        ->assertSessionHasErrors('jumlah');

    expect((float) $bahan->fresh()->stok)->toBe(3.75);
    expect(PenyesuaianStok::count())->toBe(1);
});

it('allows managers only to view stock adjustments', function () {
    $cashier = User::create([
        'name' => 'Cashier',
        'role' => 'kasir',
        'username' => 'cashier',
        'password' => 'password',
    ]);

    $this->actingAs($cashier)
        ->get(route('manajer.penyesuaian-stok.index'))
        ->assertForbidden();
});
