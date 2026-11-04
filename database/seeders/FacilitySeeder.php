<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        $facilities = [
            'Treadmill', 'Dumbbell', 'Barbell', 'Bench Press',
            'Locker', 'Toilet', 'Parkir', 'AC', 'Ruang Ganti', 'Air Minum',
        ];

        foreach ($facilities as $name) {
            Facility::create(['name' => $name]);
        }
    }
}