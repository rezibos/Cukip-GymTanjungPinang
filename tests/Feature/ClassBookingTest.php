<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Models\GymClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_cancel_their_class_booking_and_release_the_slot(): void
    {
        $user = User::factory()->create();
        $session = GymClass::factory()->create()->sessions()->create([
            'date' => now()->addDay()->toDateString(),
            'capacity' => 2,
        ]);
        $booking = $user->classBookings()->create([
            'class_session_id' => $session->id,
            'status' => BookingStatus::Booked,
        ]);

        $response = $this->actingAs($user)
            ->delete(route('classes.bookings.cancel', $booking));

        $response->assertRedirect()
            ->assertSessionHas('success', 'Booking berhasil dibatalkan.');
        $this->assertSame(BookingStatus::Cancelled, $booking->fresh()->status);
        $this->assertSame(2, $session->remainingSlots());
    }

    public function test_member_cannot_cancel_another_members_class_booking(): void
    {
        $owner = User::factory()->create();
        $otherMember = User::factory()->create();
        $session = GymClass::factory()->create()->sessions()->create([
            'date' => now()->addDay()->toDateString(),
            'capacity' => 2,
        ]);
        $booking = $owner->classBookings()->create([
            'class_session_id' => $session->id,
            'status' => BookingStatus::Booked,
        ]);

        $response = $this->actingAs($otherMember)
            ->delete(route('classes.bookings.cancel', $booking));

        $response->assertForbidden();
        $this->assertSame(BookingStatus::Booked, $booking->fresh()->status);
        $this->assertSame(1, $session->remainingSlots());
    }
}
