<?php

// Registrasi publik dinonaktifkan — akun dibuat oleh admin melalui Menu Users.

test('registration screen returns 404', function () {
    $response = $this->get('/register');

    $response->assertStatus(404);
});

test('registration endpoint returns 404', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertStatus(404);
    $this->assertGuest();
});
