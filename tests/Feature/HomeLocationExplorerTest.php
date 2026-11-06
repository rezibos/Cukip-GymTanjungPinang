<?php

namespace Tests\Feature;

use Database\Factories\GymProfileFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HomeLocationExplorerTest extends TestCase
{
    use RefreshDatabase;

    public function test_each_mapped_location_includes_its_own_photo_and_contact_details(): void
    {
        config(['gym_locations.preview_branches' => []]);
        GymProfileFactory::new()->create(['name' => 'Gym Pertama', 'image' => 'locations/first.jpg', 'whatsapp' => '081234567890']);
        GymProfileFactory::new()->create(['name' => 'Gym Kedua', 'latitude' => 0.93, 'longitude' => 104.47, 'image' => 'locations/second.jpg', 'opening_hours' => '08:00 - 21:00']);
        GymProfileFactory::new()->create(['name' => 'Tanpa Koordinat', 'latitude' => null]);

        $response = $this->get('/');

        $response->assertOk()->assertSee('data-location-explorer', false)->assertDontSee('data-location-select', false);
        $locations = $response->viewData('marker');
        $this->assertSame(['Gym Pertama', 'Gym Kedua'], array_column($locations, 'name'));
        $this->assertSame(Storage::disk('public')->url('locations/second.jpg'), $locations[1]['image']);
        $this->assertSame('08:00 - 21:00', $locations[1]['hours']);
        $this->assertSame('https://www.google.com/maps?q=0.93,104.47', $locations[1]['maps_url']);
        $this->assertSame('https://wa.me/6281234567890', $locations[0]['whatsapp_url']);
    }

    public function test_missing_coordinates_keep_the_location_panel_and_show_an_empty_map_message(): void
    {
        config(['gym_locations.preview_branches' => []]);
        GymProfileFactory::new()->create(['name' => 'Gym Tanpa Titik', 'latitude' => null, 'longitude' => null]);

        $response = $this->get('/');

        $response->assertOk()->assertSee('Gym Tanpa Titik')->assertSee('Koordinat lokasi belum tersedia.');
        $this->assertSame([], $response->viewData('marker'));
    }

    public function test_location_names_are_escaped_and_photos_have_a_fallback(): void
    {
        config(['gym_locations.preview_branches' => []]);
        GymProfileFactory::new()->create(['name' => '<script>alert(1)</script>', 'image' => null]);

        $response = $this->get('/');

        $response->assertOk()->assertDontSee('<script>alert(1)</script>', false);
        $this->assertSame(asset('images/gym-equipment-editorial.webp'), $response->viewData('marker')[0]['image']);
    }

    public function test_preview_branches_render_six_selectable_locations_without_route_or_contact_links(): void
    {
        $response = $this->get('/');

        $response->assertOk()->assertSee('Cabang contoh')->assertDontSee('data-location-select', false)
            ->assertDontSee('data-location-maps', false)->assertDontSee('data-location-whatsapp', false);
        $this->assertSame([
            'CukipGym · Pusat Kota',
            'CukipGym · Batu 9',
            'CukipGym · Bukit Bestari',
            'CukipGym · Tanjung Unggat',
            'CukipGym · Melayu Kota Piring',
            'CukipGym · Senggarang',
        ], array_column($response->viewData('marker'), 'name'));
        $this->assertSame(asset('images/gym-equipment-editorial.webp'), $response->viewData('marker')[0]['image']);
    }
}
