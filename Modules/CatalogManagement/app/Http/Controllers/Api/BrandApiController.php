<?php

namespace Modules\CatalogManagement\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Modules\CatalogManagement\app\DTOs\BrandFilterDTO;
use Modules\CatalogManagement\app\Http\Resources\Api\BrandApiResource;
use Modules\CatalogManagement\app\Services\Api\BrandApiService;

class BrandApiController extends Controller
{
    use Res;

    public function __construct(
        protected BrandApiService $BrandService
    )
    {}

    public function index(Request $request)
    {
        $dto = BrandFilterDTO::fromRequest($request);

        if (!$dto->validate()) {
            return $this->sendRes(
                config('responses.validation')[app()->getLocale()],
                false,
                [],
                $dto->getErrors(),
                422
            );
        }

        $brands = $this->BrandService->getAllBrands($dto);

        return $this->sendRes(config('responses.success')[app()->getLocale()], true, BrandApiResource::collection($brands));
    }

    public function show(Request $request, $id)
    {
        $dto = BrandFilterDTO::fromRequest($request);

        if (!$dto->validate()) {
            return $this->sendRes(
                config('responses.validation')[app()->getLocale()],
                false,
                [],
                $dto->getErrors(),
                422
            );
        }

        $brand = $this->BrandService->find($dto, $id);

        return $this->sendRes(config('responses.success')[app()->getLocale()], true, BrandApiResource::make($brand));
    }
}
