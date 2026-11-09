<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class PaymentController extends Controller
{
    public function store(Request $request, Payment $payment, PaymentService $paymentService): RedirectResponse
    {
        abort_unless($payment->user_id === $request->user()->id, 403);

        $transaction = $payment->transactions()->oldest('id')->firstOrFail();

        if ($payment->status !== PaymentStatus::Pending) {
            return redirect()->route('membership.invoice', $transaction)
                ->with('error', 'Pembayaran ini sudah selesai dan tidak dapat dimulai kembali.');
        }

        try {
            $payment = $paymentService->start($payment);
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('membership.invoice', $transaction)
                ->with('error', 'Midtrans belum dapat dihubungi. Silakan coba lagi.');
        }

        return redirect()->away($payment->redirect_url);
    }
}
