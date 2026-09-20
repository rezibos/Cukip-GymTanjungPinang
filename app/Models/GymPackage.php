<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GymPackage extends Model
{
    protected $fillable = ['gym_id', 'name', 'duration', 'price', 'description'];

    protected function casts(): array
    {
        return ['price' => 'integer'];
    }

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    // Contoh pemakaian: {{ $package->formatted_price }} -> Rp250.000
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp' . number_format($this->price, 0, ',', '.');
    }
}