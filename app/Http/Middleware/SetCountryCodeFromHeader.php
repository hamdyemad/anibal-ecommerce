<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\AreaSettings\app\Models\Country;

class SetCountryCodeFromHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Get country_code from header (case-insensitive)
        $countryCode = $request->header('X-Country-Code');

        if ($countryCode) {
            // Try cache first (cache forever, invalidate on country update)
            $cacheKey = 'country_by_code_' . strtolower($countryCode);
            $country = cache()->remember($cacheKey, 3600, function () use ($countryCode) {
                return Country::where('code', strtolower($countryCode))->first();
            });

            if ($country) {
                session(['country_code' => $country->code]);
                session(['country_id' => $country->id]);
            }
        } else {

            $country_id = $request->country_id;
            if($country_id) {
                // Cache by ID as well
                $cacheKey = 'country_by_id_' . $country_id;
                $country = cache()->remember($cacheKey, 3600, function () use ($country_id) {
                    return Country::where('id', $country_id)->first();
                });
                if ($country) {
                    session(['country_code' => $country->code]);
                    session(['country_id' => $country->id]);
                }
            }
            // If no header provided, use default or existing session value
            if (!session('country_code')) {
                // Cache the default country query (most common on first request)
                $defaultCountry = cache()->remember('default_country', 3600, function () {
                    return Country::default()->first();
                });
                if ($defaultCountry) {
                    session(['country_code' => $defaultCountry->code]);
                    session(['country_id' => $defaultCountry->id]);
                }
            }
        }

        return $next($request);
    }
}
