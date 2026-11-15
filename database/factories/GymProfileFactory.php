<?php

namespace Database\Factories;

use App\Models\GymProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GymProfile>
 */
class GymProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company().' Gym',
            'description' => 'Gym lokal di Tanjungpinang.',
            'address' => fake()->streetAddress().', Tanjungpinang',
            'latitude' => 0.9187,
            'longitude' => 104.4665,
            'opening_hours' => '06:00 - 22:00',
            'whatsapp' => null,
            'image' => null,
            'hero_image' => null,
            'hero_eyebrow' => null,
            'hero_title_primary' => null,
            'hero_title_secondary' => null,
            'hero_description' => null,
            'hero_cta_label' => null,
            'hero_about_label' => null,
            'about_eyebrow' => null,
            'about_location_label' => null,
            'about_title_line_one' => null,
            'about_title_line_two' => null,
            'about_title_line_three' => null,
            'about_description_primary' => null,
            'about_description_secondary' => null,
            'about_gallery_image_one' => null,
            'about_gallery_image_two' => null,
            'about_gallery_image_three' => null,
            'about_gallery_image_four' => null,
            'about_gallery_image_five' => null,
            'about_gallery_eyebrow' => null,
            'about_gallery_title' => null,
            'footer_description' => null,
            'footer_location' => null,
            'footer_sponsors' => null,
        ];
    }
}
