<?php

namespace Tests\Feature;

use App\Enums\FacilityCategory;
use App\Models\Facility;
use Database\Factories\GymProfileFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FacilitiesPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_facilities_are_grouped_and_only_render_saved_descriptions(): void
    {
        GymProfileFactory::new()->create(['opening_hours' => '07:00 - 21:00']);
        Facility::factory()->create(['name' => 'Barbell', 'category' => FacilityCategory::Strength, 'description' => 'Barbell dengan variasi beban.']);
        Facility::factory()->create(['name' => 'Treadmill', 'category' => FacilityCategory::Cardio, 'description' => null]);
        Facility::factory()->create(['name' => 'Matras Yoga', 'category' => FacilityCategory::Recovery, 'description' => 'Matras untuk peregangan.']);
        Facility::factory()->create(['name' => 'Locker', 'category' => FacilityCategory::Support, 'description' => null]);

        $response = $this->get(route('facilities.index'));

        $response->assertOk()->assertSee('07:00 - 21:00')
            ->assertSeeInOrder(['id="fasilitas-kekuatan"', 'Barbell', 'Barbell dengan variasi beban.', 'id="fasilitas-kardio"', 'Treadmill', 'id="fasilitas-pemulihan"', 'Matras Yoga', 'id="fasilitas-kenyamanan"', 'Locker'], false)
            ->assertDontSee('Atur kecepatan langkahmu untuk sesi berjalan atau berlari.')
            ->assertDontSee('Tempat menyimpan barang agar kamu bisa lebih fokus selama latihan.');
    }

    public function test_uploaded_facility_images_render_as_hover_previews_without_replacing_cards(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('facilities/cable-machine.jpg', 'image-content');
        Facility::factory()->create([
            'name' => 'Cable Machine',
            'category' => FacilityCategory::Strength,
            'image_path' => 'facilities/cable-machine.jpg',
        ]);

        $response = $this->get(route('facilities.index'));

        $response->assertOk()
            ->assertSee('Cable Machine')
            ->assertSee('data-facility-with-image', false)
            ->assertSee('data-facility-image-preview', false)
            ->assertSee(Storage::disk('public')->url('facilities/cable-machine.jpg'), false);
    }

    public function test_an_empty_catalog_shows_a_message_without_empty_categories(): void
    {
        $response = $this->get(route('facilities.index'));

        $response->assertOk()->assertSee('Daftar fasilitas sedang diperbarui.')
            ->assertDontSee('id="fasilitas-kekuatan"', false)
            ->assertDontSee('id="fasilitas-kardio"', false)
            ->assertDontSee('id="fasilitas-pemulihan"', false)
            ->assertDontSee('id="fasilitas-kenyamanan"', false);
    }

    public function test_unknown_facilities_are_kept_and_user_supplied_content_is_escaped(): void
    {
        $name = 'Peralatan <script>alert(1)</script>';
        $description = '<img src=x onerror=alert(2)>';
        Facility::factory()->create(['name' => $name, 'category' => FacilityCategory::Support, 'description' => $description]);

        $response = $this->get(route('facilities.index'));

        $response->assertOk()->assertSee('id="fasilitas-kenyamanan"', false)
            ->assertSee($name)->assertSee($description)
            ->assertDontSee($name, false)->assertDontSee($description, false);
    }
}
