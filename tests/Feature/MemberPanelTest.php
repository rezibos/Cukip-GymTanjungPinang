<?php

namespace Tests\Feature;

use App\Filament\Member\Pages\AccountSettings;
use App\Models\GymClass;
use App\Models\MembershipPlan;
use App\Models\Payment;
use App\Models\PersonalTrainer;
use App\Models\TrainerSlot;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class MemberPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login_from_the_member_panel(): void
    {
        $this->get('/member')->assertRedirect('/member/login');
        $this->get('/member/login')->assertRedirect(route('login'));
    }

    public function test_admins_cannot_access_the_member_panel(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/member')->assertForbidden();
    }

    public function test_legacy_profile_route_redirects_members_to_the_member_panel(): void
    {
        $member = User::factory()->create(['role' => 'user']);

        $this->actingAs($member)
            ->get('/profile')
            ->assertRedirect('/member');
    }

    public function test_active_members_see_their_own_membership_and_active_access_code(): void
    {
        $this->travelTo(Carbon::parse('2026-11-14 10:00:00'));
        $member = User::factory()->create(['name' => 'Rani Member']);
        $otherMember = User::factory()->create();
        $plan = MembershipPlan::factory()->create(['name' => 'Regular Bulanan']);
        $otherPlan = MembershipPlan::factory()->create(['name' => 'Paket Rahasia']);
        $member->memberships()->create([
            'membership_plan_id' => $plan->id,
            'status' => 'active',
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
        ]);
        $otherMember->memberships()->create([
            'membership_plan_id' => $otherPlan->id,
            'status' => 'active',
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
        ]);

        $response = $this->actingAs($member)->get('/member');

        $response->assertOk()
            ->assertSee('Halo, Rani Member')
            ->assertSee('Regular Bulanan')
            ->assertSee('Status membership')
            ->assertSee('Aktif')
            ->assertSee('QR akses aktif')
            ->assertSee('30 November 2026')
            ->assertDontSee('Paket Rahasia');
    }

    public function test_member_dashboard_shows_personal_summary_and_upcoming_schedules(): void
    {
        $this->travelTo(Carbon::parse('2026-11-15 10:00:00'));
        $member = User::factory()->create(['name' => 'Rani Member']);
        $plan = MembershipPlan::factory()->create(['name' => '18 Month']);
        $member->memberships()->create([
            'membership_plan_id' => $plan->id,
            'status' => 'active',
            'start_date' => '2026-11-01',
            'end_date' => '2027-05-02',
        ]);

        $trainer = PersonalTrainer::factory()->create(['name' => 'Kak Rina']);
        $trainerSlot = TrainerSlot::factory()->for($trainer, 'trainer')->create([
            'starts_at' => '2026-11-16 07:00:00',
            'ends_at' => '2026-11-16 09:00:00',
            'status' => 'booked',
        ]);
        $payment = Payment::factory()->for($member)->create(['status' => 'paid']);
        $payment->trainerBookings()->create([
            'user_id' => $member->id,
            'trainer_slot_id' => $trainerSlot->id,
            'amount' => 150000,
            'status' => 'confirmed',
        ]);

        $gymClass = GymClass::factory()->create([
            'name' => 'Yoga Pagi',
            'instructor' => 'Kak Sinta',
            'start_time' => '07:00',
            'end_time' => '08:00',
            'capacity' => 15,
        ]);
        $classSession = $gymClass->sessions()->create([
            'date' => '2026-11-16',
            'capacity' => 15,
        ]);
        $classBooking = $member->classBookings()->create([
            'class_session_id' => $classSession->id,
            'status' => 'booked',
        ]);

        $this->actingAs($member)->get('/member')
            ->assertOk()
            ->assertSee('class="member-personal-dashboard"', false)
            ->assertSee('class="fi-breadcrumbs"', false)
            ->assertSeeInOrder(['CukipGym', 'Dashboard member'])
            ->assertDontSee('CukipGym / Dashboard member')
            ->assertSee('Ringkasan personal.')
            ->assertSee('Sisa masa membership')
            ->assertSee('Sesi personal trainer')
            ->assertSee('Booking kelas mendatang')
            ->assertSee('Booking kelas bulan ini')
            ->assertSee('Halo, Rani Member.')
            ->assertSee('Kak Rina')
            ->assertSee('Yoga Pagi')
            ->assertSee('data-member-class-cancel-form', false)
            ->assertSee('aria-label="Batalkan booking Yoga Pagi"', false)
            ->assertSee('action="'.route('classes.bookings.cancel', $classBooking).'"', false)
            ->assertSee('member-schedule-carousel', false)
            ->assertSee('member-schedule-arrow is-previous', false)
            ->assertSee('member-schedule-arrow is-next', false)
            ->assertSee('x-on:scroll.debounce.100ms="syncArrows()"', false)
            ->assertSee('Atur sesimu')
            ->assertSee('Atur kelasmu');
    }

    public function test_qr_access_page_uses_the_split_access_layout(): void
    {
        $this->travelTo(Carbon::parse('2026-11-15 10:00:00'));
        $member = User::factory()->create();
        $plan = MembershipPlan::factory()->create(['name' => '18 Month']);
        $member->memberships()->create([
            'membership_plan_id' => $plan->id,
            'status' => 'active',
            'start_date' => '2026-11-01',
            'end_date' => '2027-05-07',
        ]);

        $this->actingAs($member)
            ->get('/member/qr-access')
            ->assertOk()
            ->assertSee('class="member-qr-access-page"', false)
            ->assertSee('class="fi-breadcrumbs"', false)
            ->assertSeeInOrder(['CukipGym', 'QR akses'])
            ->assertDontSee('CukipGym / Dashboard member')
            ->assertSee('QR akses')
            ->assertSee('Tunjukkan QR ini saat masuk.')
            ->assertSee('Informasi akses')
            ->assertSee('Membership aktif')
            ->assertSee('18 Month')
            ->assertSee('Berlaku sampai 07 Mei 2027')
            ->assertSee('Perpanjang membership');
    }

    public function test_my_classes_page_uses_the_booked_class_schedule_layout(): void
    {
        $this->travelTo(Carbon::parse('2026-11-15 10:00:00'));
        $member = User::factory()->create(['name' => 'Rani Member']);
        $plan = MembershipPlan::factory()->create();
        $member->memberships()->create([
            'membership_plan_id' => $plan->id,
            'status' => 'active',
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
        ]);
        $gymClass = GymClass::factory()->create([
            'name' => 'Yoga Pagi',
            'instructor' => 'Kak Rina',
            'start_time' => '07:00',
            'end_time' => '08:00',
            'capacity' => 15,
        ]);
        $session = $gymClass->sessions()->create([
            'date' => '2026-11-16',
            'capacity' => 15,
        ]);
        $booking = $member->classBookings()->create([
            'class_session_id' => $session->id,
            'status' => 'booked',
        ]);

        $this->actingAs($member)
            ->get('/member/my-classes')
            ->assertOk()
            ->assertSee('class="member-classes-page"', false)
            ->assertSee('class="fi-breadcrumbs"', false)
            ->assertSeeInOrder(['CukipGym', 'Kelas'])
            ->assertSee('Daftar kelas mendatang yang sudah kamu booking.')
            ->assertSee('Menu ini hanya tersedia saat membership aktif.')
            ->assertSee('class="member-classes-summary-heading"', false)
            ->assertSee('class="member-class-filter-select"', false)
            ->assertSee('Semua jadwal')
            ->assertSee('Pilih tanggal')
            ->assertSee('Senin, 16 Nov')
            ->assertSee('Tambah kelas')
            ->assertSee('class="member-booked-class-card"', false)
            ->assertSee('class="member-booked-class-date"', false)
            ->assertSee('class="member-booked-class-status"', false)
            ->assertSee('class="member-booked-class-meta"', false)
            ->assertSee('class="member-booked-class-actions"', false)
            ->assertSee('Yoga Pagi')
            ->assertSee('Kak Rina')
            ->assertSee('15 kapasitas')
            ->assertSee('data-member-class-cancel-form', false)
            ->assertSee('action="'.route('classes.bookings.cancel', $booking).'"', false);
    }

    public function test_personal_trainer_page_is_forbidden_until_member_has_a_confirmed_purchase(): void
    {
        $member = User::factory()->create();
        $slot = TrainerSlot::factory()->create([
            'starts_at' => now()->addDay()->setTime(9, 0),
            'ends_at' => now()->addDay()->setTime(12, 0),
            'status' => 'held',
        ]);
        $payment = Payment::factory()->for($member)->create(['status' => 'pending']);
        $payment->trainerBookings()->create([
            'user_id' => $member->id,
            'trainer_slot_id' => $slot->id,
            'amount' => 150000,
            'status' => 'pending_payment',
        ]);

        $this->actingAs($member)
            ->get('/member/personal-trainer')
            ->assertForbidden();
    }

    public function test_personal_trainer_navigation_is_hidden_without_a_confirmed_purchase(): void
    {
        $member = User::factory()->create();
        $slot = TrainerSlot::factory()->create([
            'starts_at' => now()->addDay()->setTime(9, 0),
            'ends_at' => now()->addDay()->setTime(12, 0),
            'status' => 'held',
        ]);
        $payment = Payment::factory()->for($member)->create(['status' => 'pending']);
        $payment->trainerBookings()->create([
            'user_id' => $member->id,
            'trainer_slot_id' => $slot->id,
            'amount' => 150000,
            'status' => 'pending_payment',
        ]);

        $this->actingAs($member)
            ->get('/member')
            ->assertOk()
            ->assertDontSee('href="'.url('/member/personal-trainer').'"', false);
    }

    public function test_confirmed_trainer_buyer_sees_personal_trainer_navigation(): void
    {
        $member = User::factory()->create();
        $slot = TrainerSlot::factory()->create([
            'starts_at' => now()->addDay()->setTime(9, 0),
            'ends_at' => now()->addDay()->setTime(12, 0),
            'status' => 'booked',
        ]);
        $payment = Payment::factory()->for($member)->create(['status' => 'paid']);
        $payment->trainerBookings()->create([
            'user_id' => $member->id,
            'trainer_slot_id' => $slot->id,
            'amount' => 150000,
            'status' => 'confirmed',
        ]);

        $this->actingAs($member)
            ->get('/member')
            ->assertOk()
            ->assertSee('href="'.url('/member/personal-trainer').'"', false)
            ->assertSee('Personal Trainer');
    }

    public function test_personal_trainer_page_shows_only_own_upcoming_confirmed_sessions(): void
    {
        $this->travelTo(Carbon::parse('2026-11-15 10:00:00'));
        $member = User::factory()->create();
        $otherMember = User::factory()->create();
        $trainer = PersonalTrainer::factory()->create([
            'name' => 'Kak Bayu',
            'specialization' => 'Functional Training',
            'session_minutes' => 180,
        ]);
        $otherTrainer = PersonalTrainer::factory()->create(['name' => 'Kak Rahasia']);
        $upcomingSlot = TrainerSlot::factory()->for($trainer, 'trainer')->create([
            'starts_at' => '2026-11-18 09:00:00',
            'ends_at' => '2026-11-18 12:00:00',
            'status' => 'booked',
        ]);
        $pastSlot = TrainerSlot::factory()->for($trainer, 'trainer')->create([
            'starts_at' => '2026-11-14 09:00:00',
            'ends_at' => '2026-11-14 12:00:00',
            'status' => 'booked',
        ]);
        $otherSlot = TrainerSlot::factory()->for($otherTrainer, 'trainer')->create([
            'starts_at' => '2026-11-19 09:00:00',
            'ends_at' => '2026-11-19 12:00:00',
            'status' => 'booked',
        ]);
        $payment = Payment::factory()->for($member)->create(['status' => 'paid']);
        $otherPayment = Payment::factory()->for($otherMember)->create(['status' => 'paid']);

        foreach ([$upcomingSlot, $pastSlot] as $slot) {
            $payment->trainerBookings()->create([
                'user_id' => $member->id,
                'trainer_slot_id' => $slot->id,
                'amount' => 150000,
                'status' => 'confirmed',
            ]);
        }
        $otherPayment->trainerBookings()->create([
            'user_id' => $otherMember->id,
            'trainer_slot_id' => $otherSlot->id,
            'amount' => 150000,
            'status' => 'confirmed',
        ]);

        $this->actingAs($member)
            ->get('/member/personal-trainer')
            ->assertOk()
            ->assertSee('class="member-trainer-page member-classes-page"', false)
            ->assertSee('class="member-class-filter-select member-trainer-filter-select"', false)
            ->assertSee('Semua jadwal')
            ->assertSee('Pilih tanggal')
            ->assertSee('Rabu, 18 Nov')
            ->assertSee('Kak Bayu')
            ->assertSee('Functional Training')
            ->assertSee('09:00 – 12:00')
            ->assertSee('Rp150.000')
            ->assertDontSee('Kak Rahasia')
            ->assertDontSee('Sabtu, 14 Nov');
    }

    public function test_transaction_history_lists_membership_and_personal_trainer_payments_owned_by_member(): void
    {
        $this->travelTo(Carbon::parse('2026-11-18 10:00:00'));
        $member = User::factory()->create();
        $otherMember = User::factory()->create();
        $plan = MembershipPlan::factory()->create(['name' => '6 Month']);
        $membership = $member->memberships()->create([
            'membership_plan_id' => $plan->id,
            'status' => 'active',
            'start_date' => '2026-11-18',
            'end_date' => '2027-05-17',
        ]);
        $membershipPayment = Payment::factory()->for($member)->create([
            'order_id' => 'GYMPINANG-MEMBER-001',
            'gross_amount' => 2400000,
            'status' => 'paid',
            'paid_at' => now(),
        ]);
        $membership->transactions()->create([
            'user_id' => $member->id,
            'payment_id' => $membershipPayment->id,
            'amount' => 2400000,
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $trainer = PersonalTrainer::factory()->create(['name' => 'Kak Bayu']);
        $slot = TrainerSlot::factory()->for($trainer, 'trainer')->create([
            'starts_at' => '2026-11-20 09:00:00',
            'status' => 'booked',
        ]);
        $trainerPayment = Payment::factory()->for($member)->create([
            'order_id' => 'TRAINER-MEMBER-001',
            'gross_amount' => 150000,
            'status' => 'paid',
            'paid_at' => now(),
        ]);
        $trainerPayment->trainerBookings()->create([
            'user_id' => $member->id,
            'trainer_slot_id' => $slot->id,
            'amount' => 150000,
            'status' => 'confirmed',
        ]);
        Payment::factory()->for($otherMember)->create([
            'order_id' => 'GYMPINANG-SECRET-OTHER',
            'checkout_items' => [['name' => 'Paket Rahasia', 'quantity' => 1]],
            'status' => 'paid',
        ]);

        $this->actingAs($member)
            ->get('/member/transactions')
            ->assertOk()
            ->assertSee('class="member-transaction-history"', false)
            ->assertSee('Cari transaksi')
            ->assertSee('6 Month')
            ->assertSee('Personal Trainer · Kak Bayu')
            ->assertSee('Rp2.400.000')
            ->assertSee('Rp150.000')
            ->assertDontSee('Paket Rahasia')
            ->assertDontSee('GYMPINANG-SECRET-OTHER');
    }

    public function test_transaction_history_can_filter_personal_trainer_payments(): void
    {
        $member = User::factory()->create();
        Payment::factory()->for($member)->create([
            'order_id' => 'GYMPINANG-MEMBERSHIP-SEARCH',
            'checkout_items' => [['name' => 'Membership Tahunan', 'quantity' => 1]],
            'status' => 'paid',
        ]);
        $trainer = PersonalTrainer::factory()->create(['name' => 'Kak Sinta']);
        $slot = TrainerSlot::factory()->for($trainer, 'trainer')->create();
        $trainerPayment = Payment::factory()->for($member)->create([
            'order_id' => 'TRAINER-SEARCH-001',
            'gross_amount' => 150000,
            'status' => 'paid',
        ]);
        $trainerPayment->trainerBookings()->create([
            'user_id' => $member->id,
            'trainer_slot_id' => $slot->id,
            'amount' => 150000,
            'status' => 'confirmed',
        ]);

        $this->actingAs($member)
            ->get('/member/transactions?search=Sinta&type=trainer&status=paid')
            ->assertOk()
            ->assertSee('Personal Trainer · Kak Sinta')
            ->assertDontSee('Membership Tahunan')
            ->assertDontSee('GYMPINANG-MEMBERSHIP-SEARCH');
    }

    public function test_member_panel_renders_redirect_feedback_for_sweetalert(): void
    {
        $member = $this->signInMember();

        $this->withSession(['success' => 'Booking berhasil dibatalkan.'])
            ->actingAs($member)
            ->get('/member')
            ->assertOk()
            ->assertSee('data-filament-flash', false)
            ->assertSee('Booking berhasil dibatalkan.');
    }

    public function test_expired_members_see_limited_dashboard_and_renewal_action(): void
    {
        $this->travelTo(Carbon::parse('2026-11-14 10:00:00'));
        $member = User::factory()->create();
        $plan = MembershipPlan::factory()->create(['name' => 'Paket Lama']);
        $member->memberships()->create([
            'membership_plan_id' => $plan->id,
            'status' => 'expired',
            'start_date' => '2026-10-01',
            'end_date' => '2026-10-31',
        ]);

        $response = $this->actingAs($member)->get('/member');

        $response->assertOk()
            ->assertSee('Tidak aktif')
            ->assertSee('QR akses dikunci')
            ->assertSee('Perpanjang membership')
            ->assertSee(route('membership.index'), false)
            ->assertSee('QR akses pintu');

        $this->get('/member/my-classes')->assertForbidden();
        $this->get('/member/transactions')->assertOk();
        $this->get('/member/account-settings')->assertOk();
    }

    public function test_members_can_update_name_whatsapp_and_password(): void
    {
        $member = $this->signInMember();
        $originalEmail = $member->email;

        Livewire::test(AccountSettings::class)
            ->fillForm([
                'name' => 'Nama Member Baru',
                'phone' => '6281234567890',
                'password' => 'Password-Baru-123',
                'passwordConfirmation' => 'Password-Baru-123',
                'currentPassword' => 'password',
            ])
            ->set('data.email', 'email-tidak-boleh-berubah@example.com')
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertDispatched('filament-sweetalert')
            ->assertNotNotified();

        $member->refresh();

        $this->assertSame('Nama Member Baru', $member->name);
        $this->assertSame('6281234567890', $member->phone);
        $this->assertSame($originalEmail, $member->email);
        $this->assertTrue(Hash::check('Password-Baru-123', $member->password));
    }

    public function test_member_dashboard_escapes_member_and_membership_names(): void
    {
        $this->travelTo(Carbon::parse('2026-11-14 10:00:00'));
        $memberName = '<script>alert("member")</script>';
        $planName = '<img src=x onerror=alert(1)>';
        $member = User::factory()->create(['name' => $memberName]);
        $plan = MembershipPlan::factory()->create(['name' => $planName]);
        $member->memberships()->create([
            'membership_plan_id' => $plan->id,
            'status' => 'active',
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
        ]);

        $response = $this->actingAs($member)->get('/member');

        $response->assertOk()
            ->assertSee($memberName)
            ->assertSee($planName)
            ->assertDontSee($memberName, false)
            ->assertDontSee($planName, false);
    }

    public function test_member_settings_reject_an_incorrect_current_password(): void
    {
        $member = $this->signInMember();

        Livewire::test(AccountSettings::class)
            ->fillForm([
                'name' => 'Nama Tidak Boleh Tersimpan',
                'phone' => '6281234567890',
                'password' => 'Password-Baru-123',
                'passwordConfirmation' => 'Password-Baru-123',
                'currentPassword' => 'password-yang-salah',
            ])
            ->call('save')
            ->assertHasFormErrors(['currentPassword']);

        $this->assertNotSame('Nama Tidak Boleh Tersimpan', $member->fresh()->name);
        $this->assertTrue(Hash::check('password', $member->fresh()->password));
    }

    private function signInMember(): User
    {
        $member = User::factory()->create(['role' => 'user']);
        $this->actingAs($member);
        Filament::setCurrentPanel(Filament::getPanel('member'));
        Filament::bootCurrentPanel();

        return $member;
    }
}
