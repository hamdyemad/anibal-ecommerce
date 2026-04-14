<?php

namespace Modules\CatalogManagement\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Modules\CatalogManagement\app\DTOs\ProductFilterDTO;
use Modules\CatalogManagement\app\Http\Resources\Api\VariantListResource;
use Modules\CatalogManagement\app\Services\Api\VariantApiService;

class VariantApiController extends Controller
{
    use Res;

    public function __construct(
        protected VariantApiService $variantService
    ) {}

    /**
     * Get all variants with filters and pagination
     * Similar to /products but returns variants instead
     * 
     * GET /api/v1/variants
     */
    public function index(Request $request)
    {
        $dto = ProductFilterDTO::fromRequest($request);

        if (!$dto->validate()) {
            return $this->sendRes(
                config('responses.validation')[app()->getLocale()],
                false,
                [],
                $dto->getErrors(),
                422
            );
        }

        $variants = $this->variantService->getAllVariants($dto);

        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            VariantListResource::collection($variants)
        );
    }
}
