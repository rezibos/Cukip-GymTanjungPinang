<?php

namespace Tests\Feature;

use App\Contracts\PaymentGateway;
use App\Mail\PaymentSuccessfulMail;
use App\Models\MembershipPlan;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\Fakes\FakePaymentGateway;
use Tests\TestCase;

class GuestCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_customer_can_open_snap_without_login_or_receiving_credentials_early(): void
    {
        Mail::fake();
        $gateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $gateway);
        $plan = MembershipPlan::factory()->create([
            'name' => 'Regular Bulanan',
            'price' => 250000,
        ]);

        $response = $this->withSession([
            'membership_cart' => [$plan->id => 1],
            'membership_cart_version' => 2,
        ])->postJson(route('cart.guest-checkout'), [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'phone' => '6281234567890',
        ]);

        $payment = Payment::query()->sole();

        $response->assertOk()
            ->assertJsonPath('snap_token', 'sandbox-snap-token')
            ->assertJsonPath('order_id', $gateway->createdParameters['transaction_details']['order_id'])
            ->assertJsonStructure(['invoice_url']);
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'budi@example.com']);
        $this->assertSame('Budi Santoso', $gateway->createdParameters['customer_details']['first_name']);
        $this->assertSame('budi@example.com', $gateway->createdParameters['customer_details']['email']);
        $this->assertTrue($payment->creates_account);
        $this->assertNull($payment->user_id);
        $this->assertNull($payment->account_password);
        $this->assertDatabaseCount('payments', 1);
        $this->assertDatabaseCount('transactions', 0);
        $this->assertDatabaseCount('memberships', 0);
        Mail::assertNothingSent();
    }

    public function test_existing_email_can_checkout_without_login_or_account_takeover(): void
    {
        Mail::fake();
        $gateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $gateway);
        $existingUser = User::factory()->create(['email' => 'member@example.com']);
        $plan = MembershipPlan::factory()->create();

        $response = $this->withSession([
            'membership_cart' => [$plan->id => 1],
            'membership_cart_version' => 2,
        ])->postJson(route('cart.guest-checkout'), [
            'name' => 'Penyerang',
            'email' => $existingUser->email,
            'phone' => '6281234567890',
        ]);

        $payment = Payment::query()->sole();

        $response->assertOk()->assertJsonPath('snap_token', 'sandbox-snap-token');
        $this->assertGuest();
        $this->assertSame($existingUser->id, $payment->user_id);
        $this->assertFalse($payment->creates_account);
        $this->assertNull($payment->account_password);
        $this->assertNotNull($gateway->createdParameters);

        $gateway->statusResponse = [
            'transaction_id' => 'midtrans-transaction-existing',
            'order_id' => $payment->order_id,
            'gross_amount' => (string) $payment->gross_amount,
            'transaction_status' => 'settlement',
            'payment_type' => 'bank_transfer',
            'fraud_status' => 'accept',
        ];

        $this->get(route('payments.finish', ['order_id' => $payment->order_id]))->assertOk();
        $this->assertGuest();
        $this->assertTrue(Hash::check('password', $existingUser->fresh()->password));
        Mail::assertSent(PaymentSuccessfulMail::class, fn (PaymentSuccessfulMail $mail): bool => $mail->hasTo($existingUser->email));
    }

    public function test_authenticated_customer_checkout_uses_account_identity(): void
    {
        $gateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $gateway);
        $user = User::factory()->create([
            'name' => 'Member Asli',
            'email' => 'member@example.com',
            'phone' => null,
        ]);
        $plan = MembershipPlan::factory()->create();

        $response = $this->actingAs($user)->withSession([
            'membership_cart' => [$plan->id => 1],
            'membership_cart_version' => 2,
        ])->postJson(route('cart.guest-checkout'), [
            'name' => 'Nama Palsu',
            'email' => 'palsu@example.com',
            'phone' => '6281234567890',
        ]);

        $response->assertOk()->assertJsonPath('snap_token', 'sandbox-snap-token');
        $this->assertSame('Member Asli', $gateway->createdParameters['customer_details']['first_name']);
        $this->assertSame('member@example.com', $gateway->createdParameters['customer_details']['email']);
        $this->assertSame('6281234567890', $user->fresh()->phone);
    }

    public function test_successful_payment_sends_credentials_and_logs_in_new_customer(): void
    {
        Mail::fake();
        $gateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $gateway);
        $plan = MembershipPlan::factory()->create(['price' => 250000]);

        $checkoutResponse = $this->withSession([
            'membership_cart' => [$plan->id => 1],
            'membership_cart_version' => 2,
        ])->postJson(route('cart.guest-checkout'), [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'phone' => '6281234567890',
        ]);
        $payment = Payment::query()->sole();
        $gateway->statusResponse = [
            'transaction_id' => 'midtrans-transaction-1',
            'order_id' => $payment->order_id,
            'gross_amount' => '250000.00',
            'transaction_status' => 'settlement',
            'payment_type' => 'bank_transfer',
            'fraud_status' => 'accept',
        ];

        $response = $this->get(route('payments.finish', [
            'order_id' => $checkoutResponse->json('order_id'),
        ]));
        $user = User::query()->where('email', 'budi@example.com')->sole();

        $response->assertOk();
        $this->assertAuthenticatedAs($user);
        Mail::assertSent(PaymentSuccessfulMail::class, function (PaymentSuccessfulMail $mail) use ($user): bool {
            return $mail->hasTo($user->email)
                && is_string($mail->plainPassword)
                && Hash::check($mail->plainPassword, $user->password);
        });
        $this->assertNotNull($payment->fresh()->credentials_sent_at);
        $this->assertNotNull($payment->fresh()->payment_confirmation_sent_at);
        $this->assertNull($payment->fresh()->account_password);

        $this->postJson(route('payments.midtrans.notification'), [
            'transaction_id' => 'midtrans-transaction-1',
            'order_id' => $payment->order_id,
        ])->assertOk();
        Mail::assertSent(PaymentSuccessfulMail::class, 1);
    }
}
