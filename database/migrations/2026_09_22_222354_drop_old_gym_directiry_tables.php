<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Urutan drop memperhatikan foreign key: anak dulu, baru induk
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('gym_images');
        Schema::dropIfExists('gym_packages');
        Schema::dropIfExists('facilities');
        Schema::dropIfExists('gyms');
        Schema::dropIfExists('categories');
    }

    public function down(): void
    {
        // Tidak perlu dikembalikan; ini migration satu arah untuk perombakan struktur.
    }
};