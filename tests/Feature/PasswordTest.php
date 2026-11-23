<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('allows cashiers to access the password change page and update their password', function () {
    $cashier = User::create([
        'name' => 'Cashier',
        'role' => 'kasir',
        'username' => 'cashier',
        'password' => 'old-password',
    ]);

    $this->actingAs($cashier)
        ->get(route('kasir.menu.index'))
        ->assertOk()
        ->assertSee(route('password.edit'))
        ->assertSeeText('Ganti Password');

    $this->get(route('password.edit'))->assertOk();

    $this->put(route('password.update'), [
        'current_password' => 'old-password',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ])->assertRedirect()
        ->assertSessionHas('success', 'Password berhasil diubah.');

    expect(Hash::check('new-password', $cashier->fresh()->password))->toBeTrue();
});
