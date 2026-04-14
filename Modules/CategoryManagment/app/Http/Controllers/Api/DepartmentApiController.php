<?php

namespace Modules\CategoryManagment\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Modules\CategoryManagment\app\DTOs\DepartmentFilterDTO;
use Modules\CategoryManagment\app\Http\Resources\Api\DepartmentApiResource;
use Modules\CategoryManagment\app\Services\Api\DepartmentApiService;

class DepartmentApiController extends Controller
{
    use Res;
    public function __construct(protected DepartmentApiService $DepartmentService)
    {}

    public function index(Request $request)
    {
        $dto = DepartmentFilterDTO::fromRequest($request);

        if (!$dto->validate()) {
            return $this->sendRes(
                config('responses.validation')[app()->getLocale()],
                false,
                [],
                $dto->getErrors(),
                422
            );
        }

        // Cache departments forever - will be cleared automatically when data changes
        $cacheKey = 'departments_' . md5(json_encode($dto->toArray()) . app()->getLocale());
        
        $departments = cache()->rememberForever($cacheKey, function () use ($dto) {
            return $this->DepartmentService->getAllDepartments($dto);
        });

        $departments = DepartmentApiResource::collection($departments)->additional($request->all());
        return $this->sendRes(config('responses.success')[app()->getLocale()], true, $departments);
    }

    public function show(Request $request, $id)
    {
        $dto = DepartmentFilterDTO::fromRequest($request);

        if (!$dto->validate()) {
            return $this->sendRes(
                config('responses.validation')[app()->getLocale()],
                false,
                [],
                $dto->getErrors(),
                422
            );
        }

        $department = $this->DepartmentService->find($dto, $id);

        return $this->sendRes(config('responses.success')[app()->getLocale()], true, DepartmentApiResource::make($department));
    }
}
