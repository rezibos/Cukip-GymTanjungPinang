<?php

namespace Tests\Feature;

use App\Models\GymClass;
use App\Models\MembershipPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class ProfileDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_the_profile_dashboard(): void
    {
        $this->get(route('profile'))->assertRedirect(route('login'));
    }

    public function test_new_accounts_see_their_details_and_helpful_empty_states(): void
    {
        $user = User::factory()->create(['name' => 'Rani Pinang', 'phone' => null]);

        $response = $this->actingAs($user)->get(route('profile'));

        $response->assertOk()->assertSee('Rani Pinang')->assertSee($user->email)
            ->assertSee('Belum ditambahkan')->assertSee('Belum aktif')
            ->assertSee('Kamu belum memiliki membership aktif.')
            ->assertSee('Jadwalmu masih kosong.')->assertSee('Belum ada transaksi.')
            ->assertSee(route('membership.index'), false);
    }

    public function test_members_see_only_their_own_membership_invoices_and_upcoming_bookings(): void
    {
        $this->travelTo(Carbon::parse('2026-11-08 10:00:00'));
        $user = User::factory()->create();
        $otherUser = User::factory()->create(['email' => 'akun-lain@example.test']);
        $plan = MembershipPlan::factory()->create(['name' => 'Paket Saya']);
        $membership = $user->memberships()->create(['membership_plan_id' => $plan->id, 'status' => 'active', 'start_date' => '2026-11-01', 'end_date' => '2026-11-30']);
        $transaction = $user->transactions()->create(['membership_id' => $membership->id, 'invoice_number' => 'INV-MILIK-SAYA', 'amount' => 250000, 'status' => 'paid']);
        $otherPlan = MembershipPlan::factory()->create(['name' => 'Paket Rahasia Akun Lain']);
        $otherMembership = $otherUser->memberships()->create(['membership_plan_id' => $otherPlan->id, 'status' => 'active', 'end_date' => '2026-11-30']);
        $otherUser->transactions()->create(['membership_id' => $otherMembership->id, 'invoice_number' => 'INV-RAHASIA-AKUN-LAIN', 'amount' => 500000, 'status' => 'paid']);
        $class = GymClass::factory()->create(['name' => 'Kelas Saya']);
        $session = $class->sessions()->create(['date' => '2026-11-09', 'capacity' => 10]);
        $booking = $user->classBookings()->create(['class_session_id' => $session->id, 'status' => 'booked']);
        $pastClass = GymClass::factory()->create(['name' => 'Kelas Sudah Lewat']);
        $pastSession = $pastClass->sessions()->create(['date' => '2026-11-07', 'capacity' => 10]);
        $user->classBookings()->create(['class_session_id' => $pastSession->id, 'status' => 'booked']);
        $privateClass = GymClass::factory()->create(['name' => 'Kelas Rahasia Akun Lain']);
        $privateSession = $privateClass->sessions()->create(['date' => '2026-11-10', 'capacity' => 10]);
        $otherUser->classBookings()->create(['class_session_id' => $privateSession->id, 'status' => 'booked']);

        $response = $this->actingAs($user)->get(route('profile'));

        $response->assertOk()->assertSee('Paket Saya')->assertSee('30 Nov 2026')
            ->assertSee('INV-MILIK-SAYA')->assertSee('Rp250.000')->assertSee('Lunas')->assertSee('Kelas Saya')
            ->assertSee(route('membership.invoice', $transaction), false)
            ->assertSee('action="'.route('classes.bookings.cancel', $booking).'"', false)
            ->assertDontSee('akun-lain@example.test')->assertDontSee('Paket Rahasia Akun Lain')
            ->assertDontSee('INV-RAHASIA-AKUN-LAIN')->assertDontSee('Kelas Rahasia Akun Lain')->assertDontSee('Kelas Sudah Lewat');
    }

    #[TestWith([null, 'Lanjutkan pembayaran'])]
    #[TestWith(['payment-proofs/contoh.jpg', 'Lihat pembayaran'])]
    public function test_pending_payments_offer_the_appropriate_next_step(?string $proof, string $action): void
    {
        $user = User::factory()->create();
        $plan = MembershipPlan::factory()->create();
        $membership = $user->memberships()->create(['membership_plan_id' => $plan->id, 'status' => 'pending_payment']);
        $transaction = $user->transactions()->create(['membership_id' => $membership->id, 'invoice_number' => 'INV-PENDING-SAYA', 'amount' => 250000, 'status' => 'pending', 'payment_proof' => $proof]);

        $response = $this->actingAs($user)->get(route('profile'));

        $response->assertOk()->assertSee('Dalam proses')->assertSee($action)
            ->assertSee(route('membership.invoice', $transaction), false)
            ->assertSee('Menunggu Konfirmasi');
    }

    public function test_cancelled_bookings_do_not_appear_in_the_dashboard(): void
    {
        $this->travelTo(Carbon::parse('2026-11-08 10:00:00'));
        $user = User::factory()->create();
        $class = GymClass::factory()->create(['name' => 'Kelas Dibatalkan']);
        $session = $class->sessions()->create(['date' => '2026-11-09', 'capacity' => 10]);
        $user->classBookings()->create(['class_session_id' => $session->id, 'status' => 'cancelled']);

        $response = $this->actingAs($user)->get(route('profile'));

        $response->assertOk()->assertSee('Jadwalmu masih kosong.')->assertDontSee('Kelas Dibatalkan');
    }

    public function test_profile_and_class_content_is_escaped(): void
    {
        $this->travelTo(Carbon::parse('2026-11-08 10:00:00'));
        $name = '<script>alert("name")</script>';
        $className = '<img src=x onerror=alert(1)>';
        $user = User::factory()->create(['name' => $name]);
        $class = GymClass::factory()->create(['name' => $className]);
        $session = $class->sessions()->create(['date' => '2026-11-09', 'capacity' => 10]);
        $user->classBookings()->create(['class_session_id' => $session->id, 'status' => 'booked']);

        $response = $this->actingAs($user)->get(route('profile'));

        $response->assertOk()->assertSee($name)->assertSee($className)
            ->assertDontSee($name, false)->assertDontSee($className, false);
    }
}
