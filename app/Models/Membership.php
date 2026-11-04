<?php

namespace App\Models;

use App\Enums\MembershipStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Membership extends Model
{
    protected $fillable = ['user_id', 'membership_plan_id', 'status', 'start_date', 'end_date'];

    protected function casts(): array
    {
        return [
            'status' => MembershipStatus::class,
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(MembershipPlan::class, 'membership_plan_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function isActive(): bool
    {
        return $this->status === MembershipStatus::Active
            && $this->end_date !== null
            && $this->end_date->isFuture();
    }

    // Dipanggil saat admin menandai transaksi lunas
    public function activate(): void
    {
        $start = now()->toDateString();
        $end = now()->addDays($this->plan->duration_days)->toDateString();

        $this->update([
            'status' => MembershipStatus::Active,
            'start_date' => $start,
            'end_date' => $end,
        ]);
    }
}