<?php

namespace App\Models;

use App\Enums\MembershipStatus;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'phone', 'password', 'role'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin();
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function classBookings(): HasMany
    {
        return $this->hasMany(ClassBooking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // Membership yang sedang aktif (kalau ada), dipakai berulang kali
    public function activeMembership(): ?Membership
    {
        return $this->memberships()
            ->where('status', MembershipStatus::Active)
            ->whereDate('end_date', '>=', now())
            ->latest('end_date')
            ->first();
    }

    public function isMember(): bool
    {
        return $this->activeMembership() !== null;
    }

    // Pernah punya membership apa pun (aktif atau sudah kedaluwarsa) — syarat boleh review
    public function everHadMembership(): bool
    {
        return $this->memberships()
            ->whereIn('status', [MembershipStatus::Active, MembershipStatus::Expired])
            ->exists();
    }

    // Ada transaksi/membership yang masih menunggu pembayaran
    public function hasPendingMembership(): bool
    {
        return $this->memberships()->where('status', 'pending_payment')->exists();
    }
}
