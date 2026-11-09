<?php

namespace Tests\Fakes;

use App\Contracts\PaymentGateway;

class FakePaymentGateway implements PaymentGateway
{
    /** @var array<string, mixed> */
    public array $createResponse = [
        'token' => 'sandbox-snap-token',
        'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/test-token',
    ];

    /** @var array<string, mixed> */
    public array $statusResponse = [];

    /** @var array<string, mixed>|null */
    public ?array $createdParameters = null;

    public ?string $createdIdempotencyKey = null;

    public ?string $requestedTransactionId = null;

    public function createTransaction(array $parameters, string $idempotencyKey): array
    {
        $this->createdParameters = $parameters;
        $this->createdIdempotencyKey = $idempotencyKey;

        return $this->createResponse;
    }

    public function transactionStatus(string $transactionId): array
    {
        $this->requestedTransactionId = $transactionId;

        return $this->statusResponse;
    }
}
