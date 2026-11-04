<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassSession extends Model
{
    protected $fillable = ['gym_class_id', 'date', 'capacity'];

    protected function casts(): array
    {
        return ['date' => 'date', 'capacity' => 'integer'];
    }

    public function gymClass(): BelongsTo
    {
        return $this->belongsTo(GymClass::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(ClassBooking::class);
    }

    public function activeBookings(): HasMany
    {
        return $this->hasMany(ClassBooking::class)->where('status', 'booked');
    }

    public function remainingSlots(): int
    {
        return max(0, $this->capacity - $this->activeBookings()->count());
    }

    public function isFull(): bool
    {
        return $this->remainingSlots() <= 0;
    }
}