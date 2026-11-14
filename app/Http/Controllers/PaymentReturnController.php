<?php

namespace App\Http\Controllers;

use App\Contracts\PaymentGateway;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Throwable;

class PaymentReturnController extends Controller
{
    public function show(
        Request $request,
        string $result,
        PaymentGateway $gateway,
        PaymentService $paymentService,
    ): View {
        $payment = null;
        $transaction = null;
        $orderId = $request->string('order_id')->toString();

        if ($orderId !== '') {
            $payment = $this->accessiblePayments($request)
                ->where('order_id', $orderId)
                ->first();

            if ($payment) {
                try {
                    $paymentService->processStatus($payment, $gateway->transactionStatus($orderId));
                    $payment->refresh();

                    if ($payment->status->value === 'paid' && $payment->creates_account) {
                        Auth::login($payment->user);
                        $request->session()->regenerate();
                    }
                } catch (Throwable $exception) {
                    report($exception);
                }

                $payment->loadMissing(['transactions.membership.plan']);
            }

            $transaction = $payment?->transactions()->oldest('id')->first();
        }

        return view('payments.return', compact('payment', 'result', 'transaction'));
    }

    public function history(Request $request): View
    {
        $payments = $this->accessiblePayments($request)
            ->with(['transactions.membership.plan'])
            ->latest()
            ->get();

        return view('payments.history', compact('payments'));
    }

    private function accessiblePayments(Request $request): Builder
    {
        $accessiblePaymentIds = $request->session()->get('guest_payment_ids', []);

        return Payment::query()->where(function (Builder $query) use ($accessiblePaymentIds, $request): void {
            $query->whereKey($accessiblePaymentIds);

            if ($request->user()) {
                $query->orWhereBelongsTo($request->user());
            }
        });
    }
}
