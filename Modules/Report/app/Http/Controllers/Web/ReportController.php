<?php

namespace Modules\Report\app\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Modules\Report\app\Services\ReportService;
use Modules\Report\app\Http\Requests\ReportFilterRequest;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    /**
     * Show reports dashboard
     */
    public function index()
    {
        return view('report::index');
    }

    /**
     * Show registered users report
     */
    public function registeredUsers()
    {
        return view('report::registered-users');
    }

    /**
     * Get registered users data for AJAX/DataTables
     */
    public function getRegisteredUsersData(ReportFilterRequest $request)
    {
        Log::info('getRegisteredUsersData called', ['all_inputs' => $request->all()]);
        
        try {
            $validated = $request->validated();
            Log::info('Report Filter Request', $validated);
            $reportData = $this->reportService->getRegisteredUsersReport($validated);
            Log::info('Report Data Retrieved', ['count' => count($reportData['data'] ?? []), 'total' => $reportData['total'] ?? 0]);
            
            // Transform data to match DataTables serverSide format
            return response()->json([
                'status' => true,
                'data' => [
                    'data' => $reportData['data'],
                    'total' => $reportData['total'],
                    'count' => $reportData['count'],
                    'per_page' => $reportData['per_page'],
                    'current_page' => $reportData['current_page'],
                    'last_page' => $reportData['last_page'],
                    'from' => $reportData['from'],
                    'to' => $reportData['to'],
                    'statistics' => $reportData['statistics'] ?? [],
                    'gender_distribution' => $reportData['gender_distribution'] ?? [],
                    'registration_trend' => $reportData['registration_trend'] ?? [],
                ],
            ], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            Log::error('Report Error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show area users report
     */
    public function areaUsers()
    {
        return view('report::area-users');
    }

    /**
     * Get area users data for AJAX/DataTables
     */
    public function getAreaUsersData(ReportFilterRequest $request)
    {
        try {
            $reportData = $this->reportService->getAreaUsersReport($request->validated());
            return response()->json([
                'status' => true,
                'data' => [
                    'data' => $reportData['data'],
                    'total' => $reportData['total'],
                    'count' => $reportData['count'],
                    'per_page' => $reportData['per_page'],
                    'current_page' => $reportData['current_page'],
                    'last_page' => $reportData['last_page'],
                    'from' => $reportData['from'],
                    'to' => $reportData['to'],
                ],
            ], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            Log::error('Report Error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show area users report
     */
    public function orders()
    {
        return view('report::orders');
    }

    /**
     * Get orders data for AJAX/DataTables
     */
    public function getOrdersData(ReportFilterRequest $request)
    {
        try {
            $reportData = $this->reportService->getOrdersReport($request->validated());
            return response()->json([
                'status' => true,
                'data' => [
                    'data' => $reportData['data'],
                    'total' => $reportData['total'],
                    'count' => $reportData['count'],
                    'per_page' => $reportData['per_page'],
                    'current_page' => $reportData['current_page'],
                    'last_page' => $reportData['last_page'],
                    'from' => $reportData['from'],
                    'to' => $reportData['to'],
                ],
            ], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            Log::error('Report Error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show products report
     */
    public function products()
    {
        return view('report::products');
    }

    /**
     * Get products data for AJAX/DataTables
     */
    public function getProductsData(ReportFilterRequest $request)
    {
        try {
            $reportData = $this->reportService->getProductsReport($request->validated());
            return response()->json([
                'status' => true,
                'data' => [
                    'data' => $reportData['data'],
                    'total' => $reportData['total'],
                    'count' => $reportData['count'],
                    'per_page' => $reportData['per_page'],
                    'current_page' => $reportData['current_page'],
                    'last_page' => $reportData['last_page'],
                    'from' => $reportData['from'],
                    'to' => $reportData['to'],
                ],
            ], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            Log::error('Report Error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show points report
     */
    public function points()
    {
        return view('report::points');
    }

    /**
     * Get points data for AJAX/DataTables
     */
    public function getPointsData(ReportFilterRequest $request)
    {
        try {
            $reportData = $this->reportService->getPointsReport($request->validated());
            return response()->json([
                'status' => true,
                'data' => [
                    'data' => $reportData['data'],
                    'total' => $reportData['total'],
                    'count' => $reportData['count'],
                    'per_page' => $reportData['per_page'],
                    'current_page' => $reportData['current_page'],
                    'last_page' => $reportData['last_page'],
                    'from' => $reportData['from'],
                    'to' => $reportData['to'],
                ],
            ], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            Log::error('Report Error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}


