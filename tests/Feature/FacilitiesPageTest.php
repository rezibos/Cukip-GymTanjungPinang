<?php

namespace Tests\Feature;

use App\Models\Facility;
use Database\Factories\GymProfileFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacilitiesPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_facilities_are_grouped_and_keep_their_saved_descriptions_and_opening_hours(): void
    {
        GymProfileFactory::new()->create(['opening_hours' => '07:00 - 21:00']);
        Facility::factory()->create(['name' => 'Barbell', 'description' => 'Barbell dengan variasi beban.']);
        Facility::factory()->create(['name' => 'Treadmill', 'description' => null]);
        Facility::factory()->create(['name' => 'Matras Yoga', 'description' => 'Matras untuk peregangan.']);
        Facility::factory()->create(['name' => 'Locker', 'description' => null]);

        $response = $this->get(route('facilities.index'));

        $response->assertOk()->assertSee('07:00 - 21:00')
            ->assertSeeInOrder(['id="fasilitas-kekuatan"', 'Barbell', 'Barbell dengan variasi beban.', 'id="fasilitas-kardio"', 'Treadmill', 'id="fasilitas-pemulihan"', 'Matras Yoga', 'id="fasilitas-kenyamanan"', 'Locker'], false)
            ->assertSee('Atur kecepatan langkahmu untuk sesi berjalan atau berlari.')
            ->assertSee('Tempat menyimpan barang agar kamu bisa lebih fokus selama latihan.');
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
        Facility::factory()->create(['name' => $name, 'description' => $description]);

        $response = $this->get(route('facilities.index'));

        $response->assertOk()->assertSee('id="fasilitas-kenyamanan"', false)
            ->assertSee($name)->assertSee($description)
            ->assertDontSee($name, false)->assertDontSee($description, false);
    }
}
