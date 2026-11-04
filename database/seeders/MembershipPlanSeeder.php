<?php

namespace Database\Seeders;

use App\Models\MembershipPlan;
use Illuminate\Database\Seeder;

class MembershipPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            ['name' => 'Paket Harian', 'duration_days' => 1, 'price' => 25000, 'description' => 'Akses satu hari penuh.'],
            ['name' => 'Paket Mingguan', 'duration_days' => 7, 'price' => 100000, 'description' => 'Akses tujuh hari.'],
            ['name' => 'Paket Bulanan', 'duration_days' => 30, 'price' => 250000, 'description' => 'Akses satu bulan penuh, termasuk kelas.'],
            ['name' => 'Paket Tahunan', 'duration_days' => 365, 'price' => 2500000, 'description' => 'Akses satu tahun, hemat dibanding bulanan.'],
        ];

        foreach ($plans as $plan) {
            MembershipPlan::create($plan);
        }
    }
}