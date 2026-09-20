<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Akun demo (data contoh, password: "password")
        User::create([
            'name' => 'Admin GymPinang',
            'email' => 'admin@gympinang.test',
            'password' => 'password',
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'User Demo',
            'email' => 'user@gympinang.test',
            'password' => 'password',
            'role' => 'user',
        ]);

        // Beberapa user dummy untuk review contoh
        $names = ['Andi Contoh', 'Sari Contoh', 'Budi Contoh', 'Dewi Contoh', 'Rizky Contoh', 'Maya Contoh'];

        foreach ($names as $i => $name) {
            User::create([
                'name' => $name,
                'email' => 'member' . ($i + 1) . '@example.com',
                'password' => 'password',
                'role' => 'user',
            ]);
        }
    }
}