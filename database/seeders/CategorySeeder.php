<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Gym', 'description' => 'Tempat latihan beban dan kebugaran umum.'],
            ['name' => 'Fitness Center', 'description' => 'Pusat kebugaran dengan alat kardio, beban, dan kelas.'],
            ['name' => 'CrossFit', 'description' => 'Latihan fungsional intensitas tinggi dengan pelatih.'],
            ['name' => 'Women Only', 'description' => 'Gym khusus perempuan.'],
        ];

        foreach ($categories as $category) {
            Category::create($category + ['slug' => Str::slug($category['name'])]);
        }
    }
}