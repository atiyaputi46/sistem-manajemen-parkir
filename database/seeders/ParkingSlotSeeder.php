<?php

namespace Database\Seeders;

use App\Models\ParkingSlot;
use Illuminate\Database\Seeder;

class ParkingSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 20 slot motor (M01–M20)
        for ($i = 1; $i <= 20; $i++) {
            ParkingSlot::create([
                'slot_code' => sprintf('M%02d', $i),
                'vehicle_type' => 'motor',
                'floor' => '1',
                'status' => 'available',
            ]);
        }

        // 10 slot mobil (C01–C10)
        for ($i = 1; $i <= 10; $i++) {
            ParkingSlot::create([
                'slot_code' => sprintf('C%02d', $i),
                'vehicle_type' => 'mobil',
                'floor' => '1',
                'status' => 'available',
            ]);
        }

        // 5 slot truk (T01–T05)
        for ($i = 1; $i <= 5; $i++) {
            ParkingSlot::create([
                'slot_code' => sprintf('T%02d', $i),
                'vehicle_type' => 'truk',
                'floor' => '1',
                'status' => 'available',
            ]);
        }
    }
}
