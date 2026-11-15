<?php

namespace Tests\Feature;

use App\Models\GymProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
