<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class TranslatableCast implements CastsAttributes
{
    /**
     * Cast the given value (from database to model).
     * Store the translation key as-is
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if (!$value) {
            return null;
        }

        // If value looks like a translation key (contains dots), translate it
        if (str_contains($value, '.') && trans($value) !== $value) {
            return trans($value);
        }

        // Otherwise return as-is (for backward compatibility with already translated values)
        return $value;
    }

    /**
     * Prepare the given value for storage (from model to database).
     * Store the translation key without translating
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        return $value;
    }
}
