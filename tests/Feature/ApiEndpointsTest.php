<?php

use App\Models\Member;
use App\Models\ParkingRate;
use App\Models\ParkingSlot;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ─── GET /api/available-slots ───────────────────────────────────────────────

test('available-slots returns correct counts per vehicle type', function () {
    ParkingSlot::create(['slot_code' => 'M01', 'vehicle_type' => 'motor', 'status' => 'available']);
    ParkingSlot::create(['slot_code' => 'M02', 'vehicle_type' => 'motor', 'status' => 'occupied']);
    ParkingSlot::create(['slot_code' => 'C01', 'vehicle_type' => 'mobil', 'status' => 'available']);
    ParkingSlot::create(['slot_code' => 'T01', 'vehicle_type' => 'truk', 'status' => 'occupied']);

    $response = $this->getJson('/api/available-slots');

    $response->assertOk()
        ->assertJson([
            'motor' => ['available' => 1, 'total' => 2],
            'mobil' => ['available' => 1, 'total' => 1],
            'truk' => ['available' => 0, 'total' => 1],
        ]);
});

test('available-slots returns zero counts when no slots exist', function () {
    $response = $this->getJson('/api/available-slots');

    $response->assertOk()
        ->assertJson([
            'motor' => ['available' => 0, 'total' => 0],
            'mobil' => ['available' => 0, 'total' => 0],
            'truk' => ['available' => 0, 'total' => 0],
        ]);
});

// ─── GET /api/rates ─────────────────────────────────────────────────────────

test('rates returns all parking rates', function () {
    ParkingRate::create([
        'vehicle_type' => 'motor',
        'first_hour_rate' => 3000,
        'subsequent_hour_rate' => 2000,
        'daily_max_rate' => 20000,
        'fine_lost_ticket' => 10000,
    ]);

    $response = $this->getJson('/api/rates');

    $response->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment(['vehicle_type' => 'motor']);
});

test('rates returns empty array when no rates exist', function () {
    $response = $this->getJson('/api/rates');

    $response->assertOk()->assertExactJson([]);
});

// ─── POST /api/members ───────────────────────────────────────────────────────

test('register member succeeds with valid data', function () {
    $response = $this->postJson('/api/members', [
        'full_name' => 'Budi Santoso',
        'vehicle_plate' => 'b1234abc',
        'vehicle_type' => 'motor',
        'phone' => '081234567890',
    ]);

    $response->assertCreated()
        ->assertJson(['message' => 'Pendaftaran berhasil. Admin akan menghubungi Anda untuk aktivasi.']);

    $this->assertDatabaseHas('members', [
        'full_name' => 'Budi Santoso',
        'vehicle_plate' => 'B1234ABC', // stored as uppercase
        'vehicle_type' => 'motor',
        'phone' => '081234567890',
        'status' => 'pending',
    ]);
});

test('register member fails when required fields are missing', function () {
    $response = $this->postJson('/api/members', []);

    $response->assertUnprocessable()
        ->assertJsonStructure(['errors' => ['full_name', 'vehicle_plate', 'vehicle_type', 'phone']]);
});

test('register member fails when vehicle_plate is already taken', function () {
    Member::create([
        'full_name' => 'Existing Member',
        'vehicle_plate' => 'B9999XYZ',
        'vehicle_type' => 'mobil',
        'phone' => '081111111111',
        'status' => 'pending',
    ]);

    $response = $this->postJson('/api/members', [
        'full_name' => 'New Member',
        'vehicle_plate' => 'B9999XYZ',
        'vehicle_type' => 'mobil',
        'phone' => '082222222222',
    ]);

    $response->assertUnprocessable()
        ->assertJsonStructure(['errors' => ['vehicle_plate']]);
});

test('register member fails with invalid vehicle_type', function () {
    $response = $this->postJson('/api/members', [
        'full_name' => 'Test User',
        'vehicle_plate' => 'D1234EF',
        'vehicle_type' => 'sepeda',
        'phone' => '081234567890',
    ]);

    $response->assertUnprocessable()
        ->assertJsonStructure(['errors' => ['vehicle_type']]);
});

test('register member fails when phone is too short', function () {
    $response = $this->postJson('/api/members', [
        'full_name' => 'Test User',
        'vehicle_plate' => 'E5678GH',
        'vehicle_type' => 'motor',
        'phone' => '0812',
    ]);

    $response->assertUnprocessable()
        ->assertJsonStructure(['errors' => ['phone']]);
});
