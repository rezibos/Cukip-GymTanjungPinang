<?php

namespace App\Contracts;

interface PaymentGateway
{
    /**
     * @param  array<string, mixed>  $parameters
     * @return array<string, mixed>
     */
    public function createTransaction(array $parameters, string $idempotencyKey): array;

    /**
     * @return array<string, mixed>
     */
    public function transactionStatus(string $transactionId): array;
}
