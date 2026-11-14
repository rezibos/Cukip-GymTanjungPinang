<?php

namespace App\Services;

use App\Contracts\PaymentGateway;
use App\Enums\MembershipStatus;
use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use App\Mail\PaymentSuccessfulMail;
use App\Models\MembershipPlan;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class PaymentService
{
    public function __construct(
        private readonly PaymentGateway $gateway,
    ) {}

    public function start(Payment $payment): Payment
    {
        if ($payment->status !== PaymentStatus::Pending) {
            throw new RuntimeException('Pembayaran ini tidak dapat dimulai kembali.');
        }

        if (filled($payment->redirect_url)) {
            return $payment;
        }

        $payment->loadMissing(['user', 'transactions.membership.plan']);

        $checkoutItems = collect($payment->checkout_items ?? []);

        if ($payment->transactions->isEmpty() && $checkoutItems->isEmpty()) {
            throw new RuntimeException('Pembayaran tidak memiliki rincian transaksi.');
        }

        $itemDetails = $checkoutItems->isNotEmpty()
            ? $checkoutItems->map(fn (array $item): array => [
                'id' => 'plan-'.$item['membership_plan_id'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'name' => Str::limit($item['name'], 50, ''),
            ])->all()
            : $payment->transactions->map(function ($transaction): array {
                return [
                    'id' => $transaction->invoice_number,
                    'price' => intdiv($transaction->amount, $transaction->quantity),
                    'quantity' => $transaction->quantity,
                    'name' => Str::limit($transaction->membership->plan->name, 50, ''),
                ];
            })->all();

        $response = $this->gateway->createTransaction([
            'transaction_details' => [
                'order_id' => $payment->order_id,
                'gross_amount' => $payment->gross_amount,
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => Str::limit($payment->customer_name ?? $payment->user?->name ?? '', 50, ''),
                'email' => $payment->customer_email ?? $payment->user?->email,
                'phone' => $payment->customer_phone ?? $payment->user?->phone,
            ],
            'callbacks' => [
                'finish' => route('payments.finish'),
            ],
        ], $payment->order_id);

        $snapToken = $response['token'] ?? null;
        $redirectUrl = $response['redirect_url'] ?? null;

        if (! is_string($snapToken) || ! is_string($redirectUrl)) {
            throw new RuntimeException('Midtrans tidak mengembalikan token pembayaran yang valid.');
        }

        $payment->update([
            'snap_token' => $snapToken,
            'redirect_url' => $redirectUrl,
        ]);

        return $payment->refresh();
    }

    /**
     * @param  array<string, mixed>  $statusResponse
     */
    public function processStatus(Payment $payment, array $statusResponse): void
    {
        $orderId = $statusResponse['order_id'] ?? null;
        $grossAmount = $statusResponse['gross_amount'] ?? null;
        $midtransStatus = $statusResponse['transaction_status'] ?? null;

        if ($orderId !== $payment->order_id || ! is_numeric($grossAmount)) {
            throw new RuntimeException('Identitas pembayaran Midtrans tidak sesuai.');
        }

        if ((int) $grossAmount !== $payment->gross_amount || ! is_string($midtransStatus)) {
            throw new RuntimeException('Nominal atau status pembayaran Midtrans tidak sesuai.');
        }

        $shouldSendPaymentConfirmation = DB::transaction(function () use ($payment, $statusResponse, $midtransStatus): bool {
            $lockedPayment = Payment::query()
                ->with(['transactions.membership.plan'])
                ->lockForUpdate()
                ->findOrFail($payment->id);

            $fraudStatus = $statusResponse['fraud_status'] ?? null;
            $resolvedStatus = $this->resolveStatus($midtransStatus, is_string($fraudStatus) ? $fraudStatus : null);

            $lockedPayment->update([
                'midtrans_transaction_id' => $statusResponse['transaction_id'] ?? $lockedPayment->midtrans_transaction_id,
                'midtrans_status' => $midtransStatus,
                'payment_type' => $statusResponse['payment_type'] ?? $lockedPayment->payment_type,
                'fraud_status' => $fraudStatus,
                'last_notification' => $statusResponse,
            ]);

            if ($resolvedStatus === null) {
                return false;
            }

            if ($resolvedStatus === PaymentStatus::Paid) {
                $this->markPaid($lockedPayment);

                return true;
            }

            if ($resolvedStatus === PaymentStatus::Refunded) {
                $this->markUnavailable($lockedPayment, $resolvedStatus, 'Pembayaran dikembalikan oleh Midtrans.');

                return false;
            }

            if ($lockedPayment->status !== PaymentStatus::Paid && $resolvedStatus !== PaymentStatus::Pending) {
                $message = $resolvedStatus === PaymentStatus::Expired
                    ? 'Pembayaran kedaluwarsa di Midtrans.'
                    : 'Pembayaran gagal atau dibatalkan di Midtrans.';

                $this->markUnavailable($lockedPayment, $resolvedStatus, $message);
            }

            return false;
        });

        if ($shouldSendPaymentConfirmation) {
            $this->sendPaymentConfirmation($payment->id);
        }
    }

    private function resolveStatus(string $transactionStatus, ?string $fraudStatus): ?PaymentStatus
    {
        return match ($transactionStatus) {
            'settlement' => PaymentStatus::Paid,
            'capture' => $fraudStatus === 'accept' ? PaymentStatus::Paid : PaymentStatus::Pending,
            'pending' => PaymentStatus::Pending,
            'deny', 'cancel', 'failure' => PaymentStatus::Failed,
            'expire' => PaymentStatus::Expired,
            'refund' => PaymentStatus::Refunded,
            'partial_refund' => null,
            default => null,
        };
    }

    private function markPaid(Payment $payment): void
    {
        if ($payment->status !== PaymentStatus::Paid) {
            $this->createGuestCheckoutRecords($payment);

            $payment->update([
                'status' => PaymentStatus::Paid,
                'paid_at' => now(),
            ]);

            foreach ($payment->transactions as $transaction) {
                $transaction->update([
                    'status' => TransactionStatus::Paid,
                    'paid_at' => now(),
                    'admin_note' => null,
                ]);

                if ($transaction->membership->status === MembershipStatus::PendingPayment) {
                    $transaction->membership->activate();
                }
            }
        }
    }

    private function createGuestCheckoutRecords(Payment $payment): void
    {
        $checkoutItems = collect($payment->checkout_items ?? []);

        if ($checkoutItems->isEmpty() || $payment->transactions->isNotEmpty()) {
            return;
        }

        $user = $payment->user;

        if (! $user) {
            $accountPassword = Str::password(12);
            $user = User::query()->firstOrCreate(
                ['email' => $payment->customer_email],
                [
                    'name' => $payment->customer_name,
                    'phone' => $payment->customer_phone,
                    'password' => $accountPassword,
                    'role' => 'user',
                ],
            );

            $payment->update([
                'user_id' => $user->id,
                'creates_account' => $user->wasRecentlyCreated,
                'account_password' => $user->wasRecentlyCreated ? $accountPassword : null,
            ]);
            $payment->setRelation('user', $user);
        }

        foreach ($checkoutItems as $item) {
            $plan = MembershipPlan::query()->findOrFail($item['membership_plan_id']);
            $membership = $user->memberships()->create([
                'membership_plan_id' => $plan->id,
                'quantity' => $item['quantity'],
                'status' => MembershipStatus::PendingPayment,
            ]);
            $membership->transactions()->create([
                'user_id' => $user->id,
                'payment_id' => $payment->id,
                'quantity' => $item['quantity'],
                'checkout_batch' => $payment->checkout_batch,
                'amount' => $item['subtotal'],
                'status' => TransactionStatus::Pending,
            ]);
        }

        $payment->load('transactions.membership.plan');
    }

    private function sendPaymentConfirmation(int $paymentId): void
    {
        $payment = Payment::query()
            ->with(['user', 'transactions.membership.plan'])
            ->findOrFail($paymentId);
        $accountPassword = $payment->creates_account
            && $payment->credentials_sent_at === null
            && is_string($payment->account_password)
            && $payment->account_password !== ''
                ? $payment->account_password
                : null;
        $reservation = Payment::query()
            ->whereKey($paymentId)
            ->whereNull('payment_confirmation_sent_at');
        $timestamps = ['payment_confirmation_sent_at' => now()];

        if ($accountPassword !== null) {
            $reservation->whereNull('credentials_sent_at');
            $timestamps['credentials_sent_at'] = now();
        }

        $reserved = $reservation->update($timestamps);

        if ($reserved !== 1) {
            return;
        }

        $recipientEmail = $payment->customer_email ?? $payment->user?->email;

        if (! is_string($recipientEmail) || $recipientEmail === '') {
            $this->releaseMailReservation($payment, $accountPassword !== null);

            return;
        }

        try {
            Mail::to($recipientEmail)->send(new PaymentSuccessfulMail($payment, $accountPassword));

            if ($accountPassword !== null) {
                $payment->update(['account_password' => null]);
            }
        } catch (Throwable $exception) {
            $this->releaseMailReservation($payment, $accountPassword !== null);
            report($exception);
        }
    }

    private function releaseMailReservation(Payment $payment, bool $includedCredentials): void
    {
        $attributes = ['payment_confirmation_sent_at' => null];

        if ($includedCredentials) {
            $attributes['credentials_sent_at'] = null;
        }

        $payment->update($attributes);
    }

    private function markUnavailable(Payment $payment, PaymentStatus $status, string $message): void
    {
        $payment->update(['status' => $status]);

        foreach ($payment->transactions as $transaction) {
            $transaction->update([
                'status' => TransactionStatus::Rejected,
                'admin_note' => $message,
            ]);

            $transaction->membership->update(['status' => MembershipStatus::Cancelled]);
        }
    }
}
