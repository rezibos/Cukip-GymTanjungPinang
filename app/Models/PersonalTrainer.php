<?php

namespace App\Models;

use Database\Factories\PersonalTrainerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PersonalTrainer extends Model
{
    /** @use HasFactory<PersonalTrainerFactory> */
    use HasFactory;

    protected $fillable = ['name', 'specialization', 'description', 'experience_years', 'certifications', 'certification_documents', 'session_price', 'session_minutes', 'photo_path', 'is_active'];

    protected function casts(): array
    {
        return ['certifications' => 'array', 'certification_documents' => 'array', 'session_price' => 'integer', 'session_minutes' => 'integer', 'experience_years' => 'integer', 'is_active' => 'boolean'];
    }

    /**
     * @param  array<int, string|array{name?: string|null, document?: string|null}>  $certifications
     * @param  array<int, string|null>  $documents
     * @return array<int, array{name: string, document: string|null}>
     */
    public static function normalizeCertificationItems(array $certifications, array $documents = []): array
    {
        $itemCount = max(count($certifications), count($documents));

        if ($itemCount === 0) {
            return [];
        }

        return collect(range(0, $itemCount - 1))
            ->map(function (int $index) use ($certifications, $documents): array {
                $certification = $certifications[$index] ?? 'Sertifikat tambahan';

                if (is_array($certification)) {
                    return [
                        'name' => (string) ($certification['name'] ?? ''),
                        'document' => $certification['document'] ?? $documents[$index] ?? null,
                    ];
                }

                return [
                    'name' => $certification,
                    'document' => $documents[$index] ?? null,
                ];
            })
            ->filter(fn (array $certification): bool => filled($certification['name']) || filled($certification['document']))
            ->values()
            ->all();
    }

    /** @return array<int, array{name: string, document: string|null}> */
    public function certificationItems(): array
    {
        return self::normalizeCertificationItems(
            $this->certifications ?? [],
            $this->certification_documents ?? [],
        );
    }

    public function slots(): HasMany
    {
        return $this->hasMany(TrainerSlot::class);
    }
}
