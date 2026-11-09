<?php

namespace App\Services;

use App\Contracts\PaymentGateway;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use RuntimeException;

class MidtransGateway implements PaymentGateway
{
    public function __construct(
        private readonly ?string $serverKey = null,
    ) {}

    public function createTransaction(array $parameters, string $idempotencyKey): array
    {
        $this->configure();
        Config::$paymentIdempotencyKey = $idempotencyKey;

        return get_object_vars(Snap::createTransaction($parameters));
    }

    public function transactionStatus(string $transactionId): array
    {
        $this->configure();
        Config::$paymentIdempotencyKey = null;

        return get_object_vars(Transaction::status($transactionId));
    }

    private function configure(): void
    {
        $serverKey = $this->serverKey ?? config('services.midtrans.server_key');

        if (! is_string($serverKey) || blank($serverKey)) {
            throw new RuntimeException('MIDTRANS_SERVER_KEY belum dikonfigurasi.');
        }

        Config::$serverKey = $serverKey;
        Config::$clientKey = config('services.midtrans.client_key');
        Config::$isProduction = (bool) config('services.midtrans.is_production', false);
        Config::$isSanitized = (bool) config('services.midtrans.is_sanitized', true);
        Config::$is3ds = (bool) config('services.midtrans.is_3ds', true);
        Config::$curlOptions = [
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_HTTPHEADER => [],
        ];
    }
}
