<?php

namespace App\Models\Traits;

use Modules\AreaSettings\app\Models\Country;

trait AutoStoreCountryId
{
    public static function bootAutoStoreCountryId()
    {
        static::creating(function ($model) {
            // Skip if country_id is already set
            if (array_key_exists('country_id', $model->getAttributes()) && $model->country_id) {
                return;
            }
            
            $country_id = null;
            
            // 1. Try from session (web requests)
            $countryCode = session('country_code');
            if ($countryCode) {
                $country_id = Country::where('code', $countryCode)->value('id');
            }
            
            // 2. Try from request header (API requests)
            if (!$country_id && request()->hasHeader('X-Country-Code')) {
                $headerCode = request()->header('X-Country-Code');
                $country_id = Country::where('code', strtolower($headerCode))->value('id');
            }
            
            // 3. Try from request header X-Country-Id (API requests)
            if (!$country_id && request()->hasHeader('X-Country-Id')) {
                $headerId = request()->header('X-Country-Id');
                if (Country::where('id', $headerId)->exists()) {
                    $country_id = $headerId;
                }
            }
            
            // 4. Try from request body
            if (!$country_id && request()->has('country_id')) {
                $requestId = request()->input('country_id');
                if (Country::where('id', $requestId)->exists()) {
                    $country_id = $requestId;
                }
            }
            
            // 5. Fallback to default country
            if (!$country_id) {
                $country_id = Country::default()->value('id');
            }
            
            if ($country_id) {
                $model->country_id = $country_id;
            }
        });
    }
}
