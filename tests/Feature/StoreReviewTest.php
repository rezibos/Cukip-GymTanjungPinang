<?php

namespace Tests\Feature;

use App\Models\MembershipPlan;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class StoreReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_must_log_in_and_cannot_store_a_review(): void
    {
        $this->post(route('reviews.store'), ['name' => 'Tamu', 'rating' => 5, 'review' => 'Tempatnya nyaman untuk latihan.'])
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('reviews', 0);
        $this->get(route('login'))->assertOk()->assertSee('CukipGym')->assertSee('Masuk akun')
            ->assertDontSee('<footer', false)->assertDontSee('id="menu-button"', false)
            ->assertDontSee('data-floating-actions', false)
            ->assertDontSee('gym-cable-row-editorial.webp');
        $this->get('/register')->assertNotFound();
        $this->get('/')->assertOk()->assertSee('Tulis review')->assertSee('Masuk untuk menulis review.')
            ->assertSee('Pilih membership')->assertDontSee('Buat akun');
    }

    #[TestWith(['pending_payment'])]
    #[TestWith(['cancelled'])]
    #[TestWith([null])]
    public function test_users_without_membership_experience_cannot_store_a_review(?string $status): void
    {
        $user = $status === null ? User::factory()->create() : $this->createMember($status);

        $this->actingAs($user)->post(route('reviews.store'), ['name' => 'Rani', 'rating' => 5, 'review' => 'Tempatnya nyaman untuk latihan.'])
            ->assertForbidden();

        $this->assertDatabaseCount('reviews', 0);
    }

    #[TestWith(['active'])]
    #[TestWith(['expired'])]
    public function test_members_can_save_their_name_rating_and_description(string $status): void
    {
        $user = $this->createMember($status);

        $response = $this->actingAs($user)->post(route('reviews.store'), [
            'name' => 'Rani Pinang', 'rating' => 4, 'review' => 'Area latihannya nyaman dan alatnya lengkap.',
        ]);

        $response->assertRedirect(route('home').'#write-review')->assertSessionHas('review_success');
        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id, 'name' => 'Rani Pinang', 'rating' => 4,
            'review' => 'Area latihannya nyaman dan alatnya lengkap.', 'status' => Review::STATUS_APPROVED,
        ]);
        $this->get('/')->assertOk()->assertSee('Rani Pinang')->assertSee('Perbarui review')->assertSee('Simpan perubahan');
    }

    public function test_resubmission_updates_only_the_current_users_review_and_preserves_moderation(): void
    {
        $user = $this->createMember();
        $otherUser = User::factory()->create();
        Review::factory()->for($user)->create(['name' => 'Nama lama', 'review' => 'Review lama milik saya.', 'status' => Review::STATUS_REJECTED]);
        $otherReview = Review::factory()->for($otherUser)->create(['review' => 'Review milik akun lain.']);

        $this->actingAs($user)->post(route('reviews.store'), [
            'name' => 'Nama baru', 'rating' => 3, 'review' => 'Review diperbarui dengan pengalaman terbaru.',
            'user_id' => $otherUser->id, 'status' => Review::STATUS_APPROVED,
        ])->assertRedirect(route('home').'#write-review');

        $this->assertDatabaseCount('reviews', 2);
        $this->assertDatabaseHas('reviews', ['user_id' => $user->id, 'name' => 'Nama baru', 'rating' => 3, 'status' => Review::STATUS_REJECTED]);
        $this->assertDatabaseHas('reviews', ['id' => $otherReview->id, 'user_id' => $otherUser->id, 'review' => 'Review milik akun lain.']);
        $this->assertSame('user', $user->fresh()->role);
    }

    #[DataProvider('invalidReviewData')]
    public function test_invalid_reviews_show_errors_without_writing_data(array $input, array $messages): void
    {
        $user = $this->createMember();

        $response = $this->actingAs($user)->post(route('reviews.store'), $input);

        $response->assertRedirect(route('home').'#write-review');
        $this->assertDatabaseCount('reviews', 0);
        $this->get('/')->assertOk()->assertSee(array_values($messages));
    }

    public static function invalidReviewData(): array
    {
        $valid = ['name' => 'Rani', 'rating' => 5, 'review' => 'Pengalaman latihan yang menyenangkan.'];

        return [
            'required fields' => [[], ['name' => 'Isi nama yang ingin ditampilkan.', 'rating' => 'Pilih jumlah bintang terlebih dahulu.', 'review' => 'Ceritakan pengalaman latihanmu.']],
            'name too long' => [array_replace($valid, ['name' => str_repeat('a', 101)]), ['name' => 'Nama maksimal 100 karakter.']],
            'name not text' => [array_replace($valid, ['name' => ['Rani']]), ['name' => 'Nama harus berupa teks.']],
            'rating below range' => [array_replace($valid, ['rating' => 0]), ['rating' => 'Pilih rating antara 1 sampai 5 bintang.']],
            'rating above range' => [array_replace($valid, ['rating' => 6]), ['rating' => 'Pilih rating antara 1 sampai 5 bintang.']],
            'rating fractional' => [array_replace($valid, ['rating' => 3.5]), ['rating' => 'Rating harus berupa angka bulat.']],
            'description too short' => [array_replace($valid, ['review' => 'Singkat']), ['review' => 'Deskripsi minimal 10 karakter.']],
            'description too long' => [array_replace($valid, ['review' => str_repeat('a', 2001)]), ['review' => 'Deskripsi maksimal 2.000 karakter.']],
            'description not text' => [array_replace($valid, ['review' => ['Latihan']]), ['review' => 'Deskripsi harus berupa teks.']],
        ];
    }

    public function test_stored_display_name_and_description_are_escaped_on_the_home_page(): void
    {
        $user = $this->createMember();
        $name = '<img src=x onerror=alert(1)>';
        $description = 'Latihan nyaman <script>alert(2)</script>';

        $this->actingAs($user)->post(route('reviews.store'), ['name' => $name, 'rating' => 5, 'review' => $description])
            ->assertRedirect();

        $this->get('/')->assertOk()->assertSee($name)->assertSee($description)
            ->assertDontSee($name, false)->assertDontSee($description, false);
    }

    public function test_review_submissions_are_rate_limited(): void
    {
        $user = $this->createMember();
        $this->actingAs($user);
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('reviews.store'), ['name' => 'Rani', 'rating' => 5, 'review' => 'Pengalaman latihan yang menyenangkan.'])->assertRedirect();
        }

        $this->post(route('reviews.store'), ['name' => 'Rani', 'rating' => 5, 'review' => 'Pengalaman latihan yang menyenangkan.'])->assertTooManyRequests();

        $this->assertDatabaseCount('reviews', 1);
    }

    private function createMember(string $status = 'active'): User
    {
        $user = User::factory()->create();
        $plan = MembershipPlan::factory()->create();
        $user->memberships()->create(['membership_plan_id' => $plan->id, 'status' => $status]);

        return $user;
    }
}
