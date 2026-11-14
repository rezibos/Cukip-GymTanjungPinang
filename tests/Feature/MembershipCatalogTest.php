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

        $this->assertDatabaseCount('membership_plans', 7);
        $this->assertDatabaseHas('membership_plans', ['id' => $legacyPlan->id, 'name' => 'Paket Bulanan', 'is_active' => false, 'price' => 250000]);
        $this->assertModelExists($membership);
        $this->assertDatabaseHas('membership_plans', ['name' => '1 Day Pass', 'price' => 50000, 'duration_days' => 1, 'badge' => 'Trial', 'is_active' => true]);
        $this->assertDatabaseHas('membership_plans', ['name' => '1 Month', 'price' => 500000, 'duration_days' => 30, 'is_active' => true]);
        $this->assertDatabaseHas('membership_plans', ['name' => '3 Month', 'price' => 1350000, 'duration_days' => 90, 'original_price' => 1500000, 'discount_label' => '10% OFF', 'is_active' => true]);
        $this->assertDatabaseHas('membership_plans', ['name' => '6 Month', 'price' => 2400000, 'duration_days' => 180, 'original_price' => 3000000, 'discount_label' => '20% OFF', 'is_active' => true]);
        $this->assertDatabaseHas('membership_plans', ['name' => '12 Month', 'price' => 4200000, 'duration_days' => 365, 'original_price' => 6000000, 'discount_label' => '30% OFF', 'is_active' => true]);
        $this->assertDatabaseHas('membership_plans', ['name' => '18 Month', 'price' => 5400000, 'duration_days' => 540, 'original_price' => 9000000, 'discount_label' => '40% OFF', 'badge' => 'Best Value', 'is_active' => true]);
    }

    public function test_home_renders_six_independent_packages_with_purchase_benefits(): void
    {
        $this->seed(MembershipPlanSeeder::class);

        $response = $this->get('/');

        $response->assertOk()
            ->assertSeeInOrder(['1 Day Pass', '1 Month', '3 Month', '6 Month', '12 Month', '18 Month'])
            ->assertSee('2 sesi personal trainer')->assertSee('Gratis kaos CukipGym')
            ->assertSee('Tidak ada benefit tambahan')
            ->assertSee('Trial')->assertSee('DISKON 10%')->assertSee('DISKON 20%')->assertSee('DISKON 30%')->assertSee('DISKON 40%')->assertSee('Best Value')
            ->assertDontSee('10% OFF')
            ->assertSee('sculpted-plan-discount-label', false)
            ->assertSee('sculpted-plan-discount-value', false)
            ->assertSee('class="mx-auto max-w-[76rem]" data-membership-slider', false)
            ->assertSee('sculpted-plan-badge', false)
            ->assertSee('sculpted-plan-badge-tab', false)
            ->assertSee('data-membership-carousel', false)
            ->assertSee('data-membership-swipe', false)
            ->assertSeeInOrder(['data-membership-carousel', 'data-membership-controls'], false)
            ->assertSee('aria-label="Paket sebelumnya"', false)
            ->assertSee('aria-label="Paket berikutnya"', false)
            ->assertDontSee('data-plan-period=', false)
            ->assertDontSee('Facilities');
    }

    public function test_membership_page_shows_all_packages_without_a_billing_period_switch(): void
    {
        $this->seed(MembershipPlanSeeder::class);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('membership.index'));

        $response->assertOk()
            ->assertSeeInOrder(['1 Day Pass', '1 Month', '3 Month', '6 Month', '12 Month', '18 Month'])
            ->assertSee('Benefits')->assertSee('5 sesi personal trainer')
            ->assertSee('Rp3.000.000')->assertSee('Rp6.000.000')->assertSee('Rp9.000.000')
            ->assertSee('max-w-[76rem]', false)
            ->assertSee('sculpted-plan sculpted-plan-featured', false)
            ->assertSee('sculpted-plan-badge', false)
            ->assertSee('sculpted-plan-badge-tab', false)
            ->assertSee('data-membership-carousel', false)
            ->assertSee('data-membership-swipe', false)
            ->assertSeeInOrder(['data-membership-carousel', 'data-membership-controls'], false)
            ->assertSee('data-membership-previous', false)
            ->assertSee('data-membership-next', false)
            ->assertSee('data-membership-page', false)
            ->assertDontSee('Satu akses. Pilih durasimu.')
            ->assertDontSee('Benefit di bawah adalah bonus pembelian paket')
            ->assertDontSee('Akses fasilitas yang sama')
            ->assertDontSee('Bonus khusus paket jangka panjang')
            ->assertDontSee('Pantau membership dari profil')
            ->assertDontSee('data-plan-picker', false)
            ->assertDontSee('data-plan-period=', false)
            ->assertDontSee('data-plan-option=', false);
        foreach (MembershipPlan::where('is_active', true)->get() as $plan) {
            $response->assertSee(route('cart.store', $plan), false)
                ->assertSee($plan->formatted_price);
        }
    }

    public function test_an_empty_membership_catalog_shows_an_explanation_and_hides_inactive_plans(): void
    {
        MembershipPlan::factory()->create(['name' => 'Paket lama tersembunyi', 'is_active' => false]);

        $response = $this->get(route('membership.index'));

        $response->assertOk()->assertSee('Paket membership sedang diperbarui.')
            ->assertDontSee('Paket lama tersembunyi')->assertDontSee('data-plan-period=', false);
    }

    #[TestWith(['1 Day Pass', 50000, 1])]
    #[TestWith(['1 Month', 500000, 30])]
    #[TestWith(['3 Month', 1350000, 90])]
    #[TestWith(['6 Month', 2400000, 180])]
    #[TestWith(['12 Month', 4200000, 365])]
    #[TestWith(['18 Month', 5400000, 540])]
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
