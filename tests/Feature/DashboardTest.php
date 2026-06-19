<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));

    $response->assertRedirect(route('login'));
});

test('admin can visit the dashboard', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get(route('dashboard'));

    $response->assertOk();
});

test('staff cannot visit the dashboard and is redirected to pos entry', function () {
    $staff = User::factory()->create(['role' => 'staff']);

    $response = $this->actingAs($staff)->get(route('dashboard'));

    $response->assertRedirect(route('pos.entry'));
    $response->assertSessionHas('error', 'Anda tidak memiliki akses ke halaman ini.');
});
