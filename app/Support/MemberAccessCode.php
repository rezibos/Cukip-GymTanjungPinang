<?php

namespace App\Support;

use App\Models\Membership;
use App\Models\User;

class MemberAccessCode
{
    public static function make(User $user, Membership $membership): string
    {
        $signature = hash_hmac(
            'sha256',
            $user->getKey().'|'.$membership->getKey(),
            (string) config('app.key'),
        );

        return 'CGM-'.strtoupper(substr($signature, 0, 12));
    }

    /**
     * @return list<bool>
     */
    public static function cells(string $accessCode): array
    {
        $seed = hash('sha256', $accessCode);

        return collect(range(0, 288))
            ->map(fn (int $index): bool => hexdec($seed[$index % strlen($seed)]) % 2 === 0)
            ->all();
    }
}
