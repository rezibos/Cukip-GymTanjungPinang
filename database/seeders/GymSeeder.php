<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use App\Enums\Region;
use App\Models\Category;
use App\Models\Gym;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class GymSeeder extends Seeder
{
    public function run(): void
    {
        $gyms = [
            [
                'name' => 'FitZone Tanjungpinang', 'category' => 'Fitness Center',
                'region' => Region::Tanjungpinang, 'district' => 'Tanjungpinang Kota',
                'address' => 'Jl. Contoh No. 10, Tanjungpinang',
                'lat' => 0.9187, 'lng' => 104.4665, 'hours' => '06:00 - 22:00',
                'phone' => '0771-000001', 'whatsapp' => '6281200000001',
                'description' => 'Fitness center di pusat kota dengan alat kardio dan beban yang lengkap, cocok untuk pemula maupun yang sudah rutin berlatih.',
                'prices' => [25000, 100000, 250000],
                'facilities' => ['Treadmill', 'Dumbbell', 'Barbell', 'Bench Press', 'Locker', 'Toilet', 'Parkir', 'AC'],
            ],
            [
                'name' => 'Power House Gym', 'category' => 'Gym',
                'region' => Region::Tanjungpinang, 'district' => 'Tanjungpinang Timur',
                'address' => 'Jl. Contoh No. 25, Tanjungpinang',
                'lat' => 0.9110, 'lng' => 104.4790, 'hours' => '07:00 - 21:00',
                'phone' => '0771-000002', 'whatsapp' => '6281200000002',
                'description' => 'Gym fokus latihan beban dengan area free weight yang luas dan suasana latihan yang santai.',
                'prices' => [20000, 80000, 200000],
                'facilities' => ['Dumbbell', 'Barbell', 'Bench Press', 'Squat Rack', 'Toilet', 'Parkir'],
            ],
            [
                'name' => 'Titan Fitness', 'category' => 'Gym',
                'region' => Region::Tanjungpinang, 'district' => 'Bukit Bestari',
                'address' => 'Jl. Contoh No. 7, Tanjungpinang',
                'lat' => 0.9350, 'lng' => 104.4560, 'hours' => '06:00 - 22:00',
                'phone' => '0771-000003', 'whatsapp' => '6281200000003',
                'description' => 'Gym dengan peralatan mesin lengkap dan ruang latihan ber-AC. Tersedia personal trainer.',
                'prices' => [30000, 120000, 300000],
                'facilities' => ['Treadmill', 'Leg Press', 'Dumbbell', 'Locker', 'Toilet', 'Parkir', 'AC', 'Personal Trainer'],
            ],
            [
                'name' => 'Iron Kepri Gym', 'category' => 'Gym',
                'region' => Region::Tanjungpinang, 'district' => 'Tanjungpinang Barat',
                'address' => 'Jl. Contoh No. 33, Tanjungpinang',
                'lat' => 0.9200, 'lng' => 104.4480, 'hours' => '08:00 - 21:00',
                'phone' => '0771-000004', 'whatsapp' => '6281200000004',
                'description' => 'Gym sederhana dengan harga bersahabat untuk pelajar dan mahasiswa.',
                'prices' => [15000, 70000, 150000],
                'facilities' => ['Dumbbell', 'Barbell', 'Bench Press', 'Toilet', 'Parkir'],
            ],
            [
                'name' => 'Srikandi Women Fitness', 'category' => 'Women Only',
                'region' => Region::Tanjungpinang, 'district' => 'Tanjungpinang Kota',
                'address' => 'Jl. Contoh No. 18, Tanjungpinang',
                'lat' => 0.9165, 'lng' => 104.4620, 'hours' => '08:00 - 20:00',
                'phone' => '0771-000005', 'whatsapp' => '6281200000005',
                'description' => 'Gym khusus perempuan dengan instruktur perempuan dan kelas senam rutin.',
                'prices' => [25000, 100000, 275000],
                'facilities' => ['Treadmill', 'Dumbbell', 'Kelas Senam', 'Locker', 'Toilet', 'Musala', 'AC'],
            ],
            [
                'name' => 'Bintan Fitness Center', 'category' => 'Fitness Center',
                'region' => Region::Bintan, 'district' => 'Bintan Timur',
                'address' => 'Jl. Contoh No. 5, Kijang, Bintan',
                'lat' => 0.8500, 'lng' => 104.6000, 'hours' => '06:00 - 21:00',
                'phone' => '0771-000006', 'whatsapp' => '6281200000006',
                'description' => 'Pusat kebugaran di Kijang dengan area kardio, beban, dan lapangan parkir yang luas.',
                'prices' => [20000, 90000, 225000],
                'facilities' => ['Treadmill', 'Dumbbell', 'Barbell', 'Locker', 'Toilet', 'Parkir', 'AC'],
            ],
            [
                'name' => 'Uban Muscle Club', 'category' => 'Gym',
                'region' => Region::Bintan, 'district' => 'Bintan Utara',
                'address' => 'Jl. Contoh No. 12, Tanjung Uban, Bintan',
                'lat' => 1.0667, 'lng' => 104.2167, 'hours' => '07:00 - 22:00',
                'phone' => '0771-000007', 'whatsapp' => '6281200000007',
                'description' => 'Gym di Tanjung Uban dengan peralatan beban lengkap untuk pekerja dan warga sekitar.',
                'prices' => [20000, 85000, 200000],
                'facilities' => ['Dumbbell', 'Barbell', 'Squat Rack', 'Bench Press', 'Toilet', 'Parkir'],
            ],
            [
                'name' => 'Bintan CrossFit Box', 'category' => 'CrossFit',
                'region' => Region::Bintan, 'district' => 'Teluk Sebong',
                'address' => 'Jl. Contoh No. 3, Bintan',
                'lat' => 1.1000, 'lng' => 104.4500, 'hours' => '05:30 - 20:00',
                'phone' => '0771-000008', 'whatsapp' => '6281200000008',
                'description' => 'Box CrossFit dengan kelas terjadwal dipandu pelatih. Tersedia sesi pengenalan untuk pemula.',
                'prices' => [50000, 200000, 450000],
                'facilities' => ['Barbell', 'Kettlebell', 'Pull-up Rig', 'Rowing Machine', 'Toilet', 'Parkir'],
            ],
        ];

        foreach ($gyms as $data) {
            $gym = Gym::create([
                'category_id' => Category::where('name', $data['category'])->value('id'),
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'description' => $data['description'],
                'address' => $data['address'],
                'region' => $data['region'],
                'district' => $data['district'],
                'phone' => $data['phone'],
                'whatsapp' => $data['whatsapp'],
                'latitude' => $data['lat'],
                'longitude' => $data['lng'],
                'opening_hours' => $data['hours'],
            ]);

            // Paket harga (harian, mingguan, bulanan)
            [$daily, $weekly, $monthly] = $data['prices'];
            $gym->packages()->createMany([
                ['name' => 'Paket Harian',   'duration' => '1 hari',  'price' => $daily],
                ['name' => 'Paket Mingguan', 'duration' => '1 minggu', 'price' => $weekly],
                ['name' => 'Paket Bulanan',  'duration' => '1 bulan', 'price' => $monthly],
            ]);

            // Fasilitas
            $gym->facilities()->createMany(
                array_map(fn ($name) => ['name' => $name], $data['facilities'])
            );
        }

        $this->seedReviews();
    }

    private function seedReviews(): void
    {
        $users = User::where('role', 'user')->get();

        $comments = [
            'Tempatnya bersih dan alatnya cukup lengkap.',
            'Harga terjangkau, cocok untuk pemula.',
            'Suasananya nyaman, tidak terlalu ramai di pagi hari.',
            'Alat cukup lengkap, tapi parkir agak sempit.',
            'Staf ramah dan membantu menjelaskan cara pakai alat.',
            'Lokasinya mudah dijangkau, jam bukanya pas.',
            'AC dingin dan tempatnya terawat.',
            'Sedikit ramai di sore hari, tapi overall bagus.',
        ];

        foreach (Gym::all() as $gym) {
            // 3 user acak per gym, 1 review per user (sesuai unique constraint)
            foreach ($users->random(3) as $user) {
                Review::create([
                    'user_id' => $user->id,
                    'gym_id' => $gym->id,
                    'rating' => rand(3, 5),
                    'review' => $comments[array_rand($comments)],
                    'status' => Review::STATUS_APPROVED,
                ]);
            }
        }
    }
}