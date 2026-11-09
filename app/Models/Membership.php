<?php

namespace App\Models;

use App\Enums\MembershipStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Membership extends Model
{
    protected $fillable = ['user_id', 'membership_plan_id', 'quantity', 'status', 'start_date', 'end_date'];

    protected function casts(): array
    {
        return [
            'status' => MembershipStatus::class,
            'quantity' => 'integer',
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

    public function effectiveStatus(): MembershipStatus
    {
        if (in_array($this->status, [MembershipStatus::PendingPayment, MembershipStatus::Cancelled], true)) {
            return $this->status;
        }

        if ($this->end_date?->lessThan(Carbon::today())) {
            return MembershipStatus::Expired;
        }

        if ($this->start_date?->greaterThan(Carbon::today())) {
            return MembershipStatus::Upcoming;
        }

        if (
            $this->start_date?->lessThanOrEqualTo(Carbon::today())
            && $this->end_date?->greaterThanOrEqualTo(Carbon::today())
        ) {
            return MembershipStatus::Active;
        }

        return $this->status;
    }

    public function isActive(): bool
    {
        return $this->effectiveStatus() === MembershipStatus::Active
            && $this->start_date?->lessThanOrEqualTo(Carbon::today())
            && $this->end_date?->greaterThanOrEqualTo(Carbon::today());
    }

    public static function synchronizeDateStatuses(): int
    {
        $expiredCount = static::query()
            ->whereIn('status', [MembershipStatus::Active, MembershipStatus::Upcoming])
            ->whereDate('end_date', '<', Carbon::today())
            ->update(['status' => MembershipStatus::Expired]);

        $upcomingCount = static::query()
            ->whereIn('status', [MembershipStatus::Active, MembershipStatus::Expired])
            ->whereDate('start_date', '>', Carbon::today())
            ->whereDate('end_date', '>=', Carbon::today())
            ->update(['status' => MembershipStatus::Upcoming]);

        $activeCount = static::query()
            ->whereIn('status', [MembershipStatus::Expired, MembershipStatus::Upcoming])
            ->whereDate('start_date', '<=', Carbon::today())
            ->whereDate('end_date', '>=', Carbon::today())
            ->update(['status' => MembershipStatus::Active]);

        return $expiredCount + $upcomingCount + $activeCount;
    }

    // Dipanggil saat admin menandai transaksi lunas
    public function activate(): void
    {
        $start = now()->toDateString();
        $end = now()->addDays($this->plan->duration_days * $this->quantity)->toDateString();

        $this->update([
            'status' => MembershipStatus::Active,
            'start_date' => $start,
            'end_date' => $end,
        ]);
    }
}
