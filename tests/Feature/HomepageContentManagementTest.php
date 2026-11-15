<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageGymProfile;
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

    public function test_admin_can_update_about_gallery_footer_and_sponsors(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        Livewire::test(ManageGymProfile::class)
            ->fillForm([
                'about_eyebrow' => 'Tentang CukipGym',
                'about_location_label' => 'Tanjungpinang, Indonesia',
                'about_title_line_one' => 'Tempatmu',
                'about_title_line_two' => 'menjadi',
                'about_title_line_three' => 'lebih hebat.',
                'about_description_primary' => 'Paragraf pertama.',
                'about_description_secondary' => 'Paragraf kedua.',
                'about_gallery_eyebrow' => 'Ruang untuk progresmu',
                'about_gallery_title' => "Latihanmu.\nRitmemu.",
                'about_gallery_image_one' => UploadedFile::fake()->image('gallery-1.jpg'),
                'footer_description' => 'Footer terbaru.',
                'footer_location' => 'Gym lokal Tanjungpinang',
                'footer_sponsors' => [[
                    'name' => 'Sponsor Utama',
                    'tagline' => 'Partner latihan',
                    'logo' => [UploadedFile::fake()->image('sponsor.png')],
                    'url' => 'https://example.com',
                    'is_active' => true,
                ]],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $profile = GymProfile::current()->refresh();

        $this->assertSame('lebih hebat.', $profile->about_title_line_three);
        $this->assertSame('Sponsor Utama', $profile->footer_sponsors[0]['name']);
        Storage::disk('public')->assertExists($profile->about_gallery_image_one);
        Storage::disk('public')->assertExists($profile->footer_sponsors[0]['logo']);
    }

    public function test_homepage_and_footer_render_saved_content(): void
    {
        GymProfile::factory()->create([
            'hero_title_primary' => 'Gerak bersama.',
            'hero_title_secondary' => 'Tumbuh bersama.',
            'about_title_line_three' => 'lebih konsisten.',
            'footer_description' => 'Deskripsi footer khusus.',
            'footer_sponsors' => [
                ['name' => 'Sponsor Aktif', 'tagline' => null, 'logo' => null, 'url' => null, 'is_active' => true],
                ['name' => 'Sponsor Nonaktif', 'tagline' => null, 'logo' => null, 'url' => null, 'is_active' => false],
            ],
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeText('Gerak bersama.')
            ->assertSeeText('Tumbuh bersama.')
            ->assertSeeText('lebih konsisten.')
            ->assertSeeText('Deskripsi footer khusus.')
            ->assertSeeText('Sponsor Aktif')
            ->assertDontSeeText('Sponsor Nonaktif');
    }
}
