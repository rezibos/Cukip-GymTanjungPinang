<?php

namespace App\Http\Controllers;

use App\Contracts\PaymentGateway;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class MidtransWebhookController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(
        Request $request,
        PaymentGateway $gateway,
        PaymentService $paymentService,
    ): Response {
        $transactionId = $request->string('transaction_id')->toString();
        $notificationOrderId = $request->string('order_id')->toString();

        if ($transactionId === '' || $notificationOrderId === '') {
            return response('Invalid notification payload.', 400);
        }

        if (str_starts_with($notificationOrderId, 'payment_notif_test_')) {
            return $this->hasValidTestSignature($request, $notificationOrderId)
                ? response('OK')
                : response('Invalid notification signature.', 403);
        }

        try {
            $statusResponse = $gateway->transactionStatus($transactionId);
            $verifiedOrderId = $statusResponse['order_id'] ?? null;

            if (! is_string($verifiedOrderId) || ! hash_equals($notificationOrderId, $verifiedOrderId)) {
                return response('Notification identity mismatch.', 422);
            }

            $payment = Payment::query()->where('order_id', $verifiedOrderId)->first();

            if (! $payment) {
                return response('Payment not found.', 404);
            }

            $paymentService->processStatus($payment, $statusResponse);
        } catch (Throwable $exception) {
            report($exception);

            return response('Unable to process notification.', 503);
        }

        return response('OK');
    }

    private function hasValidTestSignature(Request $request, string $orderId): bool
    {
        $statusCode = $request->string('status_code')->toString();
        $grossAmount = $request->string('gross_amount')->toString();
        $providedSignature = $request->string('signature_key')->toString();
        $serverKey = config('services.midtrans.server_key');

        if ($statusCode === '' || $grossAmount === '' || $providedSignature === '' || ! is_string($serverKey)) {
            return false;
        }

        $expectedSignature = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

        return hash_equals($expectedSignature, $providedSignature);
    }
}
