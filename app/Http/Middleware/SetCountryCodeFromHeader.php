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
            // Validate that the country code exists in database
            $country = Country::where('code', strtoupper($countryCode))->first();

            if ($country) {
                // Store in session
                session(['country_code' => $country->code]);
            }
        } else {
            // If no header provided, use default or existing session value
            if (!session('country_code')) {
                $defaultCountry = Country::default()->first();
                if ($defaultCountry) {
                    session(['country_code' => $defaultCountry->code]);
                }
            }
        }

        return $next($request);
    }
}
