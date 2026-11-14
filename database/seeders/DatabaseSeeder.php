<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            GymProfileSeeder::class,
            MembershipPlanSeeder::class,
            FacilitySeeder::class,
            GymClassSeeder::class,
            ClassSessionSeeder::class,
            PersonalTrainerSeeder::class,
            UserSeeder::class,
            DemoTransactionSeeder::class,
        ]);
    }
}
