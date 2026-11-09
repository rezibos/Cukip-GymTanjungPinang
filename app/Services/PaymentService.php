<?php

namespace App\Services;

use App\Contracts\PaymentGateway;
use App\Enums\MembershipStatus;
use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

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

        if ($payment->transactions->isEmpty()) {
            throw new RuntimeException('Pembayaran tidak memiliki rincian transaksi.');
        }

        $itemDetails = $payment->transactions->map(function ($transaction): array {
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
                'first_name' => Str::limit($payment->user->name, 50, ''),
                'email' => $payment->user->email,
                'phone' => $payment->user->phone,
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

        DB::transaction(function () use ($payment, $statusResponse, $midtransStatus): void {
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
                return;
            }

            if ($resolvedStatus === PaymentStatus::Paid) {
                $this->markPaid($lockedPayment);

                return;
            }

            if ($resolvedStatus === PaymentStatus::Refunded) {
                $this->markUnavailable($lockedPayment, $resolvedStatus, 'Pembayaran dikembalikan oleh Midtrans.');

                return;
            }

            if ($lockedPayment->status !== PaymentStatus::Paid && $resolvedStatus !== PaymentStatus::Pending) {
                $message = $resolvedStatus === PaymentStatus::Expired
                    ? 'Pembayaran kedaluwarsa di Midtrans.'
                    : 'Pembayaran gagal atau dibatalkan di Midtrans.';

                $this->markUnavailable($lockedPayment, $resolvedStatus, $message);
            }
        });
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
        if ($payment->status === PaymentStatus::Paid) {
            return;
        }

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
