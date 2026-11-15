<?php

namespace Tests\Feature;

use App\Filament\Resources\GymLocations\Pages\ListGymLocations;
use App\Models\GymLocation;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class GymLocationManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_location_builds_image_maps_and_whatsapp_urls(): void
    {
        GymLocation::query()->delete();

        $location = GymLocation::factory()->create([
            'latitude' => 0.9274,
            'longitude' => 104.4446,
            'whatsapp' => '081234567890',
            'image_path' => 'gym-locations/pusat-kota.jpg',
        ]);

        $this->assertSame(Storage::disk('public')->url('gym-locations/pusat-kota.jpg'), $location->image_url);
        $this->assertSame('https://www.google.com/maps?q=0.9274,104.4446', $location->maps_url);
        $this->assertSame('https://wa.me/6281234567890', $location->whatsapp_url);
    }

    public function test_only_active_locations_are_returned_in_display_order(): void
    {
        GymLocation::query()->delete();

        $second = GymLocation::factory()->create(['name' => 'Kedua', 'sort_order' => 20, 'is_active' => true]);
        $first = GymLocation::factory()->create(['name' => 'Pertama', 'sort_order' => 10, 'is_active' => true]);
        GymLocation::factory()->create(['name' => 'Nonaktif', 'sort_order' => 1, 'is_active' => false]);

        $this->assertSame([$first->id, $second->id], GymLocation::active()->ordered()->pluck('id')->all());
    }

    public function test_admin_can_create_a_gym_location_with_a_photo(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::bootCurrentPanel();
        Livewire::withoutLazyLoading();

        Livewire::test(ListGymLocations::class)
            ->callAction('create', [
                'name' => 'CukipGym · Dompak',
                'address' => 'Dompak, Tanjungpinang',
                'latitude' => 0.875,
                'longitude' => 104.45,
                'opening_hours' => '06:00 - 22:00',
                'whatsapp' => '6281234567890',
                'image_path' => UploadedFile::fake()->image('dompak.jpg', 1200, 900),
                'image_position' => 'center',
                'sort_order' => 7,
                'is_active' => true,
            ])
            ->assertHasNoFormErrors();

        $location = GymLocation::query()->where('name', 'CukipGym · Dompak')->firstOrFail();

        Storage::disk('public')->assertExists($location->image_path);
    }
}
