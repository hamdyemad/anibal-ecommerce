<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\ModelCountry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Modules\AreaSettings\app\Models\Country;

class CountryGlobalObserver
{
    /**
     * Models to exclude from country tracking
     */
    private array $excludedModels = [
        'App\Models\PersonalAccessToken',
        'App\Models\PasswordReset',
        'App\Models\FailedJob',
        'App\Models\Translation',
        'App\Models\Attachment',
        'App\Models\ModelCountry',
    ];

    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
        $this->storeCountry($model);
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        $this->storeCountry($model);
    }

    /**
     * Store country_id for the model from session country_code
     */
    private function storeCountry(Model $model): void
    {
        Log::info('CountryGlobalObserver firing for model: ' . get_class($model) . ' ID: ' . $model->id);

        try {
            // Skip excluded models
            foreach ($this->excludedModels as $excluded) {
                if ($model instanceof $excluded || get_class($model) === $excluded) {
                    Log::info('Model excluded from country tracking: ' . get_class($model));
                    return;
                }
            }

            $countryCode = session('country_code');
            Log::info('Country code from session: ' . ($countryCode ?? 'NULL'));

            if (!$countryCode) {
                return; // No country code in session
            }

            $countryId = Country::where('code', $countryCode)->value('id');
            if (!$countryId) {
                Log::warning('Invalid country code: ' . $countryCode);
                return; // Invalid country code
            }

            $exists = ModelCountry::where('countryable_id', $model->id)
                ->where('countryable_type', get_class($model))
                ->exists();

            if (!$exists) {
                ModelCountry::create([
                    'countryable_id' => $model->id,
                    'countryable_type' => get_class($model),
                    'country_id' => $countryId,
                ]);
                Log::info('Country association created for model: ' . get_class($model) . ' ID: ' . $model->id);
            } else {
                Log::info('Country association already exists for model: ' . get_class($model) . ' ID: ' . $model->id);
            }

        } catch (\Exception $e) {
            Log::error('Failed to store country for model: ' . $e->getMessage());
        }
    }
}
