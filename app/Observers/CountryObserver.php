<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Modules\AreaSettings\app\Models\Country;

class CountryObserver
{
    /**
     * Models to exclude from country_id storage
     */
    private array $excludedModels = [
        'App\Models\PersonalAccessToken',
        'App\Models\PasswordReset',
        'App\Models\FailedJob',
    ];


    public function saving(Model $model): void
    {
        $this->storeCountryId($model);
    }

    /**
     * Store country_id from session country_code for models that need it
     */
    public function storeCountryId(Model $model): void
    {
        // Skip if model is excluded
        if ($this->isExcluded($model)) {
            return;
        }

        // Skip if model doesn't have country_id column
        if (!Schema::hasColumn($model->getTable(), 'country_id')) {
            return;
        }

        try {
            // Get country code from session
            $countryCode = session('country_code');

            if (!$countryCode) {
                return;
            }

            // Find country by code
            $country = Country::where('code', strtoupper($countryCode))->first();

            if ($country) {
                $model->country_id = $country->id;
            }
        } catch (\Exception $e) {
            // Log::warning("Could not store country_id: " . $e->getMessage());
        }
    }

    /**
     * Check if model is excluded from country_id storage
     */
    private function isExcluded(Model $model): bool
    {
        foreach ($this->excludedModels as $excludedModel) {
            if ($model instanceof $excludedModel || get_class($model) === $excludedModel) {
                return true;
            }
        }
        return false;
    }
}
