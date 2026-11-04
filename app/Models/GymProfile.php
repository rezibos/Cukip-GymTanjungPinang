<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class GymProfile extends Model
{
    protected $fillable = [
        'name', 'description', 'address', 'latitude', 'longitude',
        'opening_hours', 'phone', 'whatsapp', 'image',
    ];

    protected function casts(): array
    {
        return ['latitude' => 'float', 'longitude' => 'float'];
    }

    // Selalu ambil baris pertama (dan satu-satunya)
    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'name' => 'CukipGym',
            'description' => 'Gym lokal dengan fasilitas lengkap.',
            'address' => '-',
            'opening_hours' => '06:00 - 22:00',
        ]);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::disk('public')->url($this->image) : null;
    }

    public function getWhatsappUrlAttribute(): ?string
    {
        if (! $this->whatsapp) {
            return null;
        }

        $number = preg_replace('/\D/', '', $this->whatsapp);
        if (str_starts_with($number, '0')) {
            $number = '62' . substr($number, 1);
        }

        return 'https://wa.me/' . $number;
    }

    public function getMapsUrlAttribute(): ?string
    {
        if ($this->latitude === null || $this->longitude === null) {
            return null;
        }

        return 'https://www.google.com/maps?q=' . $this->latitude . ',' . $this->longitude;
    }
}