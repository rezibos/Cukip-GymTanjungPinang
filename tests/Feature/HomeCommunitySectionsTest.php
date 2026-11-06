<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeCommunitySectionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_approved_reviews_are_visible_after_the_map_before_the_faq_and_rejected_reviews_stay_private(): void
    {
        Review::factory()->for(User::factory()->state(['name' => 'Rani Member']))->create([
            'rating' => 4,
            'review' => 'Latihan nyaman <script>alert("review")</script>',
            'status' => Review::STATUS_APPROVED,
        ]);
        Review::factory()->for(User::factory())->create([
            'review' => 'Komentar yang belum boleh tampil',
            'status' => Review::STATUS_REJECTED,
        ]);

        $response = $this->get('/');

        $response->assertOk()
            ->assertSeeInOrder(['Tempat latihanmu.', 'Kata mereka.', 'Rani Member', 'Ada pertanyaan?'])
            ->assertSee('Latihan nyaman <script>alert("review")</script>')
            ->assertDontSee('<script>alert("review")</script>', false)
            ->assertDontSee('Komentar yang belum boleh tampil')
            ->assertDontSee('Contoh review');
    }

    public function test_empty_reviews_and_sponsors_are_clearly_presented_as_examples(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSeeInOrder(['Calculate Your BMI Now', 'Orang nyata.', 'Kata mereka.', 'Ada pertanyaan?', '<footer', 'Sponsor &amp; partner'], false)
            ->assertSee('Identitas contoh')
            ->assertSee('Contoh review untuk pratinjau tampilan.')
            ->assertSee('Raka')
            ->assertSee('Nadia')
            ->assertSee('Dimas');
    }
}
