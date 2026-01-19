<?php

namespace App\Helpers;

use Modules\SystemSetting\app\Models\PointsSetting;

class PointsHelper
{
    protected static $countryCache = [];
    protected static $pointsSettingCache = [];

    /**
     * Calculate points based on price and currency points settings
     * 
     * @param float $price The price to calculate points for
     * @return int The calculated points
     */
    public static function calculatePoints(float $price): int
    {
        if ($price <= 0) {
            return 0;
        }

        // Get currency from x-country-code header
        $countryCode = request()->header('x-country-code');
        
        if (!$countryCode) {
            return 0;
        }

        if (isset(self::$countryCache[$countryCode])) {
            $country = self::$countryCache[$countryCode];
        } else {
            // Get country and its currency
            $country = \Modules\AreaSettings\app\Models\Country::where('code', strtoupper($countryCode))->first();
            self::$countryCache[$countryCode] = $country;
        }

        $currencyId = $country?->currency_id;
        
        if (!$currencyId) {
            return 0;
        }

        return self::calculatePointsByCurrency($price, $currencyId);
    }

    /**
     * Calculate points based on price and currency ID
     * 
     * @param float $price The price to calculate points for
     * @param int $currencyId The currency ID
     * @return int The calculated points
     */
    public static function calculatePointsByCurrency(float $price, int $currencyId): int
    {
        if ($price <= 0 || !$currencyId) {
            return 0;
        }

        if (isset(self::$pointsSettingCache[$currencyId])) {
            $pointsSetting = self::$pointsSettingCache[$currencyId];
        } else {
            // Get points setting for this currency
            $pointsSetting = PointsSetting::where('currency_id', $currencyId)
                ->where('is_active', true)
                ->first();
            self::$pointsSettingCache[$currencyId] = $pointsSetting;
        }

        if (!$pointsSetting || $pointsSetting->points_value <= 0) {
            return 0;
        }

        // points_value = points per 1 currency unit
        return (int) floor($price * $pointsSetting->points_value);
    }
}
