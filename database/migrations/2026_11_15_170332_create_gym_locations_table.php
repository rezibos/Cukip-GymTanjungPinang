<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gym_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('address', 255);
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('opening_hours', 150);
            $table->string('whatsapp', 20)->nullable();
            $table->string('image_path')->nullable();
            $table->string('image_position', 30)->default('center');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        if (DB::table('gym_locations')->doesntExist()) {
            $timestamp = now();

            DB::table('gym_locations')->insert([
                [
                    'name' => 'CukipGym · Pusat Kota',
                    'address' => 'Area Tanjungpinang Kota, Tanjungpinang',
                    'latitude' => 0.9274,
                    'longitude' => 104.4446,
                    'opening_hours' => '06:00 – 22:00 · Setiap hari',
                    'image_path' => 'images/gym-equipment-editorial.webp',
                    'image_position' => 'left center',
                    'sort_order' => 1,
                    'is_active' => true,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ],
                [
                    'name' => 'CukipGym · Batu 9',
                    'address' => 'Area Batu IX, Tanjungpinang Timur',
                    'latitude' => 0.9270,
                    'longitude' => 104.5158,
                    'opening_hours' => '06:00 – 22:00 · Setiap hari',
                    'image_path' => 'images/gym-cable-row-editorial.webp',
                    'image_position' => '35% center',
                    'sort_order' => 2,
                    'is_active' => true,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ],
                [
                    'name' => 'CukipGym · Bukit Bestari',
                    'address' => 'Area Bukit Bestari, Tanjungpinang',
                    'latitude' => 0.8988,
                    'longitude' => 104.4622,
                    'opening_hours' => '07:00 – 22:00 · Setiap hari',
                    'image_path' => 'images/gym-kettlebell-editorial.webp',
                    'image_position' => 'center',
                    'sort_order' => 3,
                    'is_active' => true,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ],
                [
                    'name' => 'CukipGym · Tanjung Unggat',
                    'address' => 'Area Tanjung Unggat, Bukit Bestari',
                    'latitude' => 0.9108,
                    'longitude' => 104.4528,
                    'opening_hours' => '06:00 – 21:00 · Setiap hari',
                    'image_path' => 'images/gym-equipment-editorial.webp',
                    'image_position' => 'right center',
                    'sort_order' => 4,
                    'is_active' => true,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ],
                [
                    'name' => 'CukipGym · Melayu Kota Piring',
                    'address' => 'Area Melayu Kota Piring, Tanjungpinang Timur',
                    'latitude' => 0.9295,
                    'longitude' => 104.4892,
                    'opening_hours' => '06:00 – 22:00 · Setiap hari',
                    'image_path' => 'images/gym-cable-row-editorial.webp',
                    'image_position' => 'right center',
                    'sort_order' => 5,
                    'is_active' => true,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ],
                [
                    'name' => 'CukipGym · Senggarang',
                    'address' => 'Area Senggarang, Tanjungpinang Kota',
                    'latitude' => 0.9625,
                    'longitude' => 104.4300,
                    'opening_hours' => '07:00 – 21:00 · Setiap hari',
                    'image_path' => 'images/gym-kettlebell-editorial.webp',
                    'image_position' => '65% center',
                    'sort_order' => 6,
                    'is_active' => true,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gym_locations');
    }
};
