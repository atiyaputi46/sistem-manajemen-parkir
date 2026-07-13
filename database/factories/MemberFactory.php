<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $vehicleType = fake()->randomElement(['motor', 'mobil', 'truk']);

        // Plat nomor format Indonesia: [daerah] [angka] [huruf]
        $daerah = fake()->randomElement(['B', 'D', 'F', 'E', 'Z', 'AB', 'AD', 'AG', 'AE', 'L', 'N', 'W', 'S', 'K']);
        $angka = fake()->numerify('####');
        $huruf = strtoupper(fake()->lexify('??'));
        $plate = "{$daerah} {$angka} {$huruf}";

        $start = fake()->dateTimeBetween('-18 months', 'now');
        $end = (clone $start)->modify('+1 year');
        $now = new \DateTime;

        if ($end < $now) {
            $status = 'expired';
        } elseif ($start > $now) {
            $status = 'pending';
        } else {
            $status = 'active';
        }

        return [
            'full_name' => fake('id_ID')->name(),
            'vehicle_plate' => $plate,
            'vehicle_type' => $vehicleType,
            'phone' => fake('id_ID')->phoneNumber(),
            'subscription_start' => $start->format('Y-m-d'),
            'subscription_end' => $end->format('Y-m-d'),
            'status' => $status,
        ];
    }

    /** Member dengan langganan aktif. */
    public function active(): static
    {
        return $this->state(function () {
            $start = fake()->dateTimeBetween('-6 months', '-1 week');
            $end = (clone $start)->modify('+1 year');

            return [
                'subscription_start' => $start->format('Y-m-d'),
                'subscription_end' => $end->format('Y-m-d'),
                'status' => 'active',
            ];
        });
    }

    /** Member dengan langganan kadaluarsa. */
    public function expired(): static
    {
        return $this->state(function () {
            $start = fake()->dateTimeBetween('-3 years', '-13 months');
            $end = (clone $start)->modify('+1 year');

            return [
                'subscription_start' => $start->format('Y-m-d'),
                'subscription_end' => $end->format('Y-m-d'),
                'status' => 'expired',
            ];
        });
    }

    /** Member dengan status pending (belum mulai). */
    public function pending(): static
    {
        return $this->state(function () {
            $start = fake()->dateTimeBetween('+1 week', '+1 month');
            $end = (clone $start)->modify('+1 year');

            return [
                'subscription_start' => $start->format('Y-m-d'),
                'subscription_end' => $end->format('Y-m-d'),
                'status' => 'pending',
            ];
        });
    }
}
