<?php

namespace App\Models;

use App\Enums\FacilityCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category', 'description', 'image_path'];

    protected function casts(): array
    {
        return [
            'category' => FacilityCategory::class,
        ];
    }
}
