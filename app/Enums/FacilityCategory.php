<?php

namespace App\Enums;

enum FacilityCategory: string
{
    case Strength = 'kekuatan';
    case Cardio = 'kardio';
    case Recovery = 'pemulihan';
    case Support = 'kenyamanan';

    public function label(): string
    {
        return match ($this) {
            self::Strength => 'Latihan kekuatan',
            self::Cardio => 'Kardio & stamina',
            self::Recovery => 'Pemulihan',
            self::Support => 'Fasilitas pendukung',
        };
    }

    public function number(): string
    {
        return match ($this) {
            self::Strength => '01',
            self::Cardio => '02',
            self::Recovery => '03',
            self::Support => '04',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Strength => 'Dari repetisi pertama sampai set terakhir. Kenali peralatan yang bisa kamu gunakan untuk menyusun latihan beban sesuai kemampuan dan targetmu.',
            self::Cardio => 'Temukan ritme gerakmu. Lengkapi rutinitas dengan sesi kardio yang durasi dan intensitasnya bisa kamu sesuaikan secara bertahap.',
            self::Recovery => 'Luangkan waktu untuk mengembalikan kenyamanan tubuh setelah latihan. Jelajahi fasilitas yang mendukung jeda dan pemulihanmu.',
            self::Support => 'Latihan terasa lebih praktis saat kebutuhan lainnya sudah tersedia. Dari persiapan sebelum latihan sampai waktunya pulang.',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $category): array => [$category->value => $category->label()])
            ->all();
    }
}
