<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MembershipPlan extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'duration_days', 'price', 'description', 'is_active'];

    protected function casts(): array
    {
        return ['price' => 'integer', 'duration_days' => 'integer', 'is_active' => 'boolean'];
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp'.number_format($this->price, 0, ',', '.');
    }

    /**
     * @return list<string>
     */
    public function getFeaturesAttribute(): array
    {
        return config('membership.catalog')[$this->name]['features'] ?? [];
    }
}
