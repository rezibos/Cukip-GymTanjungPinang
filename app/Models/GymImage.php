<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class GymImage extends Model
{
    protected $fillable = ['gym_id', 'image', 'caption'];

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->image);
    }
}