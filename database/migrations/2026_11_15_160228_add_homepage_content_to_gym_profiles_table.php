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
        Schema::table('gym_profiles', function (Blueprint $table) {
            $table->string('hero_image')->nullable();
            $table->string('hero_eyebrow')->nullable();
            $table->string('hero_title_primary')->nullable();
            $table->string('hero_title_secondary')->nullable();
            $table->text('hero_description')->nullable();
            $table->string('hero_cta_label')->nullable();
            $table->string('hero_about_label')->nullable();
            $table->string('about_eyebrow')->nullable();
            $table->string('about_location_label')->nullable();
            $table->string('about_title_line_one')->nullable();
            $table->string('about_title_line_two')->nullable();
            $table->string('about_title_line_three')->nullable();
            $table->text('about_description_primary')->nullable();
            $table->text('about_description_secondary')->nullable();
            $table->string('about_gallery_image_one')->nullable();
            $table->string('about_gallery_image_two')->nullable();
            $table->string('about_gallery_image_three')->nullable();
            $table->string('about_gallery_image_four')->nullable();
            $table->string('about_gallery_image_five')->nullable();
            $table->string('about_gallery_eyebrow')->nullable();
            $table->text('about_gallery_title')->nullable();
            $table->text('footer_description')->nullable();
            $table->string('footer_location')->nullable();
            $table->json('footer_sponsors')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gym_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'hero_image',
                'hero_eyebrow',
                'hero_title_primary',
                'hero_title_secondary',
                'hero_description',
                'hero_cta_label',
                'hero_about_label',
                'about_eyebrow',
                'about_location_label',
                'about_title_line_one',
                'about_title_line_two',
                'about_title_line_three',
                'about_description_primary',
                'about_description_secondary',
                'about_gallery_image_one',
                'about_gallery_image_two',
                'about_gallery_image_three',
                'about_gallery_image_four',
                'about_gallery_image_five',
                'about_gallery_eyebrow',
                'about_gallery_title',
                'footer_description',
                'footer_location',
                'footer_sponsors',
            ]);
        });
    }
};
