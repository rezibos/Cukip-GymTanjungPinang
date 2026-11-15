<?php

namespace Database\Seeders;

use App\Models\GymLocation;
use Illuminate\Database\Seeder;

class GymLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            ['name' => 'CukipGym · Pusat Kota', 'address' => 'Area Tanjungpinang Kota, Tanjungpinang', 'latitude' => 0.9274, 'longitude' => 104.4446, 'opening_hours' => '06:00 – 22:00 · Setiap hari', 'image_path' => 'images/gym-equipment-editorial.webp', 'image_position' => 'left center'],
            ['name' => 'CukipGym · Batu 9', 'address' => 'Area Batu IX, Tanjungpinang Timur', 'latitude' => 0.9270, 'longitude' => 104.5158, 'opening_hours' => '06:00 – 22:00 · Setiap hari', 'image_path' => 'images/gym-cable-row-editorial.webp', 'image_position' => '35% center'],
            ['name' => 'CukipGym · Bukit Bestari', 'address' => 'Area Bukit Bestari, Tanjungpinang', 'latitude' => 0.8988, 'longitude' => 104.4622, 'opening_hours' => '07:00 – 22:00 · Setiap hari', 'image_path' => 'images/gym-kettlebell-editorial.webp', 'image_position' => 'center'],
            ['name' => 'CukipGym · Tanjung Unggat', 'address' => 'Area Tanjung Unggat, Bukit Bestari', 'latitude' => 0.9108, 'longitude' => 104.4528, 'opening_hours' => '06:00 – 21:00 · Setiap hari', 'image_path' => 'images/gym-equipment-editorial.webp', 'image_position' => 'right center'],
            ['name' => 'CukipGym · Melayu Kota Piring', 'address' => 'Area Melayu Kota Piring, Tanjungpinang Timur', 'latitude' => 0.9295, 'longitude' => 104.4892, 'opening_hours' => '06:00 – 22:00 · Setiap hari', 'image_path' => 'images/gym-cable-row-editorial.webp', 'image_position' => 'right center'],
            ['name' => 'CukipGym · Senggarang', 'address' => 'Area Senggarang, Tanjungpinang Kota', 'latitude' => 0.9625, 'longitude' => 104.4300, 'opening_hours' => '07:00 – 21:00 · Setiap hari', 'image_path' => 'images/gym-kettlebell-editorial.webp', 'image_position' => '65% center'],
        ];

        foreach ($locations as $index => $location) {
            GymLocation::query()->firstOrCreate(
                ['name' => $location['name']],
                [...$location, 'sort_order' => $index + 1, 'is_active' => true],
            );
        }
    }
}
