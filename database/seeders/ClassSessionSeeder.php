<?php

namespace Database\Seeders;

use App\Models\ClassSession;
use App\Models\GymClass;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ClassSessionSeeder extends Seeder
{
    public function run(): void
    {
        $classes = GymClass::where('is_active', true)->get();

        // Generate 2 minggu ke depan untuk keperluan testing frontend
        for ($i = 0; $i < 14; $i++) {
            $date = Carbon::today()->addDays($i);

            foreach ($classes as $class) {
                if ($class->day_of_week->value !== \App\Enums\DayOfWeek::fromCarbon($date)->value) {
                    continue;
                }

                ClassSession::firstOrCreate(
                    ['gym_class_id' => $class->id, 'date' => $date->toDateString()],
                    ['capacity' => $class->capacity]
                );
            }
        }
    }
}