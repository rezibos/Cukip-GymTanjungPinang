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
            ->assertSee('disabled title="Semua sesi sudah dibooking" data-trainer-date-booked', false)
            ->assertSee('Sudah dibooking')
            ->assertDontSee('name="slot_ids[]" value="'.$slot->id.'"', false);
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
            ->post(route('trainer-bookings.store'), ['slot_ids' => $slots->modelKeys()])
            ->assertRedirect('https://sandbox.midtrans.com/trainer-payment');

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
