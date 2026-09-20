<?php

namespace App\Enums;

enum Region: string
{
    case Tanjungpinang = 'Tanjungpinang';
    case Bintan = 'Bintan';

    /** Daftar kecamatan/area untuk filter opsional. */
    public function districts(): array
    {
        return match ($this) {
            self::Tanjungpinang => [
                'Tanjungpinang Kota',
                'Tanjungpinang Barat',
                'Tanjungpinang Timur',
                'Bukit Bestari',
            ],
            self::Bintan => [
                'Bintan Timur',
                'Bintan Utara',
                'Bintan Pesisir',
                'Gunung Kijang',
                'Teluk Sebong',
                'Teluk Bintan',
                'Toapaya',
                'Seri Kuala Lobam',
                'Mantang',
                'Tambelan',
            ],
        };
    }

    /** Untuk dropdown: ['Tanjungpinang' => 'Tanjungpinang', ...] */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $r) => [$r->value => $r->value])
            ->all();
    }
}