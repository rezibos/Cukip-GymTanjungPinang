<?php

namespace Database\Seeders;

use App\Enums\FacilityCategory;
use App\Models\Facility;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        $facilities = [
            ['name' => 'Treadmill', 'category' => FacilityCategory::Cardio, 'description' => 'Atur kecepatan langkahmu untuk sesi berjalan atau berlari.'],
            ['name' => 'Dumbbell', 'category' => FacilityCategory::Strength, 'description' => 'Peralatan beban bebas yang fleksibel untuk berbagai kelompok otot.'],
            ['name' => 'Barbell', 'category' => FacilityCategory::Strength, 'description' => 'Beban bebas untuk menyusun variasi gerakan dan latihan kekuatan.'],
            ['name' => 'Bench Press', 'category' => FacilityCategory::Strength, 'description' => 'Bangku latihan untuk mendukung gerakan dorong dan latihan beban.'],
            ['name' => 'Locker', 'category' => FacilityCategory::Support, 'description' => 'Tempat menyimpan barang agar kamu bisa lebih fokus selama latihan.'],
            ['name' => 'Toilet', 'category' => FacilityCategory::Support, 'description' => 'Fasilitas sanitasi yang melengkapi kebutuhan selama berada di gym.'],
            ['name' => 'Parkir', 'category' => FacilityCategory::Support, 'description' => 'Area untuk memarkir kendaraan sebelum memulai sesi latihanmu.'],
            ['name' => 'AC', 'category' => FacilityCategory::Support, 'description' => 'Area berpendingin untuk menjaga kenyamanan selama kamu berlatih.'],
            ['name' => 'Ruang Ganti', 'category' => FacilityCategory::Support, 'description' => 'Ruang untuk berganti pakaian sebelum dan setelah beraktivitas.'],
            ['name' => 'Air Minum', 'category' => FacilityCategory::Support, 'description' => 'Lengkapi kebutuhan minummu di sela aktivitas dan setelah latihan.'],
        ];

        foreach ($facilities as $facility) {
            Facility::query()->firstOrCreate(['name' => $facility['name']], $facility);
        }
    }
}
