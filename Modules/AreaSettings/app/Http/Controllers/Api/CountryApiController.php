<?php

namespace Modules\AreaSettings\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Modules\AreaSettings\app\Resources\CountryResource;
use Modules\AreaSettings\app\Services\Api\CountryApiService;

class CountryApiController extends Controller
{
    use Res;

    public function __construct(
        protected CountryApiService $countryService,
    ){}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Cache countries forever - will be cleared when updated
        $cacheKey = 'api_countries_' . md5(json_encode($request->all()) . app()->getLocale());
        
        $countries = \Cache::rememberForever($cacheKey, function() use ($request) {
            return $this->countryService->getAll($request->all());
        });

        return $this->sendRes(config('responses.success')[app()->getLocale()], true, CountryResource::collection($countries));
    }

    public function show(Request $request, $id)
    {
        // Cache single country forever - will be cleared when updated
        $cacheKey = 'api_country_' . $id . '_' . md5(json_encode($request->all()) . app()->getLocale());
        
        $country = \Cache::rememberForever($cacheKey, function() use ($request, $id) {
            return $this->countryService->getCountryById($id, $request->all());
        });

        return $this->sendRes(config('responses.success')[app()->getLocale()], true, CountryResource::make($country));
    }

}
