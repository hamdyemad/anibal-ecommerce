<?php

namespace Modules\SystemSetting\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Traits\Res;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\SystemSetting\app\Actions\AdAction;
use Modules\SystemSetting\app\Http\Requests\AdRequest;
use Modules\SystemSetting\app\Http\Resources\Api\AdsResource;
use Modules\SystemSetting\app\Models\Ad;
use Modules\SystemSetting\app\Services\Api\AdApiService;
use Yajra\DataTables\Facades\DataTables;

class AdApiController extends Controller
{
    use Res;
    protected $adService;

    public function __construct(AdApiService $adService)
    {
        $this->adService = $adService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $ads = $this->adService->getAll($request->all());
        return $this->sendRes(__('main.success'), true, AdsResource::collection($ads));
    }

    /**
     * Get available ad positions with dimensions
     */
    public function positions()
    {
        $positions = Ad::getPositionsWithDimensions();
        
        $result = collect($positions)->map(function ($data, $key) {
            return [
                'key' => $key,
                'name' => $data['name'],
                'width' => $data['width'],
                'height' => $data['height'],
            ];
        })->values();

        return $this->sendRes(__('main.success'), true, $result);
    }
}
