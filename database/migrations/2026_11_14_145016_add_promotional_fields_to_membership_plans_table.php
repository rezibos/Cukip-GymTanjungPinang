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
        Schema::table('membership_plans', function (Blueprint $table) {
            $table->unsignedInteger('original_price')->nullable()->after('price');
            $table->string('discount_label', 40)->nullable()->after('original_price');
            $table->string('badge', 60)->nullable()->after('discount_label');
            $table->json('benefits')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membership_plans', function (Blueprint $table) {
            $table->dropColumn(['original_price', 'discount_label', 'badge', 'benefits']);
        });
    }
};
