<?php

namespace Modules\AreaSettings\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Modules\AreaSettings\app\Resources\RegionResource;
use Modules\AreaSettings\app\Services\Api\RegionApiService;

class RegionApiController extends Controller
{
    use Res;
    public function __construct(
        protected RegionApiService $RegionService,
    ){}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Cache regions forever - will be cleared when updated
        $cacheKey = 'api_regions_' . md5(json_encode($request->all()) . app()->getLocale());
        
        $regions = \Cache::rememberForever($cacheKey, function() use ($request) {
            return $this->RegionService->getAll($request->all());
        });

        return $this->sendRes(config('responses.success')[app()->getLocale()], true, RegionResource::collection($regions)->additional($request->all()));
    }

    public function getRegionsByCity(Request $request, $id)
    {
        // Cache regions by city forever - will be cleared when updated
        $cacheKey = 'api_regions_city_' . $id . '_' . md5(json_encode($request->all()) . app()->getLocale());
        
        $regions = \Cache::rememberForever($cacheKey, function() use ($request, $id) {
            return $this->RegionService->getRegionsByCity($id, $request->all());
        });

        return $this->sendRes(config('responses.success')[app()->getLocale()], true, RegionResource::collection($regions));
    }

}
