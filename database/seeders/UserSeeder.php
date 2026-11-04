<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin CukipGym',
            'email' => 'admin@cukipgym.test',
            'phone' => '6281200000000',
            'password' => 'password',
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'User Demo',
            'email' => 'user@cukipgym.test',
            'phone' => '6281300000000',
            'password' => 'password',
            'role' => 'user',
        ]);

        $names = ['Andi Contoh', 'Sari Contoh', 'Budi Contoh', 'Dewi Contoh'];
        foreach ($names as $i => $name) {
            User::create([
                'name' => $name,
                'email' => 'member' . ($i + 1) . '@example.com',
                'phone' => '62812000000' . ($i + 10),
                'password' => 'password',
                'role' => 'user',
            ]);
        }
    }
}