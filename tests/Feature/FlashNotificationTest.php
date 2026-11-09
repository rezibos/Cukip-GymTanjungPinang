<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class FlashNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_logout_signs_the_member_out_and_displays_a_friendly_notification(): void
    {
        $member = User::factory()->create();

        $response = $this->actingAs($member)->post(route('logout'));

        $response->assertRedirect(route('home'))
            ->assertSessionHas('notification_title', 'Sampai jumpa!')
            ->assertSessionHas('success', 'Kamu berhasil keluar.');
        $this->assertGuest();

        $this->get(route('home'))->assertOk()
            ->assertSee('data-flash-notification', false)
            ->assertSee('Sampai jumpa!')
            ->assertSee('Kamu berhasil keluar.')
            ->assertDontSee('Anda sudah logout.');
    }

    #[TestWith(['success', 'Berhasil'])]
    #[TestWith(['error', 'Ada kendala'])]
    public function test_flash_notifications_escape_messages_and_titles(string $type, string $title): void
    {
        $message = 'Pesan <script>alert("notification")</script>';

        $response = $this->withSession([$type => $message])->get(route('login'));

        $response->assertOk()
            ->assertSee('data-notification-icon="'.$type.'"', false)
            ->assertSee('data-notification-title="'.$title.'"', false)
            ->assertSee($message)
            ->assertDontSee('<script>alert("notification")</script>', false);
    }

    public function test_custom_notification_titles_are_escaped(): void
    {
        $title = '"><img src=x onerror=alert(1)>';

        $response = $this->withSession(['success' => 'Tersimpan.', 'notification_title' => $title])
            ->get(route('login'));

        $response->assertOk()->assertSee($title)->assertDontSee('<img src=x', false);
    }

    public function test_pages_without_flash_messages_do_not_render_a_notification(): void
    {
        $this->get(route('login'))->assertOk()->assertDontSee('data-flash-notification', false);
    }
}
