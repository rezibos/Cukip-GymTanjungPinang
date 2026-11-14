<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trainer_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_trainer_id')->constrained()->cascadeOnDelete();
            $table->dateTime('starts_at')->index();
            $table->dateTime('ends_at');
            $table->string('status')->default('available')->index();
            $table->dateTime('held_until')->nullable()->index();
            $table->timestamps();
            $table->unique(['personal_trainer_id', 'starts_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainer_slots');
    }
};
