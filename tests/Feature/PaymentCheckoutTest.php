<?php

namespace Tests\Feature;

use App\Contracts\PaymentGateway;
use App\Models\MembershipPlan;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Fakes\FakePaymentGateway;
use Tests\TestCase;

class PaymentCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_single_membership_checkout_creates_one_midtrans_payment_and_redirects_to_snap(): void
    {
        $gateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $gateway);
        $user = User::factory()->create();
        $plan = MembershipPlan::factory()->create([
            'name' => 'Regular Bulanan',
            'price' => 250000,
            'duration_days' => 30,
        ]);

        $response = $this->actingAs($user)->post(route('membership.checkout', $plan), [
            'phone' => '6281234567890',
        ]);

        $payment = Payment::query()->sole();

        $response->assertRedirect($gateway->createResponse['redirect_url']);
        $this->assertSame($payment->order_id, $gateway->createdIdempotencyKey);
        $this->assertSame($payment->order_id, $gateway->createdParameters['transaction_details']['order_id']);
        $this->assertSame(250000, $gateway->createdParameters['transaction_details']['gross_amount']);
        $this->assertSame('Regular Bulanan', $gateway->createdParameters['item_details'][0]['name']);
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'user_id' => $user->id,
            'gross_amount' => 250000,
            'status' => 'pending',
            'snap_token' => 'sandbox-snap-token',
        ]);
        $this->assertDatabaseHas('transactions', [
            'payment_id' => $payment->id,
            'user_id' => $user->id,
            'amount' => 250000,
            'status' => 'pending',
        ]);
    }

    public function test_cart_checkout_groups_all_memberships_into_one_midtrans_payment(): void
    {
        $gateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $gateway);
        $user = User::factory()->create();
        $regular = MembershipPlan::factory()->create(['name' => 'Regular', 'price' => 250000]);
        $vip = MembershipPlan::factory()->create(['name' => 'VIP', 'price' => 500000]);

        $response = $this->actingAs($user)
            ->withSession([
                'membership_cart' => [$regular->id => 2, $vip->id => 1],
                'membership_cart_version' => 2,
            ])
            ->post(route('cart.checkout.store'), ['phone' => '6281234567890']);

        $payment = Payment::query()->sole();

        $response->assertRedirect($gateway->createResponse['redirect_url']);
        $response->assertSessionMissing('membership_cart');
        $this->assertSame(1000000, $payment->gross_amount);
        $this->assertSame(1000000, $gateway->createdParameters['transaction_details']['gross_amount']);
        $this->assertCount(2, $gateway->createdParameters['item_details']);
        $this->assertDatabaseCount('payments', 1);
        $this->assertDatabaseCount('transactions', 2);
        $this->assertDatabaseCount('memberships', 2);
        $this->assertSame(2, $payment->transactions()->count());
    }
}
