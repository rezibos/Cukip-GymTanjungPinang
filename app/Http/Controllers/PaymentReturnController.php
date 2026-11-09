<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentReturnController extends Controller
{
    public function show(Request $request, string $result): View
    {
        $payment = null;
        $transaction = null;
        $orderId = $request->string('order_id')->toString();

        if ($orderId !== '' && $request->user()) {
            $payment = Payment::query()
                ->whereBelongsTo($request->user())
                ->where('order_id', $orderId)
                ->first();

            $transaction = $payment?->transactions()->oldest('id')->first();
        }

        return view('payments.return', compact('payment', 'result', 'transaction'));
    }
}
