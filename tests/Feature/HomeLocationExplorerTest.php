<?php

namespace Tests\Feature;

use App\Models\GymLocation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HomeLocationExplorerTest extends TestCase
{
    use RefreshDatabase;

    public function test_each_mapped_location_includes_its_own_photo_and_contact_details(): void
    {
        GymLocation::query()->delete();
        GymLocation::factory()->create([
            'name' => 'Gym Pertama',
            'image_path' => 'locations/first.jpg',
            'whatsapp' => '081234567890',
            'sort_order' => 1,
        ]);
        GymLocation::factory()->create([
            'name' => 'Gym Kedua',
            'latitude' => 0.93,
            'longitude' => 104.47,
            'image_path' => 'locations/second.jpg',
            'opening_hours' => '08:00 - 21:00',
            'sort_order' => 2,
        ]);

        $response = $this->get('/');

        $response->assertOk()->assertSee('data-location-explorer', false)->assertDontSee('data-location-select', false);
        $locations = $response->viewData('marker');
        $this->assertSame(['Gym Pertama', 'Gym Kedua'], array_column($locations, 'name'));
        $this->assertSame(Storage::disk('public')->url('locations/second.jpg'), $locations[1]['image']);
        $this->assertSame('08:00 - 21:00', $locations[1]['hours']);
        $this->assertSame('https://www.google.com/maps?q=0.93,104.47', $locations[1]['maps_url']);
        $this->assertSame('https://wa.me/6281234567890', $locations[0]['whatsapp_url']);
    }

    public function test_empty_locations_keep_the_location_panel_and_show_an_empty_map_message(): void
    {
        GymLocation::query()->delete();

        $response = $this->get('/');

        $response->assertOk()->assertSee('Lokasi belum tersedia')->assertSee('Koordinat lokasi belum tersedia.');
        $this->assertSame([], $response->viewData('marker'));
    }

    public function test_location_names_are_escaped_and_photos_have_a_fallback(): void
    {
        GymLocation::query()->delete();
        GymLocation::factory()->create(['name' => '<script>alert(1)</script>', 'image_path' => null]);

        $response = $this->get('/');

        $response->assertOk()->assertDontSee('<script>alert(1)</script>', false);
        $this->assertSame(asset('images/gym-equipment-editorial.webp'), $response->viewData('marker')[0]['image']);
    }

    public function test_initial_locations_render_six_selectable_locations_without_preview_label(): void
    {
        $response = $this->get('/');

        $response->assertOk()->assertDontSee('Cabang contoh')->assertDontSee('data-location-select', false)
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

    public function test_homepage_only_maps_active_locations_in_display_order(): void
    {
        GymLocation::query()->delete();
        GymLocation::factory()->create(['name' => 'Kedua', 'sort_order' => 20, 'is_active' => true]);
        GymLocation::factory()->create(['name' => 'Pertama', 'sort_order' => 10, 'is_active' => true]);
        GymLocation::factory()->create(['name' => 'Tidak tampil', 'sort_order' => 1, 'is_active' => false]);

        $response = $this->get(route('home'));

        $this->assertSame(['Pertama', 'Kedua'], array_column($response->viewData('marker'), 'name'));
        $response->assertDontSeeText('Tidak tampil');
    }
}
