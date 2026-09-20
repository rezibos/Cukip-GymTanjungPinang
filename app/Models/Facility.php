<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Facility extends Model
{
    protected $fillable = ['gym_id', 'name', 'description'];

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }
}