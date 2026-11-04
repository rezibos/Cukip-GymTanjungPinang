<?php

namespace App\Enums;

enum BookingStatus: string
{
    case Booked = 'booked';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Booked => 'Terdaftar',
            self::Cancelled => 'Dibatalkan',
        };
    }
}