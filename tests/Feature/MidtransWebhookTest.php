<?php

namespace Tests\Feature;

use App\Contracts\PaymentGateway;
use App\Enums\MembershipStatus;
use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use App\Mail\PaymentSuccessfulMail;
use App\Models\Membership;
use App\Models\MembershipPlan;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\Fakes\FakePaymentGateway;
use Tests\TestCase;

class MidtransWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_signed_midtrans_test_notification_returns_ok_without_transaction_lookup(): void
    {
        config(['services.midtrans.server_key' => 'sandbox-server-key']);
        $gateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $gateway);
        $orderId = 'payment_notif_test_M332158236_example';
        $statusCode = '200';
        $grossAmount = '105000.00';

        $response = $this->postJson(route('payments.midtrans.notification'), [
            'transaction_id' => 'midtrans-test-transaction',
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => hash('sha512', $orderId.$statusCode.$grossAmount.'sandbox-server-key'),
        ]);

        $response->assertOk()->assertSeeText('OK');
        $this->assertNull($gateway->requestedTransactionId);
    }

    public function test_midtrans_test_notification_with_invalid_signature_is_rejected(): void
    {
        config(['services.midtrans.server_key' => 'sandbox-server-key']);
        $gateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $gateway);

        $response = $this->postJson(route('payments.midtrans.notification'), [
            'transaction_id' => 'midtrans-test-transaction',
            'order_id' => 'payment_notif_test_M332158236_example',
            'status_code' => '200',
            'gross_amount' => '105000.00',
            'signature_key' => 'invalid-signature',
        ]);

        $response->assertForbidden()->assertSeeText('Invalid notification signature.');
        $this->assertNull($gateway->requestedTransactionId);
    }

    public function test_verified_settlement_marks_payment_paid_and_activates_membership(): void
    {
        Mail::fake();
        $this->travelTo('2026-11-09 12:00:00');
        [$payment, $transaction, $membership] = $this->createPendingPayment();
        $gateway = $this->fakeGateway($payment, 'settlement');

        $response = $this->postJson(route('payments.midtrans.notification'), [
            'transaction_id' => 'midtrans-transaction-1',
            'order_id' => $payment->order_id,
        ]);

        $response->assertOk()->assertSeeText('OK');
        $this->assertSame('midtrans-transaction-1', $gateway->requestedTransactionId);
        $this->assertSame(PaymentStatus::Paid, $payment->fresh()->status);
        $this->assertSame(TransactionStatus::Paid, $transaction->fresh()->status);
        $this->assertSame(MembershipStatus::Active, $membership->fresh()->status);
        $this->assertSame('2026-11-09', $membership->fresh()->start_date->toDateString());
        $this->assertSame('2026-12-09', $membership->fresh()->end_date->toDateString());
        $this->assertNotNull($payment->fresh()->payment_confirmation_sent_at);
        Mail::assertSent(PaymentSuccessfulMail::class, fn (PaymentSuccessfulMail $mail): bool => $mail->hasTo($payment->user->email));
    }

    public function test_duplicate_settlement_does_not_extend_membership_again(): void
    {
        Mail::fake();
        $this->travelTo('2026-11-09 12:00:00');
        [$payment, , $membership] = $this->createPendingPayment();
        $this->fakeGateway($payment, 'settlement');
        $payload = [
            'transaction_id' => 'midtrans-transaction-1',
            'order_id' => $payment->order_id,
        ];

        $this->postJson(route('payments.midtrans.notification'), $payload)->assertOk();
        $this->travelTo('2026-11-10 12:00:00');
        $this->postJson(route('payments.midtrans.notification'), $payload)->assertOk();

        $this->assertSame('2026-11-09', $membership->fresh()->start_date->toDateString());
        $this->assertSame('2026-12-09', $membership->fresh()->end_date->toDateString());
        Mail::assertSent(PaymentSuccessfulMail::class, 1);
    }

    public function test_expired_payment_cancels_pending_membership(): void
    {
        [$payment, $transaction, $membership] = $this->createPendingPayment();
        $this->fakeGateway($payment, 'expire');

        $this->postJson(route('payments.midtrans.notification'), [
            'transaction_id' => 'midtrans-transaction-1',
            'order_id' => $payment->order_id,
        ])->assertOk();

        $this->assertSame(PaymentStatus::Expired, $payment->fresh()->status);
        $this->assertSame(TransactionStatus::Rejected, $transaction->fresh()->status);
        $this->assertSame(MembershipStatus::Cancelled, $membership->fresh()->status);
    }

    public function test_notification_with_mismatched_verified_order_id_is_rejected(): void
    {
        [$payment, $transaction, $membership] = $this->createPendingPayment();
        $gateway = $this->fakeGateway($payment, 'settlement');
        $gateway->statusResponse['order_id'] = 'GYMPINANG-OTHER-ORDER';

        $response = $this->postJson(route('payments.midtrans.notification'), [
            'transaction_id' => 'midtrans-transaction-1',
            'order_id' => $payment->order_id,
        ]);

        $response->assertUnprocessable();
        $this->assertSame(PaymentStatus::Pending, $payment->fresh()->status);
        $this->assertSame(TransactionStatus::Pending, $transaction->fresh()->status);
        $this->assertSame(MembershipStatus::PendingPayment, $membership->fresh()->status);
    }

    /**
     * @return array{Payment, Transaction, Membership}
     */
    private function createPendingPayment(): array
    {
        $user = User::factory()->create();
        $plan = MembershipPlan::factory()->create(['duration_days' => 30, 'price' => 250000]);
        $membership = $user->memberships()->create([
            'membership_plan_id' => $plan->id,
            'quantity' => 1,
            'status' => MembershipStatus::PendingPayment,
        ]);
        $payment = Payment::factory()->for($user)->create([
            'gross_amount' => 250000,
            'status' => PaymentStatus::Pending,
        ]);
        $transaction = $membership->transactions()->create([
            'user_id' => $user->id,
            'payment_id' => $payment->id,
            'checkout_batch' => $payment->checkout_batch,
            'quantity' => 1,
            'amount' => 250000,
            'status' => TransactionStatus::Pending,
        ]);

        return [$payment, $transaction, $membership];
    }

    private function fakeGateway(Payment $payment, string $transactionStatus): FakePaymentGateway
    {
        $gateway = new FakePaymentGateway;
        $gateway->statusResponse = [
            'transaction_id' => 'midtrans-transaction-1',
            'order_id' => $payment->order_id,
            'gross_amount' => '250000.00',
            'transaction_status' => $transactionStatus,
            'payment_type' => 'bank_transfer',
            'fraud_status' => 'accept',
        ];
        $this->app->instance(PaymentGateway::class, $gateway);

        return $gateway;
    }
}
