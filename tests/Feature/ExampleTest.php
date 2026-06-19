<?php

use App\Models\User;

test('root redirects guest to login', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});

test('root redirects authenticated user to pos entry', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertRedirect(route('pos.entry'));
});
