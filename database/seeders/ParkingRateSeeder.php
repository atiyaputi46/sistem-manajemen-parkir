<?php

namespace Database\Seeders;

use App\Models\ParkingRate;
use Illuminate\Database\Seeder;

class ParkingRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ParkingRate::create([
            'vehicle_type' => 'motor',
            'first_hour_rate' => 2000,
            'subsequent_hour_rate' => 1000,
            'daily_max_rate' => null,
            'fine_lost_ticket' => 20000,
        ]);

        ParkingRate::create([
            'vehicle_type' => 'mobil',
            'first_hour_rate' => 5000,
            'subsequent_hour_rate' => 3000,
            'daily_max_rate' => 50000,
            'fine_lost_ticket' => 50000,
        ]);

        ParkingRate::create([
            'vehicle_type' => 'truk',
            'first_hour_rate' => 10000,
            'subsequent_hour_rate' => 7000,
            'daily_max_rate' => 100000,
            'fine_lost_ticket' => 100000,
        ]);
    }
}
