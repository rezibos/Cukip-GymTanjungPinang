<?php

namespace Tests\Feature;

use App\Contracts\PaymentGateway;
use App\Enums\MembershipStatus;
use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use App\Models\MembershipPlan;
use App\Models\Payment;
use App\Models\TrainerSlot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\Fakes\FakePaymentGateway;
use Tests\TestCase;

class PaymentReturnTest extends TestCase
{
    use RefreshDatabase;

    public function test_finish_page_reconciles_verified_midtrans_status(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $plan = MembershipPlan::factory()->create([
            'duration_days' => 30,
            'price' => 250000,
        ]);
        $membership = $user->memberships()->create([
            'membership_plan_id' => $plan->id,
            'quantity' => 1,
            'status' => MembershipStatus::PendingPayment,
        ]);
        $payment = Payment::factory()->for($user)->create([
            'customer_email' => $user->email,
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
        $gateway = new FakePaymentGateway;
        $gateway->statusResponse = [
            'transaction_id' => 'midtrans-transaction-1',
            'order_id' => $payment->order_id,
            'gross_amount' => '250000.00',
            'transaction_status' => 'settlement',
            'payment_type' => 'bank_transfer',
            'fraud_status' => 'accept',
        ];
        $this->app->instance(PaymentGateway::class, $gateway);

        $response = $this->actingAs($user)->get(route('payments.finish', [
            'order_id' => $payment->order_id,
        ]));

        $response->assertOk()
            ->assertViewHas('payment', fn (Payment $viewPayment): bool => $viewPayment->is($payment))
            ->assertSee('Pembayaran berhasil.')
            ->assertSee('data-notification-title="Pembayaran berhasil"', false)
            ->assertSee('Membership kamu sudah aktif.')
            ->assertSee('Konfirmasi pembayaran sudah dikirim ke '.$user->email);
        $this->assertSame($payment->order_id, $gateway->requestedTransactionId);
        $this->assertSame(PaymentStatus::Paid, $payment->fresh()->status);
        $this->assertSame(TransactionStatus::Paid, $transaction->fresh()->status);
        $this->assertSame(MembershipStatus::Active, $membership->fresh()->status);
    }

    public function test_paid_trainer_return_page_shows_a_success_notification(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $slot = TrainerSlot::factory()->create([
            'status' => 'held',
            'held_until' => now()->addMinutes(30),
        ]);
        $payment = Payment::factory()->for($user)->create([
            'customer_email' => $user->email,
            'gross_amount' => 150000,
            'status' => PaymentStatus::Pending,
        ]);
        $payment->trainerBookings()->create([
            'user_id' => $user->id,
            'trainer_slot_id' => $slot->id,
            'amount' => 150000,
            'status' => 'pending_payment',
        ]);
        $gateway = new FakePaymentGateway;
        $gateway->statusResponse = [
            'transaction_id' => 'midtrans-trainer-transaction-1',
            'order_id' => $payment->order_id,
            'gross_amount' => '150000.00',
            'transaction_status' => 'settlement',
            'payment_type' => 'bank_transfer',
            'fraud_status' => 'accept',
        ];
        $this->app->instance(PaymentGateway::class, $gateway);

        $this->actingAs($user)
            ->get(route('payments.finish', ['order_id' => $payment->order_id]))
            ->assertOk()
            ->assertSee('data-notification-title="Pembayaran berhasil"', false)
            ->assertSee('Jadwal latihan privatmu sudah dikonfirmasi.');
    }

    public function test_guest_without_checkout_session_cannot_reconcile_another_customers_payment(): void
    {
        $payment = Payment::factory()->create([
            'gross_amount' => 250000,
            'status' => PaymentStatus::Pending,
        ]);
        $gateway = new FakePaymentGateway;
        $gateway->statusResponse = [
            'order_id' => $payment->order_id,
            'gross_amount' => '250000.00',
            'transaction_status' => 'settlement',
        ];
        $this->app->instance(PaymentGateway::class, $gateway);

        $response = $this->get(route('payments.finish', [
            'order_id' => $payment->order_id,
        ]));

        $response->assertOk()->assertViewHas('payment', null);
        $this->assertNull($gateway->requestedTransactionId);
        $this->assertSame(PaymentStatus::Pending, $payment->fresh()->status);
    }

    public function test_guest_can_resume_a_pending_payment_from_the_status_page(): void
    {
        $payment = Payment::factory()->create([
            'user_id' => null,
            'customer_name' => 'Budi Santoso',
            'customer_email' => 'budi@example.com',
            'checkout_items' => [[
                'membership_plan_id' => MembershipPlan::factory()->create()->id,
                'name' => 'Regular Bulanan',
                'price' => 250000,
                'quantity' => 1,
                'subtotal' => 250000,
            ]],
            'gross_amount' => 250000,
            'snap_token' => 'sandbox-resume-token',
            'status' => PaymentStatus::Pending,
        ]);
        $gateway = new FakePaymentGateway;
        $gateway->statusResponse = [
            'order_id' => $payment->order_id,
            'gross_amount' => '250000.00',
            'transaction_status' => 'pending',
        ];
        $this->app->instance(PaymentGateway::class, $gateway);

        $response = $this->withSession(['guest_payment_ids' => [$payment->id]])
            ->get(route('payments.pending', ['order_id' => $payment->order_id]));

        $response->assertOk()
            ->assertSee('Pesananmu tetap tersimpan')
            ->assertSee('Regular Bulanan')
            ->assertSee('Rp250.000')
            ->assertSee('Lanjutkan pembayaran')
            ->assertSee('sandbox-resume-token');
    }

    public function test_guest_payment_history_only_contains_orders_from_the_current_browser_session(): void
    {
        $accessiblePayment = Payment::factory()->create([
            'user_id' => null,
            'customer_email' => 'budi@example.com',
            'checkout_items' => [[
                'membership_plan_id' => MembershipPlan::factory()->create()->id,
                'name' => 'Paket Milik Budi',
                'price' => 250000,
                'quantity' => 1,
                'subtotal' => 250000,
            ]],
            'gross_amount' => 250000,
        ]);
        Payment::factory()->create([
            'user_id' => null,
            'customer_email' => 'oranglain@example.com',
            'checkout_items' => [[
                'membership_plan_id' => MembershipPlan::factory()->create()->id,
                'name' => 'Paket Rahasia Orang Lain',
                'price' => 500000,
                'quantity' => 1,
                'subtotal' => 500000,
            ]],
            'gross_amount' => 500000,
        ]);

        $response = $this->withSession(['guest_payment_ids' => [$accessiblePayment->id]])
            ->get(route('payments.history'));

        $response->assertOk()
            ->assertSee('Riwayat pesanan')
            ->assertSee('Paket Milik Budi')
            ->assertDontSee('Paket Rahasia Orang Lain');
    }

    public function test_payment_history_groups_orders_by_status_in_collapsible_sections(): void
    {
        $pendingPayment = Payment::factory()->create([
            'user_id' => null,
            'status' => PaymentStatus::Pending,
            'checkout_items' => [[
                'name' => 'Paket Menunggu',
                'price' => 150000,
                'quantity' => 1,
                'subtotal' => 150000,
            ]],
            'gross_amount' => 150000,
        ]);
        $paidPayment = Payment::factory()->create([
            'user_id' => null,
            'status' => PaymentStatus::Paid,
            'checkout_items' => [[
                'name' => 'Paket Berhasil',
                'price' => 250000,
                'quantity' => 1,
                'subtotal' => 250000,
            ]],
            'gross_amount' => 250000,
        ]);
        $failedPayment = Payment::factory()->create([
            'user_id' => null,
            'status' => PaymentStatus::Failed,
            'checkout_items' => [[
                'name' => 'Paket Gagal',
                'price' => 350000,
                'quantity' => 1,
                'subtotal' => 350000,
            ]],
            'gross_amount' => 350000,
        ]);

        $response = $this->withSession([
            'guest_payment_ids' => [$pendingPayment->id, $paidPayment->id, $failedPayment->id],
        ])->get(route('payments.history'));

        $response->assertOk()
            ->assertSeeInOrder(['Menunggu pembayaran', 'Berhasil', 'Gagal / tidak selesai'])
            ->assertSee('data-payment-group="pending"', false)
            ->assertSee('data-payment-group="paid"', false)
            ->assertSee('data-payment-group="failed"', false)
            ->assertSee('Paket Menunggu')
            ->assertSee('Paket Berhasil')
            ->assertSee('Paket Gagal')
            ->assertSee('Lanjutkan pembayaran')
            ->assertSee('Lihat detail');

        $this->assertMatchesRegularExpression(
            '/<details\s+data-payment-group="pending"[^>]*\sopen\s*>/',
            $response->getContent(),
        );
        $this->assertDoesNotMatchRegularExpression(
            '/<details\s+data-payment-group="(?:paid|failed)"[^>]*\sopen\s*>/',
            $response->getContent(),
        );
    }
}
