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
        $countries = $this->countryService->getAll($request->all());

        return $this->sendRes(config('responses.success')[app()->getLocale()], true, CountryResource::collection($countries));
    }

    public function show(Request $request, $id)
    {
        $country = $this->countryService->getCountryById($request->all(), $id);

        return $this->sendRes(config('responses.success')[app()->getLocale()], true, CountryResource::make($country));
    }

}
