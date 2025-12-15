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
use Modules\SystemSetting\app\Http\Resources\Api\FeatureResource;
use Modules\SystemSetting\app\Http\Resources\Api\SliderResource;
use Modules\SystemSetting\app\Models\Ad;
use Modules\SystemSetting\app\Models\Feature;
use Modules\SystemSetting\app\Models\Slider;
use Modules\SystemSetting\app\Services\Api\AdApiService;
use Yajra\DataTables\Facades\DataTables;

class SliderApiController extends Controller
{
    use Res;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sliders = Slider::orderBy('sort_order', 'asc')->get();
        return $this->sendRes(__('main.success'), true, SliderResource::collection($sliders));
    }

}
