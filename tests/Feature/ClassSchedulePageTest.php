<?php

namespace Tests\Feature;

use App\Models\ClassSession;
use App\Models\GymClass;
use App\Models\MembershipPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ClassSchedulePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_see_the_seven_day_schedule_and_a_login_link(): void
    {
        $this->travelTo(Carbon::parse('2026-11-08 10:00:00'));
        $this->createSession('Kelas Hari Ini', 10, '2026-11-08');
        $this->createSession('Kelas Hari Terakhir', 12, '2026-11-14');
        $this->createSession('Kelas Kemarin', 12, '2026-11-07');
        $this->createSession('Kelas Minggu Depan', 12, '2026-11-15');

        $response = $this->get(route('classes.index'));

        $response->assertOk()->assertSee('Kelas Hari Ini')->assertSee('Kelas Hari Terakhir')
            ->assertDontSee('Kelas Kemarin')->assertDontSee('Kelas Minggu Depan')
            ->assertSee('2 sesi tersedia')->assertSee('Masuk untuk booking')
            ->assertSee('Minggu')->assertSee('Sabtu')
            ->assertSee('data-class-day="2026-11-14"', false);
    }

    public function test_members_see_available_full_and_already_booked_states(): void
    {
        $this->travelTo(Carbon::parse('2026-11-08 10:00:00'));
        $user = User::factory()->create();
        $plan = MembershipPlan::factory()->create();
        $user->memberships()->create(['membership_plan_id' => $plan->id, 'status' => 'active', 'start_date' => '2026-11-01', 'end_date' => '2026-11-30']);
        $ownSession = $this->createSession('Kelas Pilihan Saya', 3, '2026-11-09');
        $fullSession = $this->createSession('Kelas Sudah Penuh', 1, '2026-11-10');
        $availableSession = $this->createSession('Kelas Masih Tersedia', 4, '2026-11-11');
        $user->classBookings()->create(['class_session_id' => $ownSession->id, 'status' => 'booked']);
        $otherUser = User::factory()->create();
        $otherUser->classBookings()->create(['class_session_id' => $fullSession->id, 'status' => 'booked']);
        $otherUser->classBookings()->create(['class_session_id' => $availableSession->id, 'status' => 'cancelled']);

        $response = $this->actingAs($user)->get(route('classes.index'));

        $response->assertOk()->assertSee('Member aktif')->assertSee('Sudah booking')
            ->assertSee('Kelas penuh')->assertSee('4 slot tersisa')
            ->assertSee('action="'.route('classes.book', $availableSession).'"', false)
            ->assertDontSee('action="'.route('classes.book', $ownSession).'"', false)
            ->assertDontSee('action="'.route('classes.book', $fullSession).'"', false);
    }

    public function test_non_members_get_a_membership_link_without_a_booking_form(): void
    {
        $this->travelTo(Carbon::parse('2026-11-08 10:00:00'));
        $user = User::factory()->create();
        $session = $this->createSession('Kelas Terbuka', 10, '2026-11-09');

        $response = $this->actingAs($user)->get(route('classes.index'));

        $response->assertOk()->assertSee('Aktifkan membership')
            ->assertSee(route('membership.index'), false)
            ->assertDontSee('action="'.route('classes.book', $session).'"', false);
    }

    public function test_empty_schedule_keeps_the_day_selector_and_explains_the_empty_state(): void
    {
        $this->travelTo(Carbon::parse('2026-11-08 10:00:00'));

        $response = $this->get(route('classes.index'));

        $response->assertOk()->assertSee('0 sesi tersedia')->assertSee('Belum ada sesi untuk pilihan ini.')
            ->assertSee('data-class-day="all"', false)->assertSee('data-class-empty', false);
    }

    public function test_class_names_descriptions_and_instructor_names_are_escaped(): void
    {
        $this->travelTo(Carbon::parse('2026-11-08 10:00:00'));
        $name = '<script>alert("class")</script>';
        $instructor = '<img src=x onerror=alert(1)>';
        $description = '<script>alert("description")</script>';
        $class = GymClass::factory()->create(['name' => $name, 'instructor' => $instructor, 'description' => $description]);
        $class->sessions()->create(['date' => '2026-11-09', 'capacity' => 10]);

        $response = $this->get(route('classes.index'));

        $response->assertOk()->assertSee($name)->assertSee($instructor)->assertSee($description)
            ->assertDontSee($name, false)->assertDontSee($instructor, false)->assertDontSee($description, false);
    }

    private function createSession(string $name, int $capacity, string $date): ClassSession
    {
        $class = GymClass::factory()->create(['name' => $name]);

        return $class->sessions()->create(['date' => $date, 'capacity' => $capacity]);
    }
}
