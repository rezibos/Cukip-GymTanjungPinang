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

    public function test_membership_page_shows_both_billing_periods_and_the_correct_checkout_links(): void
    {
        $this->seed(MembershipPlanSeeder::class);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('membership.index'));

        $response->assertOk()->assertSee('Bulanan')->assertSee('Tahunan')
            ->assertSee('Day Pass')->assertSee('Regular')->assertSee('VIP')
            ->assertSee('Premium Equipment')->assertSee('Private Training')
            ->assertSee('data-plan-selected="monthly"', false)
            ->assertSee('data-plan-option="yearly" data-active="false"', false);
        foreach (MembershipPlan::where('is_active', true)->get() as $plan) {
            $response->assertSee(route('cart.store', $plan), false)
                ->assertSee($plan->formatted_price);
        }
    }

    #[TestWith([30, 'monthly'])]
    #[TestWith([365, 'yearly'])]
    public function test_a_single_billing_period_is_visible_without_a_switch(int $duration, string $period): void
    {
        MembershipPlan::factory()->create(['name' => 'Regular', 'duration_days' => $duration, 'is_active' => true]);

        $response = $this->get(route('membership.index'));

        $response->assertOk()->assertSee('Regular')
            ->assertSee('data-plan-selected="'.$period.'"', false)
            ->assertSee('data-plan-option="'.$period.'" data-active="true"', false)
            ->assertDontSee('data-plan-period=', false);
    }

    public function test_an_empty_membership_catalog_shows_an_explanation_and_hides_inactive_plans(): void
    {
        MembershipPlan::factory()->create(['name' => 'Paket lama tersembunyi', 'is_active' => false]);

        $response = $this->get(route('membership.index'));

        $response->assertOk()->assertSee('Paket membership sedang diperbarui.')
            ->assertDontSee('Paket lama tersembunyi')->assertDontSee('data-plan-period=', false);
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
