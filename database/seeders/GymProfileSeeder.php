<?php

namespace Database\Seeders;

use App\Models\GymProfile;
use Illuminate\Database\Seeder;

class GymProfileSeeder extends Seeder
{
    public function run(): void
    {
        GymProfile::query()->delete(); // pastikan cuma 1 baris

        GymProfile::create([
            'name' => 'CukipGym',
            'description' => 'CukipGym adalah gym lokal di Tanjungpinang dengan fasilitas latihan lengkap, kelas kebugaran terjadwal, dan pelatih berpengalaman. Cocok untuk pemula maupun yang sudah rutin berlatih.',
            'address' => 'Jl. Contoh No. 10, Tanjungpinang',
            'latitude' => 0.9187,
            'longitude' => 104.4665,
            'opening_hours' => '06:00 - 22:00',
            'phone' => '0771-000001',
            'whatsapp' => '6281200000001',
        ]);
    }
}