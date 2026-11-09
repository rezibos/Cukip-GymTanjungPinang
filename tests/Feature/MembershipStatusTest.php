<?php

namespace Tests\Feature;

use App\Enums\MembershipStatus;
use App\Models\Membership;
use App\Models\MembershipPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class MembershipStatusTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-11-09 12:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_active_membership_with_elapsed_end_date_is_effectively_expired(): void
    {
        $membership = $this->createMembership([
            'status' => MembershipStatus::Active,
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-08',
        ]);

        $this->assertSame(MembershipStatus::Expired, $membership->effectiveStatus());
        $this->assertFalse($membership->isActive());
    }

    public function test_membership_remains_active_through_its_end_date(): void
    {
        $membership = $this->createMembership([
            'status' => MembershipStatus::Active,
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-09',
        ]);

        $this->assertSame(MembershipStatus::Active, $membership->effectiveStatus());
        $this->assertTrue($membership->isActive());
    }

    public function test_membership_with_a_future_start_date_is_not_active_yet(): void
    {
        $membership = $this->createMembership([
            'status' => MembershipStatus::Active,
            'start_date' => '2026-11-10',
            'end_date' => '2026-12-10',
        ]);

        $this->assertSame(MembershipStatus::Upcoming, $membership->effectiveStatus());
        $this->assertFalse($membership->isActive());
    }

    public function test_payment_and_cancellation_statuses_are_not_overridden_by_dates(): void
    {
        $pendingMembership = $this->createMembership([
            'status' => MembershipStatus::PendingPayment,
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-08',
        ]);
        $cancelledMembership = $this->createMembership([
            'status' => MembershipStatus::Cancelled,
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
        ]);

        $this->assertSame(MembershipStatus::PendingPayment, $pendingMembership->effectiveStatus());
        $this->assertSame(MembershipStatus::Cancelled, $cancelledMembership->effectiveStatus());
    }

    public function test_date_status_synchronization_expires_and_reactivates_memberships(): void
    {
        $elapsedMembership = $this->createMembership([
            'status' => MembershipStatus::Active,
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-08',
        ]);
        $extendedMembership = $this->createMembership([
            'status' => MembershipStatus::Expired,
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
        ]);
        $upcomingMembership = $this->createMembership([
            'status' => MembershipStatus::Active,
            'start_date' => '2026-11-10',
            'end_date' => '2026-12-10',
        ]);
        $this->createMembership([
            'status' => MembershipStatus::PendingPayment,
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-08',
        ]);

        $this->assertSame(3, Membership::synchronizeDateStatuses());
        $this->assertSame(MembershipStatus::Expired, $elapsedMembership->fresh()->status);
        $this->assertSame(MembershipStatus::Active, $extendedMembership->fresh()->status);
        $this->assertSame(MembershipStatus::Upcoming, $upcomingMembership->fresh()->status);
        $this->assertDatabaseHas('memberships', ['status' => MembershipStatus::PendingPayment->value]);
    }

    public function test_activation_uses_the_selected_package_duration_and_quantity(): void
    {
        $plan = MembershipPlan::factory()->create(['duration_days' => 30]);
        $membership = $this->createMembership([
            'membership_plan_id' => $plan->id,
            'quantity' => 2,
            'status' => MembershipStatus::PendingPayment,
        ]);

        $membership->activate();

        $this->assertSame(MembershipStatus::Active, $membership->fresh()->status);
        $this->assertSame('2026-11-09', $membership->fresh()->start_date->toDateString());
        $this->assertSame('2027-01-08', $membership->fresh()->end_date->toDateString());
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createMembership(array $attributes): Membership
    {
        $user = User::factory()->create();
        $plan = MembershipPlan::factory()->create();

        return $user->memberships()->create([
            'membership_plan_id' => $plan->id,
            ...$attributes,
        ]);
    }
}
