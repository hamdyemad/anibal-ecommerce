<?php

namespace App\Traits;

use App\Models\ModelCountry;

trait HasCountries
{
    /**
     * Get all countries for this model
     */
    public function modelCountries()
    {
        return $this->morphMany(ModelCountry::class, 'countryable');
    }

    /**
     * Get the primary country for this model
     */
    public function getCountryAttribute()
    {
        return $this->modelCountries()->first()?->country;
    }

    /**
     * Get all countries as a collection
     */
    public function getCountriesAttribute()
    {
        return $this->modelCountries()->with('country')->get()->pluck('country');
    }

    /**
     * Check if model has a specific country
     */
    public function hasCountry($countryId): bool
    {
        return $this->modelCountries()
            ->where('country_id', $countryId)
            ->exists();
    }

    /**
     * Attach a country to this model
     */
    public function attachCountry($countryId): void
    {
        if (!$this->hasCountry($countryId)) {
            $this->modelCountries()->create([
                'country_id' => $countryId,
            ]);
        }
    }

    /**
     * Detach a country from this model
     */
    public function detachCountry($countryId): void
    {
        $this->modelCountries()
            ->where('country_id', $countryId)
            ->delete();
    }

    /**
     * Sync countries for this model
     */
    public function syncCountries($countryIds): void
    {
        $this->modelCountries()->delete();

        foreach ((array) $countryIds as $countryId) {
            $this->attachCountry($countryId);
        }
    }
}
