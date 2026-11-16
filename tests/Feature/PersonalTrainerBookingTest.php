<?php

namespace Tests\Feature;

use App\Contracts\PaymentGateway;
use App\Models\PersonalTrainer;
use App\Models\TrainerSlot;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class PersonalTrainerBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_classes_page_shows_active_trainers_and_available_slots(): void
    {
        $trainer = PersonalTrainer::factory()->create([
            'name' => 'Kak Bayu',
            'specialization' => 'Functional Training',
            'description' => 'Pendampingan penuh untuk menyusun latihan, memperbaiki teknik, dan mengevaluasi progres.',
            'certifications' => ['Certified Personal Trainer'],
            'certification_documents' => ['personal-trainers/certificates/certified.jpg'],
            'session_minutes' => 180,
        ]);
        TrainerSlot::factory()->for($trainer, 'trainer')->create([
            'starts_at' => now()->addDays(2)->setTime(9, 0),
            'ends_at' => now()->addDays(2)->setTime(12, 0),
        ]);

        $this->actingAs(User::factory()->create())->get(route('classes.index'))
            ->assertOk()
            ->assertSee('Temukan pelatih.')
            ->assertSee('Maksimalkan potensi.')
            ->assertSee('id="personal-trainer" class="bg-white', false)
            ->assertSee('<span class="text-[#858585]">Maksimalkan potensi.</span>', false)
            ->assertSee('leading-[1.04]', false)
            ->assertDontSee('#9a6f50', false)
            ->assertSee('Kak Bayu')
            ->assertSee('Functional Training')
            ->assertSee('Pendampingan penuh untuk menyusun latihan, memperbaiki teknik, dan mengevaluasi progres.')
            ->assertSee('Certified Personal Trainer')
            ->assertSee('Sertifikat & lisensi', false)
            ->assertSee('Lihat dokumen')
            ->assertSee('personal-trainers/certificates/certified.jpg', false)
            ->assertSee('data-trainer-card-open', false)
            ->assertDontSee('grayscale', false)
            ->assertSee('data-trainer-date-button', false)
            ->assertSee('data-trainer-date-panel', false)
            ->assertSee('data-trainer-certification', false)
            ->assertSee('data-trainer-certificate-document', false)
            ->assertSee('data-trainer-booking-summary', false)
            ->assertSee('data-trainer-submit', false)
            ->assertSee('data-trainer-cancel-payment', false)
            ->assertSee('data-trainer-dialog-scroll', false)
            ->assertSee('app.sandbox.midtrans.com/snap/snap.js', false)
            ->assertSee('window.snap.pay', false)
            ->assertSee('gym:notification', false)
            ->assertSee("showNotification('Pembayaran dijeda'", false)
            ->assertSee("'info', trainerDialog", false)
            ->assertSee('trainerDialog?.close()', false)
            ->assertSee('pageScrollPosition', false)
            ->assertSee('lockPageScroll', false)
            ->assertSee('unlockPageScroll', false)
            ->assertSee('lg:grid-cols-5', false)
            ->assertSee('lg:col-span-2', false)
            ->assertSee('lg:col-span-3', false)
            ->assertSee('3 jam')
            ->assertSee('Harga per sesi')
            ->assertSee('Tanggal dan jam sesi')
            ->assertSee('09:00 – 12:00')
            ->assertDontSee('max-h-32 overflow-y-auto rounded-xl bg-[#f5f5f5]', false)
            ->assertSee('Booking dan bayar');
    }

    public function test_guest_must_login_before_booking_a_private_session(): void
    {
        $slot = TrainerSlot::factory()->create();

        $this->post(route('trainer-bookings.store'), ['slot_ids' => [$slot->id]])
            ->assertRedirect(route('login'));
    }

    public function test_booked_dates_remain_visible_but_cannot_be_selected(): void
    {
        $slot = TrainerSlot::factory()->create(['status' => 'booked']);

        $this->actingAs(User::factory()->create())->get(route('classes.index'))
            ->assertOk()
            ->assertSee('disabled title="Jadwal ini tidak tersedia" data-trainer-date-booked', false)
            ->assertSee('Tidak tersedia')
            ->assertDontSee('Sudah kamu booking')
            ->assertDontSee('name="slot_ids[]" value="'.$slot->id.'"', false);
    }

    public function test_booked_date_is_only_marked_as_the_current_users_booking_when_it_belongs_to_them(): void
    {
        $user = User::factory()->create();
        $slot = TrainerSlot::factory()->create(['status' => 'booked']);
        $payment = $user->payments()->create([
            'checkout_batch' => fake()->uuid(),
            'order_id' => 'TRAINER-OWNED-BOOKING',
            'gross_amount' => 150000,
            'status' => 'paid',
        ]);
        $payment->trainerBookings()->create([
            'user_id' => $user->id,
            'trainer_slot_id' => $slot->id,
            'amount' => 150000,
            'status' => 'confirmed',
        ]);

        $this->actingAs($user)->get(route('classes.index'))
            ->assertOk()
            ->assertSee('disabled title="Sudah kamu booking" data-trainer-date-booked', false)
            ->assertSee('Sudah kamu booking')
            ->assertDontSee('Tidak tersedia');
    }

    public function test_slot_end_time_follows_the_trainers_two_or_three_hour_duration(): void
    {
        $trainer = PersonalTrainer::factory()->create(['session_minutes' => 180]);
        $slot = TrainerSlot::factory()->for($trainer, 'trainer')->create([
            'starts_at' => now()->addDays(2)->setTime(9, 0),
            'ends_at' => now()->addDays(2)->setTime(10, 0),
        ]);

        $this->assertSame('09:00', $slot->starts_at->format('H:i'));
        $this->assertSame('12:00', $slot->ends_at->format('H:i'));
    }

    public function test_authenticated_user_can_hold_multiple_sessions_and_start_one_payment(): void
    {
        $this->mock(PaymentGateway::class, function (MockInterface $mock): void {
            $mock->shouldReceive('createTransaction')->once()->andReturn([
                'token' => 'trainer-snap-token',
                'redirect_url' => 'https://sandbox.midtrans.com/trainer-payment',
            ]);
        });
        $user = User::factory()->create(['phone' => '628123456789']);
        $trainer = PersonalTrainer::factory()->create(['session_price' => 150000]);
        $slots = TrainerSlot::factory()->count(2)->for($trainer, 'trainer')->sequence(
            ['starts_at' => now()->addDays(2)->setTime(9, 0), 'ends_at' => now()->addDays(2)->setTime(11, 0)],
            ['starts_at' => now()->addDays(3)->setTime(10, 0), 'ends_at' => now()->addDays(3)->setTime(12, 0)],
        )->create();

        $this->actingAs($user)
            ->postJson(route('trainer-bookings.store'), ['slot_ids' => $slots->modelKeys()])
            ->assertOk()
            ->assertJsonPath('snap_token', 'trainer-snap-token')
            ->assertJsonPath('redirect_url', 'https://sandbox.midtrans.com/trainer-payment')
            ->assertJsonPath('order_id', fn (string $orderId): bool => str_starts_with($orderId, 'TRAINER-'))
            ->assertJsonPath('invoice_url', fn (string $url): bool => str_contains($url, '/payments/pending?order_id=TRAINER-'))
            ->assertJsonPath('cancel_url', fn (string $url): bool => str_contains($url, '/personal-trainer-bookings/'));

        $this->assertDatabaseHas('payments', [
            'user_id' => $user->id,
            'gross_amount' => 300000,
            'snap_token' => 'trainer-snap-token',
        ]);
        $this->assertDatabaseCount('trainer_bookings', 2);
        foreach ($slots as $slot) {
            $this->assertDatabaseHas('trainer_slots', ['id' => $slot->id, 'status' => 'held']);
            $this->assertDatabaseHas('trainer_bookings', [
                'user_id' => $user->id,
                'trainer_slot_id' => $slot->id,
                'amount' => 150000,
                'status' => 'pending_payment',
            ]);
        }
    }

    public function test_non_ajax_trainer_booking_keeps_midtrans_redirect_as_a_fallback(): void
    {
        $this->mock(PaymentGateway::class, function (MockInterface $mock): void {
            $mock->shouldReceive('createTransaction')->once()->andReturn([
                'token' => 'trainer-fallback-token',
                'redirect_url' => 'https://sandbox.midtrans.com/trainer-fallback',
            ]);
        });
        $user = User::factory()->create(['phone' => '628123456789']);
        $slot = TrainerSlot::factory()->create([
            'starts_at' => now()->addDays(2)->setTime(9, 0),
            'ends_at' => now()->addDays(2)->setTime(11, 0),
        ]);

        $this->actingAs($user)
            ->post(route('trainer-bookings.store'), ['slot_ids' => [$slot->id]])
            ->assertRedirect('https://sandbox.midtrans.com/trainer-fallback');
    }

    public function test_user_can_cancel_pending_trainer_payment_and_choose_another_slot(): void
    {
        $this->mock(PaymentGateway::class, function (MockInterface $mock): void {
            $mock->shouldReceive('transactionStatus')
                ->once()
                ->with('TRAINER-CANCEL-TEST')
                ->andThrow(new \Exception('Transaction does not exist.', 404));
            $mock->shouldNotReceive('cancelTransaction');
        });
        $user = User::factory()->create();
        $slot = TrainerSlot::factory()->create([
            'status' => 'held',
            'held_until' => now()->addMinutes(30),
        ]);
        $payment = $user->payments()->create([
            'checkout_batch' => fake()->uuid(),
            'order_id' => 'TRAINER-CANCEL-TEST',
            'gross_amount' => 150000,
            'status' => 'pending',
            'snap_token' => 'trainer-cancel-token',
            'redirect_url' => 'https://sandbox.midtrans.com/trainer-cancel',
        ]);
        $payment->trainerBookings()->create([
            'user_id' => $user->id,
            'trainer_slot_id' => $slot->id,
            'amount' => 150000,
            'status' => 'pending_payment',
        ]);

        $this->actingAs($user)
            ->deleteJson(route('trainer-bookings.cancel', $payment))
            ->assertOk()
            ->assertJsonPath('message', 'Booking berhasil dibatalkan. Slot sudah tersedia kembali.');

        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => 'failed']);
        $this->assertDatabaseHas('trainer_bookings', ['payment_id' => $payment->id, 'status' => 'cancelled']);
        $this->assertDatabaseHas('trainer_slots', [
            'id' => $slot->id,
            'status' => 'available',
            'held_until' => null,
        ]);
    }

    public function test_paid_trainer_booking_cannot_be_cancelled(): void
    {
        $this->mock(PaymentGateway::class, function (MockInterface $mock): void {
            $mock->shouldReceive('transactionStatus')
                ->once()
                ->with('TRAINER-PAID-TEST')
                ->andReturn([
                    'order_id' => 'TRAINER-PAID-TEST',
                    'gross_amount' => '150000.00',
                    'transaction_status' => 'settlement',
                    'transaction_id' => 'midtrans-paid-test',
                    'payment_type' => 'bank_transfer',
                ]);
            $mock->shouldNotReceive('cancelTransaction');
        });
        $user = User::factory()->create();
        $slot = TrainerSlot::factory()->create([
            'status' => 'held',
            'held_until' => now()->addMinutes(30),
        ]);
        $payment = $user->payments()->create([
            'checkout_batch' => fake()->uuid(),
            'order_id' => 'TRAINER-PAID-TEST',
            'gross_amount' => 150000,
            'status' => 'pending',
        ]);
        $payment->trainerBookings()->create([
            'user_id' => $user->id,
            'trainer_slot_id' => $slot->id,
            'amount' => 150000,
            'status' => 'pending_payment',
        ]);

        $this->actingAs($user)
            ->deleteJson(route('trainer-bookings.cancel', $payment))
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Booking yang sudah dibayar tidak dapat dibatalkan.');

        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => 'paid']);
        $this->assertDatabaseHas('trainer_bookings', ['payment_id' => $payment->id, 'status' => 'confirmed']);
        $this->assertDatabaseHas('trainer_slots', ['id' => $slot->id, 'status' => 'booked']);
    }

    public function test_pending_midtrans_trainer_transaction_is_cancelled_before_releasing_slot(): void
    {
        $this->mock(PaymentGateway::class, function (MockInterface $mock): void {
            $mock->shouldReceive('transactionStatus')
                ->once()
                ->with('TRAINER-PENDING-TEST')
                ->andReturn([
                    'order_id' => 'TRAINER-PENDING-TEST',
                    'gross_amount' => '150000.00',
                    'transaction_status' => 'pending',
                    'transaction_id' => 'midtrans-pending-test',
                    'payment_type' => 'bank_transfer',
                ]);
            $mock->shouldReceive('cancelTransaction')
                ->once()
                ->with('TRAINER-PENDING-TEST');
        });
        $user = User::factory()->create();
        $slot = TrainerSlot::factory()->create([
            'status' => 'held',
            'held_until' => now()->addMinutes(30),
        ]);
        $payment = $user->payments()->create([
            'checkout_batch' => fake()->uuid(),
            'order_id' => 'TRAINER-PENDING-TEST',
            'gross_amount' => 150000,
            'status' => 'pending',
        ]);
        $payment->trainerBookings()->create([
            'user_id' => $user->id,
            'trainer_slot_id' => $slot->id,
            'amount' => 150000,
            'status' => 'pending_payment',
        ]);

        $this->actingAs($user)
            ->deleteJson(route('trainer-bookings.cancel', $payment))
            ->assertOk();

        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => 'failed']);
        $this->assertDatabaseHas('trainer_bookings', ['payment_id' => $payment->id, 'status' => 'cancelled']);
        $this->assertDatabaseHas('trainer_slots', ['id' => $slot->id, 'status' => 'available']);
    }

    public function test_user_cannot_cancel_another_users_trainer_payment(): void
    {
        $owner = User::factory()->create();
        $slot = TrainerSlot::factory()->create([
            'status' => 'held',
            'held_until' => now()->addMinutes(30),
        ]);
        $payment = $owner->payments()->create([
            'checkout_batch' => fake()->uuid(),
            'order_id' => 'TRAINER-OTHER-USER',
            'gross_amount' => 150000,
            'status' => 'pending',
        ]);
        $payment->trainerBookings()->create([
            'user_id' => $owner->id,
            'trainer_slot_id' => $slot->id,
            'amount' => 150000,
            'status' => 'pending_payment',
        ]);

        $this->actingAs(User::factory()->create())
            ->deleteJson(route('trainer-bookings.cancel', $payment))
            ->assertNotFound();

        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => 'pending']);
        $this->assertDatabaseHas('trainer_slots', ['id' => $slot->id, 'status' => 'held']);
    }

    public function test_successful_payment_confirms_booking_and_marks_slot_booked(): void
    {
        [$payment, $slot] = $this->createPendingTrainerPayment();

        app(PaymentService::class)->processStatus($payment, [
            'order_id' => $payment->order_id,
            'gross_amount' => '150000.00',
            'transaction_status' => 'settlement',
        ]);

        $this->assertDatabaseHas('trainer_bookings', ['payment_id' => $payment->id, 'status' => 'confirmed']);
        $this->assertDatabaseHas('trainer_slots', ['id' => $slot->id, 'status' => 'booked', 'held_until' => null]);
    }

    public function test_expired_payment_cancels_booking_and_releases_slot(): void
    {
        [$payment, $slot] = $this->createPendingTrainerPayment();

        app(PaymentService::class)->processStatus($payment, [
            'order_id' => $payment->order_id,
            'gross_amount' => '150000.00',
            'transaction_status' => 'expire',
        ]);

        $this->assertDatabaseHas('trainer_bookings', ['payment_id' => $payment->id, 'status' => 'cancelled']);
        $this->assertDatabaseHas('trainer_slots', ['id' => $slot->id, 'status' => 'available', 'held_until' => null]);
    }

    private function createPendingTrainerPayment(): array
    {
        $user = User::factory()->create();
        $slot = TrainerSlot::factory()->create(['status' => 'held', 'held_until' => now()->addMinutes(30)]);
        $payment = $user->payments()->create([
            'checkout_batch' => fake()->uuid(),
            'order_id' => 'TRAINER-'.fake()->uuid(),
            'gross_amount' => 150000,
            'status' => 'pending',
        ]);
        $payment->trainerBookings()->create([
            'user_id' => $user->id,
            'trainer_slot_id' => $slot->id,
            'amount' => 150000,
            'status' => 'pending_payment',
        ]);

        return [$payment, $slot];
    }
}
