<?php

namespace Modules\Report\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Modules\Report\app\Http\Requests\ReportFilterRequest;
use Modules\Report\app\Services\ReportService;

class ReportApiController extends Controller
{
    use Res;

    public function __construct(
        protected ReportService $reportService
    ) {}

    /**
     * Get registered users report
     */
    public function registeredUsers(ReportFilterRequest $request)
    {
        try {
            $data = $this->reportService->getRegisteredUsersReport($request->validated());

            return $this->sendRes(
                config('responses.success')[app()->getLocale()],
                true,
                $data,
                [],
                200
            );
        } catch (\Exception $e) {
            return $this->sendRes(
                config('responses.error')[app()->getLocale()],
                false,
                [],
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Get area users report
     */
    public function areaUsers(ReportFilterRequest $request)
    {
        try {
            $data = $this->reportService->getAreaUsersReport($request->validated());

            return $this->sendRes(
                config('responses.success')[app()->getLocale()],
                true,
                $data,
                [],
                200
            );
        } catch (\Exception $e) {
            return $this->sendRes(
                config('responses.error')[app()->getLocale()],
                false,
                [],
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Get orders report
     */
    public function orders(ReportFilterRequest $request)
    {
        try {
            $data = $this->reportService->getOrdersReport($request->validated());

            return $this->sendRes(
                config('responses.success')[app()->getLocale()],
                true,
                $data,
                [],
                200
            );
        } catch (\Exception $e) {
            return $this->sendRes(
                config('responses.error')[app()->getLocale()],
                false,
                [],
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Get products report
     */
    public function products(ReportFilterRequest $request)
    {
        try {
            $data = $this->reportService->getProductsReport($request->validated());

            return $this->sendRes(
                config('responses.success')[app()->getLocale()],
                true,
                $data,
                [],
                200
            );
        } catch (\Exception $e) {
            return $this->sendRes(
                config('responses.error')[app()->getLocale()],
                false,
                [],
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Get points report
     */
    public function points(ReportFilterRequest $request)
    {
        try {
            $data = $this->reportService->getPointsReport($request->validated());

            return $this->sendRes(
                config('responses.success')[app()->getLocale()],
                true,
                $data,
                [],
                200
            );
        } catch (\Exception $e) {
            return $this->sendRes(
                config('responses.error')[app()->getLocale()],
                false,
                [],
                ['error' => $e->getMessage()],
                500
            );
        }
    }
}
