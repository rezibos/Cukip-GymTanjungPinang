<?php

namespace App\Enums;

enum DayOfWeek: string
{
    case Monday = 'senin';
    case Tuesday = 'selasa';
    case Wednesday = 'rabu';
    case Thursday = 'kamis';
    case Friday = 'jumat';
    case Saturday = 'sabtu';
    case Sunday = 'minggu';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    // Dipakai saat generate sesi: mencocokkan hari dalam seminggu dari objek Carbon
    public static function fromCarbon(\Carbon\Carbon $date): self
    {
        // Carbon: 0 = Minggu, 1 = Senin, ... 6 = Sabtu
        $map = [0 => self::Sunday, 1 => self::Monday, 2 => self::Tuesday, 3 => self::Wednesday,
                4 => self::Thursday, 5 => self::Friday, 6 => self::Saturday];

        return $map[$date->dayOfWeek];
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $d) => [$d->value => $d->label()])->all();
    }
}