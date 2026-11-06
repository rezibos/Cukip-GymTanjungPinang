<?php

namespace Tests\Feature;

use App\Models\MembershipPlan;
use App\Models\User;
use Database\Seeders\MembershipPlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class MembershipCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_can_be_updated_twice_without_removing_historical_memberships(): void
    {
        $legacyPlan = MembershipPlan::factory()->create(['name' => 'Paket Bulanan', 'price' => 250000]);
        $user = User::factory()->create();
        $membership = $user->memberships()->create(['membership_plan_id' => $legacyPlan->id, 'status' => 'expired']);

        $this->seed(MembershipPlanSeeder::class);
        $this->seed(MembershipPlanSeeder::class);

        $this->assertDatabaseCount('membership_plans', 6);
        $this->assertDatabaseHas('membership_plans', ['id' => $legacyPlan->id, 'name' => 'Paket Bulanan', 'is_active' => false, 'price' => 250000]);
        $this->assertModelExists($membership);
        $this->assertDatabaseHas('membership_plans', ['name' => 'Day Pass', 'price' => 50000, 'duration_days' => 1, 'is_active' => true]);
        $this->assertDatabaseHas('membership_plans', ['name' => 'Regular', 'price' => 250000, 'duration_days' => 30, 'is_active' => true]);
        $this->assertDatabaseHas('membership_plans', ['name' => 'VIP', 'price' => 500000, 'duration_days' => 30, 'is_active' => true]);
        $this->assertDatabaseHas('membership_plans', ['name' => 'Regular', 'price' => 2400000, 'duration_days' => 365, 'is_active' => true]);
        $this->assertDatabaseHas('membership_plans', ['name' => 'VIP', 'price' => 4800000, 'duration_days' => 365, 'is_active' => true]);
    }

    public function test_home_renders_the_requested_plan_names_and_vip_facilities(): void
    {
        $this->seed(MembershipPlanSeeder::class);

        $response = $this->get('/');

        $response->assertOk()->assertSee('Day Pass')->assertSee('Regular')->assertSee('VIP')
            ->assertSee('Experience our gym facilities and start your fitness journey with no commitment.')
            ->assertSee('Exclusive VIP Floor')->assertSee('Premium Equipment')->assertSee('Private Training')->assertSee('VIP Lounge')
            ->assertSee('Monthly')->assertSee('Yearly')->assertSee('2,4')->assertSee('4,8');
    }

    #[TestWith(['Day Pass', 50000, 1])]
    #[TestWith(['Regular', 250000, 30])]
    #[TestWith(['VIP', 500000, 30])]
    #[TestWith(['Regular', 2400000, 365])]
    #[TestWith(['VIP', 4800000, 365])]
    public function test_checkout_charges_the_catalog_price(string $name, int $price, int $duration): void
    {
        $this->seed(MembershipPlanSeeder::class);
        $user = User::factory()->create();
        $plan = MembershipPlan::where('name', $name)->where('duration_days', $duration)->firstOrFail();

        $response = $this->actingAs($user)->post(route('membership.checkout', $plan), ['phone' => '6281234567890']);

        $response->assertRedirect();
        $this->assertDatabaseHas('memberships', ['user_id' => $user->id, 'membership_plan_id' => $plan->id, 'status' => 'pending_payment']);
        $this->assertDatabaseHas('transactions', ['user_id' => $user->id, 'amount' => $price, 'status' => 'pending']);
    }

    public function test_inactive_legacy_plans_cannot_be_purchased(): void
    {
        $user = User::factory()->create();
        $plan = MembershipPlan::factory()->create(['is_active' => false]);

        $this->actingAs($user)->get(route('membership.checkout', $plan))->assertNotFound();
        $this->post(route('membership.checkout', $plan), ['phone' => '6281234567890'])->assertNotFound();

        $this->assertDatabaseCount('memberships', 0);
        $this->assertDatabaseCount('transactions', 0);
    }
}
