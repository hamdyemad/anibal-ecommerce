<?php

namespace Modules\AreaSettings\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Modules\AreaSettings\app\Resources\CityResource;
use Modules\AreaSettings\app\Services\Api\CityApiService;

class CityApiController extends Controller
{
    use Res;
    public function __construct(
        protected CityApiService $cityService,
    ){}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $cities = $this->cityService->getAll($request->all());

        return $this->sendRes(config('responses.success')[app()->getLocale()], true, CityResource::collection($cities));
    }

    public function getCitiesByCountry(Request $request, $id)
    {
        $cities = $this->cityService->getCitiesByCountry($id, $request->all());

        return $this->sendRes(config('responses.success')[app()->getLocale()], true, CityResource::collection($cities));
    }

}
