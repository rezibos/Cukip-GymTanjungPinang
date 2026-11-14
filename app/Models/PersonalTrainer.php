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

    public function slots(): HasMany
    {
        return $this->hasMany(TrainerSlot::class);
    }
}
