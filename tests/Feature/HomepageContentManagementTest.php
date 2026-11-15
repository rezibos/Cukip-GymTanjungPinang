<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageHomepage;
use App\Models\GymProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class HomepageContentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_uses_homepage_fallbacks_when_optional_content_is_empty(): void
    {
        $profile = GymProfile::factory()->create([
            'hero_title_primary' => null,
            'hero_image' => null,
            'footer_sponsors' => null,
        ]);

        $this->assertSame('Bukan cuma.', $profile->content('hero_title_primary'));
        $this->assertStringEndsWith('/images/gympinang-hero-color.png', $profile->contentImageUrl('hero_image'));
        $this->assertCount(4, $profile->footerSponsors());
    }

    public function test_admin_can_update_homepage_hero_content(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        Livewire::test(ManageHomepage::class)
            ->fillForm([
                'hero_eyebrow' => 'Gym lokal pilihan',
                'hero_title_primary' => 'Bergerak lebih.',
                'hero_title_secondary' => 'Kuat setiap hari.',
                'hero_description' => 'Deskripsi hero terbaru.',
                'hero_cta_label' => 'Mulai latihan',
                'hero_about_label' => 'Kenali CukipGym',
                'hero_image' => UploadedFile::fake()->image('hero.jpg', 1600, 1000),
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $profile = GymProfile::current()->refresh();

        $this->assertSame('Bergerak lebih.', $profile->hero_title_primary);
        Storage::disk('public')->assertExists($profile->hero_image);
    }

    public function test_homepage_management_is_only_available_to_admins(): void
    {
        $this->get('/admin/manage-homepage')->assertRedirect('/admin/login');

        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get('/admin/manage-homepage')->assertForbidden();
    }
}
