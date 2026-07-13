<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed data dummy untuk tabel members.
     *
     * Distribusi:
     *  - 15 member aktif
     *  -  8 member kadaluarsa
     *  -  2 member pending
     */
    public function run(): void
    {
        Member::factory()->count(15)->active()->create();
        Member::factory()->count(8)->expired()->create();
        Member::factory()->count(2)->pending()->create();
    }
}
