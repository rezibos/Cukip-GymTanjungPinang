<?php

namespace Database\Seeders;

use App\Models\PersonalTrainer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PersonalTrainerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $trainers = [
            ['name' => 'Kak Bayu', 'specialization' => 'Functional Training', 'description' => 'Pendamping latihan fungsional untuk kekuatan, mobilitas, dan kebugaran harian.', 'experience_years' => 4, 'certifications' => ['Certified Personal Trainer', 'Functional Training Specialist'], 'session_price' => 150000, 'session_minutes' => 180],
            ['name' => 'Kak Rina', 'specialization' => 'Mobility & Yoga', 'description' => 'Program privat untuk fleksibilitas, postur, mobilitas, dan pemulihan tubuh.', 'experience_years' => 5, 'certifications' => ['Yoga Teacher Certification', 'Mobility Coach'], 'session_price' => 150000, 'session_minutes' => 120],
            ['name' => 'Kak Doni', 'specialization' => 'Strength & Conditioning', 'description' => 'Latihan kekuatan terukur untuk meningkatkan performa dan teknik gerakan.', 'experience_years' => 6, 'certifications' => ['Strength Coach', 'Certified Personal Trainer'], 'session_price' => 175000, 'session_minutes' => 180],
            ['name' => 'Kak Sinta', 'specialization' => 'Weight Management', 'description' => 'Pendampingan latihan personal untuk membangun kebiasaan aktif dan progres berkelanjutan.', 'experience_years' => 3, 'certifications' => ['Weight Management Specialist', 'Certified Personal Trainer'], 'session_price' => 150000, 'session_minutes' => 120],
        ];

        foreach ($trainers as $index => $attributes) {
            $trainer = PersonalTrainer::query()->updateOrCreate(['name' => $attributes['name']], $attributes + ['is_active' => true]);

            for ($day = 1; $day <= 14; $day++) {
                $start = Carbon::today()->addDays($day)->setTime(8 + (($index + $day) % 8), 0);
                $trainer->slots()->updateOrCreate(['starts_at' => $start], ['ends_at' => $start->copy()->addMinutes($trainer->session_minutes)]);
            }
        }
    }
}
