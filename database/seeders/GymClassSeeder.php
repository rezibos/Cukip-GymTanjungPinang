<?php

namespace Database\Seeders;

use App\Models\GymClass;
use Illuminate\Database\Seeder;

class GymClassSeeder extends Seeder
{
    public function run(): void
    {
        $classes = [
            ['name' => 'Yoga Pagi', 'instructor' => 'Kak Rina', 'day_of_week' => 'senin', 'start_time' => '07:00', 'end_time' => '08:00', 'capacity' => 15],
            ['name' => 'HIIT', 'instructor' => 'Kak Doni', 'day_of_week' => 'selasa', 'start_time' => '17:00', 'end_time' => '18:00', 'capacity' => 12],
            ['name' => 'Zumba', 'instructor' => 'Kak Sinta', 'day_of_week' => 'rabu', 'start_time' => '18:00', 'end_time' => '19:00', 'capacity' => 20],
            ['name' => 'Body Combat', 'instructor' => 'Kak Doni', 'day_of_week' => 'kamis', 'start_time' => '17:00', 'end_time' => '18:00', 'capacity' => 12],
            ['name' => 'Yoga Sore', 'instructor' => 'Kak Rina', 'day_of_week' => 'jumat', 'start_time' => '16:00', 'end_time' => '17:00', 'capacity' => 15],
            ['name' => 'Functional Training', 'instructor' => 'Kak Bayu', 'day_of_week' => 'sabtu', 'start_time' => '09:00', 'end_time' => '10:00', 'capacity' => 10],
        ];

        foreach ($classes as $class) {
            GymClass::create($class + ['is_active' => true]);
        }
    }
}