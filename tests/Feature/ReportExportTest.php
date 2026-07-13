<?php

use App\Models\ParkingSlot;
use App\Models\ParkingTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can export report to excel', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $slot = ParkingSlot::create([
        'slot_code' => 'A1',
        'vehicle_type' => 'mobil',
        'floor' => 1,
        'status' => 'available',
    ]);

    // Create a transaction to be exported
    ParkingTransaction::create([
        'slot_id' => $slot->id,
        'status' => 'exited',
        'entry_time' => now()->subHour(),
        'exit_time' => now(),
        'fee' => 5000,
        'vehicle_type' => 'mobil',
        'vehicle_plate' => 'B1234ABC',
        'snapshot_first_hour_rate' => 3000,
        'snapshot_subsequent_hour_rate' => 2000,
        'snapshot_daily_max_rate' => 20000,
        'snapshot_fine_lost_ticket' => 10000,
    ]);

    $response = $this->actingAs($admin)->get(route('report.export.excel', [
        'period_type' => 'daily',
        'date' => now()->format('Y-m-d'),
    ]));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

    // Run the streaming callback to ensure there are no PHP execution/formatting errors
    ob_start();
    $response->sendContent();
    $content = ob_get_clean();

    expect($content)->not->toBeEmpty();
});

test('admin can export report to pdf', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $slot = ParkingSlot::create([
        'slot_code' => 'A1',
        'vehicle_type' => 'mobil',
        'floor' => 1,
        'status' => 'available',
    ]);

    ParkingTransaction::create([
        'slot_id' => $slot->id,
        'status' => 'exited',
        'entry_time' => now()->subHour(),
        'exit_time' => now(),
        'fee' => 5000,
        'vehicle_type' => 'mobil',
        'vehicle_plate' => 'B1234ABC',
        'snapshot_first_hour_rate' => 3000,
        'snapshot_subsequent_hour_rate' => 2000,
        'snapshot_daily_max_rate' => 20000,
        'snapshot_fine_lost_ticket' => 10000,
    ]);

    $response = $this->actingAs($admin)->get(route('report.export.pdf', [
        'period_type' => 'daily',
        'date' => now()->format('Y-m-d'),
    ]));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/pdf');

    // Verify content is not empty
    expect($response->getContent())->not->toBeEmpty();
});
