<?php

namespace App\Models;

use Database\Factories\GymProfileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class GymProfile extends Model
{
    /** @use HasFactory<GymProfileFactory> */
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'address', 'latitude', 'longitude',
        'opening_hours', 'phone', 'whatsapp', 'image',
        'hero_image', 'hero_eyebrow', 'hero_title_primary', 'hero_title_secondary',
        'hero_description', 'hero_cta_label', 'hero_about_label',
        'about_eyebrow', 'about_location_label', 'about_title_line_one',
        'about_title_line_two', 'about_title_line_three', 'about_description_primary',
        'about_description_secondary', 'about_gallery_image_one',
        'about_gallery_image_two', 'about_gallery_image_three',
        'about_gallery_image_four', 'about_gallery_image_five',
        'about_gallery_eyebrow', 'about_gallery_title', 'footer_description',
        'footer_location', 'footer_sponsors',
    ];

    private const CONTENT_DEFAULTS = [
        'hero_eyebrow' => 'Gym lokal Tanjungpinang',
        'hero_title_primary' => 'Bukan cuma.',
        'hero_title_secondary' => 'Latihan.',
        'hero_description' => 'Ruang untuk membangun tubuh, disiplin, dan versi terbaik dirimu. Mulai hari ini satu repetisi, satu progres.',
        'hero_cta_label' => 'Mulai sekarang',
        'hero_about_label' => 'Jelajahi GymPinang',
        'about_eyebrow' => 'Tentang CukipGym',
        'about_location_label' => 'Tanjungpinang, Indonesia',
        'about_title_line_one' => 'Tempatmu',
        'about_title_line_two' => 'menjadi',
        'about_title_line_three' => 'lebih kuat.',
        'about_description_primary' => 'Dari latihan pertamamu sampai rutinitas yang kamu banggakan. CukipGym adalah ruang untuk bergerak, membangun kekuatan, dan terus berkembang.',
        'about_description_secondary' => 'Temukan peralatan yang kamu butuhkan dan pilihan membership yang sesuai. Mulai dari kemampuanmu hari ini, lalu jalani progresmu satu sesi demi satu sesi.',
        'about_gallery_eyebrow' => 'Ruang untuk progresmu',
        'about_gallery_title' => "Latihanmu.\nRitmemu.",
        'footer_description' => 'Tempat latihan, kelas kebugaran, dan komunitas untuk membantu kamu bergerak lebih konsisten.',
        'footer_location' => 'Gym lokal Tanjungpinang',
    ];

    private const IMAGE_DEFAULTS = [
        'hero_image' => 'images/gympinang-hero-color.png',
        'about_gallery_image_one' => 'images/gym-equipment-editorial.webp',
        'about_gallery_image_two' => 'images/gym-cable-row-editorial.webp',
        'about_gallery_image_three' => 'images/gym-kettlebell-editorial.webp',
        'about_gallery_image_four' => 'images/gympinang-hero-color.png',
        'about_gallery_image_five' => 'images/gym-equipment-editorial.webp',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'footer_sponsors' => 'array',
        ];
    }

    // Selalu ambil baris pertama (dan satu-satunya)
    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'name' => 'CukipGym',
            'description' => 'Gym lokal dengan fasilitas lengkap.',
            'address' => '-',
            'opening_hours' => '06:00 - 22:00',
        ]);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::disk('public')->url($this->image) : null;
    }

    public function content(string $key): mixed
    {
        $value = $this->getAttribute($key);

        return filled($value) ? $value : (self::CONTENT_DEFAULTS[$key] ?? null);
    }

    public function contentImageUrl(string $key): string
    {
        $path = $this->getAttribute($key);

        return filled($path)
            ? Storage::disk('public')->url($path)
            : asset(self::IMAGE_DEFAULTS[$key] ?? 'images/gympinang-hero-color.png');
    }

    /**
     * @return array<int, array{name: string, tagline: ?string, logo: ?string, url: ?string, is_active: bool}>
     */
    public function footerSponsors(bool $onlyActive = true): array
    {
        $sponsors = $this->footer_sponsors;

        if (! is_array($sponsors)) {
            $sponsors = [
                ['name' => 'FORMA.', 'tagline' => null, 'logo' => null, 'url' => null, 'is_active' => true],
                ['name' => 'PINANG', 'tagline' => 'ACTIVE CLUB', 'logo' => null, 'url' => null, 'is_active' => true],
                ['name' => 'replab', 'tagline' => null, 'logo' => null, 'url' => null, 'is_active' => true],
                ['name' => 'NUSA', 'tagline' => 'HYDRATE', 'logo' => null, 'url' => null, 'is_active' => true],
            ];
        }

        $sponsors = array_values(array_filter(
            $sponsors,
            fn (array $sponsor): bool => filled($sponsor['name'] ?? null),
        ));

        if (! $onlyActive) {
            return $sponsors;
        }

        return array_values(array_filter(
            $sponsors,
            fn (array $sponsor): bool => $sponsor['is_active'] ?? true,
        ));
    }

    public function getWhatsappUrlAttribute(): ?string
    {
        if (! $this->whatsapp) {
            return null;
        }

        $number = preg_replace('/\D/', '', $this->whatsapp);
        if (str_starts_with($number, '0')) {
            $number = '62'.substr($number, 1);
        }

        return 'https://wa.me/'.$number;
    }

    public function getMapsUrlAttribute(): ?string
    {
        if ($this->latitude === null || $this->longitude === null) {
            return null;
        }

        return 'https://www.google.com/maps?q='.$this->latitude.','.$this->longitude;
    }
}
