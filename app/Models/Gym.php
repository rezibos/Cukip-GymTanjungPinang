<?php

namespace App\Models;

use App\Enums\Region;
use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Gym extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'address',
        'region', 'district', 'phone', 'whatsapp',
        'latitude', 'longitude', 'opening_hours', 'image',
    ];

    protected function casts(): array
    {
        return [
            'region' => Region::class,
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    /* ---------- Relasi ---------- */

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(GymPackage::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(GymImage::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // Hanya review yang berstatus approved (dipakai untuk tampilan dan rata-rata rating)
    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('status', Review::STATUS_APPROVED);
    }

    /* ---------- Accessor (dipakai sebagai properti di Blade) ---------- */

    // $gym->image_url
    protected function imageUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->image ? Storage::disk('public')->url($this->image) : null
        );
    }

    // $gym->whatsapp_url  -> link WhatsApp Click-to-Chat
    protected function whatsappUrl(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->whatsapp) {
                return null;
            }

            $number = preg_replace('/\D/', '', $this->whatsapp); // buang selain angka
            if (str_starts_with($number, '0')) {
                $number = '62' . substr($number, 1);              // 08xx -> 628xx
            }

            $message = 'Halo, saya mendapatkan informasi gym ini dari GymPinang. '
                . 'Saya ingin mengetahui informasi lebih lanjut mengenai paket gym.';

            return 'https://wa.me/' . $number . '?text=' . rawurlencode($message);
        });
    }

    // $gym->maps_url  -> link Google Maps berdasarkan koordinat
    protected function mapsUrl(): Attribute
    {
        return Attribute::get(function () {
            if ($this->latitude === null || $this->longitude === null) {
                return null;
            }

            return 'https://www.google.com/maps?q=' . $this->latitude . ',' . $this->longitude;
        });
    }

    /* ---------- Scope (query yang bisa dipakai ulang) ---------- */

    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        return $keyword ? $query->where('name', 'like', '%' . $keyword . '%') : $query;
    }

    // Data yang dibutuhkan kartu gym. Eager loading + agregat dalam satu query.
    public function scopeWithCardData(Builder $query): Builder
    {
        return $query->with('category')
            ->withAvg('approvedReviews', 'rating')   // -> approved_reviews_avg_rating
            ->withCount('approvedReviews')           // -> approved_reviews_count
            ->withMin('packages', 'price');          // -> packages_min_price
    }

    public function scopeInRegion(Builder $query, ?string $region): Builder
    {
        return $region ? $query->where('region', $region) : $query;
    }

    public function scopeInCategory(Builder $query, ?string $categorySlug): Builder
    {
        return $categorySlug
            ? $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug))
            : $query;
    }

    // Untuk fitur "Gym Terdekat": menambah kolom `distance` (km) dengan rumus Haversine.
    // Pemakaian: Gym::withDistance($lat, $lng)->orderBy('distance')->get();
    public function scopeWithDistance(Builder $query, float $lat, float $lng): Builder
    {
        $haversine = '(6371 * acos(LEAST(1, GREATEST(-1,
            cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?))
            + sin(radians(?)) * sin(radians(latitude))
        ))))';

        // Pilih semua kolom gyms hanya jika belum ada select lain
        if (empty($query->getQuery()->columns)) {
            $query->select('gyms.*');
        }

        return $query
            ->selectRaw($haversine . ' as distance', [$lat, $lng, $lat])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');
    }

    // Hanya gym yang sudah punya koordinat
    public function scopeWithCoordinates(Builder $query): Builder
    {
        return $query->whereNotNull('latitude')->whereNotNull('longitude');
    }

// Data satu marker untuk JavaScript. Dipakai homepage, detail gym, dan halaman /map.
// Rating ikut terisi jika query memakai withCardData().
    public function toMapMarker(bool $withLink = true): array
    {
        return [
            'name' => $this->name,
            'region' => $this->region->value,
            'district' => $this->district,
            'rating' => $this->approved_reviews_avg_rating !== null
                ? round($this->approved_reviews_avg_rating, 1)
                : null,
            'reviews' => $this->approved_reviews_count,
            'lat' => $this->latitude,
            'lng' => $this->longitude,
            'url' => $withLink ? route('gyms.show', $this) : null,
        ];
    }
}