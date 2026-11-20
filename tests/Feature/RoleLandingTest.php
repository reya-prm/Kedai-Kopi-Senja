<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects a manager to ingredients after login', function () {
    User::create([
        'name' => 'Manager',
        'role' => 'manajer',
        'username' => 'manager',
        'password' => 'password',
    ]);

    $this->post(route('login'), [
        'username' => 'manager',
        'password' => 'password',
    ])->assertRedirect(route('manajer.bahan.index'));
});

it('redirects a cashier to the menu after login', function () {
    User::create([
        'name' => 'Cashier',
        'role' => 'kasir',
        'username' => 'cashier',
        'password' => 'password',
    ]);

    $this->post(route('login'), [
        'username' => 'cashier',
        'password' => 'password',
    ])->assertRedirect(route('kasir.menu.index'));
});

it('redirects authenticated managers away from the login page to ingredients', function () {
    $manager = User::create([
        'name' => 'Manager',
        'role' => 'manajer',
        'username' => 'manager',
        'password' => 'password',
    ]);

    $this->actingAs($manager)
        ->get(route('login'))
        ->assertRedirect(route('manajer.bahan.index'));
});

it('redirects authenticated cashiers away from the login page to the menu', function () {
    $cashier = User::create([
        'name' => 'Cashier',
        'role' => 'kasir',
        'username' => 'cashier',
        'password' => 'password',
    ]);

    $this->actingAs($cashier)
        ->get(route('login'))
        ->assertRedirect(route('kasir.menu.index'));
});
