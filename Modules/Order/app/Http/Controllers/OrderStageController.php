<?php

namespace Modules\Order\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Order\app\Http\Requests\OrderStageRequest;
use Modules\Order\app\Services\OrderStageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Language;

class OrderStageController extends Controller
{
    protected $orderStageService;

    public function __construct(OrderStageService $orderStageService)
    {
        $this->orderStageService = $orderStageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $languages = Language::all();
        return view('order::order-stages.index', compact('languages'));
    }

    /**
     * DataTable endpoint for order stages
     */
    public function datatable(Request $request)
    {
        $filters = [
            'search' => $request->input('search.value'),
            'active' => $request->input('active'),
            'created_date_from' => $request->input('created_from'),
            'created_date_to' => $request->input('created_until'),
        ];

        $query = $this->orderStageService->getOrderStagesQuery($filters);

        $totalRecords = $query->count();
        $filteredRecords = $query->count();

        // Pagination
        $perPage = $request->input('length', 10);
        $page = $request->input('start', 0) / $perPage;

        $orderStages = $query->skip($page * $perPage)->take($perPage)->get();

        $data = $orderStages->map(function ($orderStage) {
            $translations = [];
            foreach ($orderStage->translations as $translation) {
                $lang = \App\Models\Language::find($translation->lang_id);
                if ($lang) {
                    if (!isset($translations[$lang->code])) {
                        $translations[$lang->code] = [];
                    }
                    $translations[$lang->code][$translation->lang_key] = $translation->lang_value;
                }
            }

            return [
                'id' => $orderStage->id,
                'slug' => $orderStage->slug,
                'color' => $orderStage->color,
                'active' => $orderStage->active,
                'is_system' => $orderStage->is_system,
                'sort_order' => $orderStage->sort_order,
                'created_at' => $orderStage->created_at->format('Y-m-d H:i:s'),
                'translations' => $translations,
            ];
        });

        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = Language::all();
        return view('order::order-stages.form', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderStageRequest $request)
    {
        try {
            $this->orderStageService->createOrderStage($request->all());
            return redirect()->route('admin.order-stages.index')
                ->with('success', __('order::order_stage.order_stage_created'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('order::order_stage.error_creating_order_stage') . ': ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $orderStage = $this->orderStageService->getOrderStageById($id);
        $languages = Language::all();
        return view('order::order-stages.show', compact('orderStage', 'languages'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $orderStage = $this->orderStageService->getOrderStageById($id);
        $languages = Language::all();
        return view('order::order-stages.form', compact('orderStage', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderStageRequest $request, $id)
    {
        try {
            $this->orderStageService->updateOrderStage($id, $request->all());
            return redirect()->route('admin.order-stages.index')
                ->with('success', __('order::order_stage.order_stage_updated'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('order::order_stage.error_updating_order_stage') . ': ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $this->orderStageService->deleteOrderStage($id);
            return response()->json([
                'success' => true,
                'message' => __('order::order_stage.order_stage_deleted')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('order::order_stage.error_deleting_order_stage') . ': ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Toggle order stage status
     */
    public function toggleStatus(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => __('order::order_stage.invalid_status')
                ], 422);
            }

            $orderStage = $this->orderStageService->getOrderStageById($id);
            $newStatus = $request->input('status');

            // Check if status is actually changing
            if ($orderStage->active == $newStatus) {
                return response()->json([
                    'success' => false,
                    'message' => __('order::order_stage.status_already_set')
                ], 422);
            }

            $this->orderStageService->toggleOrderStageStatus($id);

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('order::order_stage.status_changed_successfully'),
                    'new_status' => $newStatus,
                    'redirect' => route('admin.order-stages.index')
                ]);
            }

            return redirect()->route('admin.order-stages.index')
                ->with('success', __('order::order_stage.status_changed_successfully'));

        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('order::order_stage.error_changing_status') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->with('error', __('order::order_stage.error_changing_status') . ': ' . $e->getMessage());
        }
    }
}
