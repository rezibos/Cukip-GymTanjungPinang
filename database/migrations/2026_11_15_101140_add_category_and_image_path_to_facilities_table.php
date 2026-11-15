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
        Schema::table('facilities', function (Blueprint $table) {
            $table->string('category')->default('kenyamanan')->after('name');
            $table->string('image_path')->nullable()->after('description');
        });

        DB::table('facilities')
            ->select(['id', 'name'])
            ->orderBy('id')
            ->each(function (object $facility): void {
                $category = match (true) {
                    (bool) preg_match('/barbell|barbel|dumbbell|bench|rack|smith|cable|beban/i', $facility->name) => 'kekuatan',
                    (bool) preg_match('/treadmill|sepeda|bike|elliptical|cardio|kardio/i', $facility->name) => 'kardio',
                    (bool) preg_match('/stretch|recovery|shower|sauna|pijat|matras|foam|yoga/i', $facility->name) => 'pemulihan',
                    default => 'kenyamanan',
                };

                DB::table('facilities')->where('id', $facility->id)->update(['category' => $category]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->dropColumn(['category', 'image_path']);
        });
    }
};
