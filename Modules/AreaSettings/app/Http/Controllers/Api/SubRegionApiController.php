<?php

namespace Modules\AreaSettings\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Modules\AreaSettings\app\Resources\SubRegionResource;
use Modules\AreaSettings\app\Services\Api\SubRegionApiService;

class SubRegionApiController extends Controller
{
    use Res;
    public function __construct(
        protected SubRegionApiService $RegionService,
    ){}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $regions = $this->RegionService->getAll($request->all());

        return $this->sendRes(config('responses.success')[app()->getLocale()], true, SubRegionResource::collection($regions));
    }

    public function getSubRegionsByRegions(Request $request, $id)
    {
        $regions = $this->RegionService->getSubRegionsByRegions($id, $request->all());

        return $this->sendRes(config('responses.success')[app()->getLocale()], true, SubRegionResource::collection($regions));
    }

}
