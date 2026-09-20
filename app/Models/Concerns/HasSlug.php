<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @mixin Model
 */
trait HasSlug
{
    // Dipanggil otomatis oleh Laravel karena namanya boot + NamaTrait
    public static function bootHasSlug(): void
    {
        static::creating(function ($model) {
            if (! empty($model->slug)) {
                return;
            }

            $base = Str::slug($model->name);
            $slug = $base;
            $i = 2;

            while (static::where('slug', $slug)->exists()) {
                $slug = $base . '-' . $i++;
            }

            $model->slug = $slug;
        });
    }
}