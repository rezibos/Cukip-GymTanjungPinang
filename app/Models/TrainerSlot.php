<?php

namespace App\Models;

use Database\Factories\TrainerSlotFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainerSlot extends Model
{
    /** @use HasFactory<TrainerSlotFactory> */
    use HasFactory;

    protected $fillable = ['personal_trainer_id', 'starts_at', 'ends_at', 'status', 'held_until'];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'held_until' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::saving(function (TrainerSlot $slot): void {
            $sessionMinutes = (int) $slot->trainer()->value('session_minutes');

            if (in_array($sessionMinutes, [120, 180], true)) {
                $slot->ends_at = $slot->starts_at->copy()->addMinutes($sessionMinutes);
            }
        });
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(PersonalTrainer::class, 'personal_trainer_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(TrainerBooking::class);
    }
}
