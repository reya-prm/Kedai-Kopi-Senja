<?php

use App\Models\Bahan;
use App\Models\StokMasuk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('adds stock and displays a success message', function () {
    $manager = User::create([
        'name' => 'Manager',
        'role' => 'manajer',
        'username' => 'manager',
        'password' => 'password',
    ]);

    $bahan = Bahan::create([
        'nama' => 'Susu',
        'satuan_dasar' => 'ml',
        'stok' => 1,
        'batas_minimum' => 0,
    ]);

    $this->actingAs($manager)
        ->from(route('manajer.stok.index'))
        ->post(route('manajer.stok.store'), [
            'bahan_id' => $bahan->id,
            'jumlah' => '2.500',
            'tanggal' => now()->toDateString(),
        ])
        ->assertRedirect(route('manajer.stok.index'))
        ->assertSessionHas('success', 'Stok masuk berhasil dicatat.');

    expect((float) $bahan->fresh()->stok)->toBe(3.5)
        ->and(StokMasuk::count())->toBe(1)
        ->and((float) StokMasuk::firstOrFail()->jumlah)->toBe(2.5);
});

it('groups stock receipt and adjustment under stock management', function () {
    $manager = User::create([
        'name' => 'Manager',
        'role' => 'manajer',
        'username' => 'manager',
        'password' => 'password',
    ]);

    $this->actingAs($manager)
        ->get(route('manajer.stok.index'))
        ->assertOk()
        ->assertSeeText('Kelola Stok')
        ->assertSeeText('Penyesuaian Stok')
        ->assertDontSeeText('Kelola Stok Masuk');
});
