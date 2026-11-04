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
        Schema::create('class_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_class_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->unsignedInteger('capacity'); // disalin dari template saat generate, bisa admin override
            $table->timestamps();

            $table->unique(['gym_class_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_sessions');
}
};
