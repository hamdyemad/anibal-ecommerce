<?php

namespace Modules\SystemSetting\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\SystemSetting\app\Actions\AdAction;
use Modules\SystemSetting\app\Http\Requests\AdRequest;
use Modules\SystemSetting\app\Models\Ad;
use Modules\SystemSetting\app\Models\AdPosition;
use Modules\SystemSetting\app\Services\AdService;
use Yajra\DataTables\Facades\DataTables;

class AdController extends Controller
{
    protected $adService;
    protected $adAction;

    public function __construct(AdService $adService, AdAction $adAction)
    {
        $this->adService = $adService;
        $this->adAction = $adAction;
        
        $this->middleware('can:ads.index')->only(['index', 'datatable']);
        $this->middleware('can:ads.create')->only(['create', 'store']);
        $this->middleware('can:ads.show')->only(['show']);
        $this->middleware('can:ads.edit')->only(['edit', 'update']);
        $this->middleware('can:ads.delete')->only(['destroy']);
        $this->middleware('can:ads.toggle-status')->only(['toggleStatus']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $positions = AdPosition::pluck('position', 'id')->toArray();
        return view('systemsetting::ads.index', compact('positions'));
    }

    /**
     * Get datatable data
     */
    public function datatable(Request $request)
    {
        $query = Ad::with('translations', 'attachments', 'adPosition');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('translations', function($query) use ($search) {
                    $query->where('lang_value', 'like', "%{$search}%");
                })
                ->orWhere('link', 'like', "%{$search}%");
            });
        }

        if ($request->filled('position')) {
            $query->where('ad_position_id', $request->position);
        }

        if ($request->filled('type')) {
            $query->whereJsonContains('type', $request->type);
        }

        if ($request->filled('active') && $request->active !== '') {
            $query->where('active', $request->active);
        }

        if ($request->filled('created_date_from')) {
            $query->whereDate('created_at', '>=', $request->created_date_from);
        }

        if ($request->filled('created_date_to')) {
            $query->whereDate('created_at', '<=', $request->created_date_to);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('ad_information', function ($ad) {
                $html = '<div class="d-flex align-items-center gap-3">';
                
                // Image
                if ($ad->image) {
                    $html .= '<div class="flex-shrink-0">';
                    $html .= '<img src="' . asset('storage/' . $ad->image) . '" alt="Ad Image" style="width: 80px; height: 60px; border-radius: 4px; object-fit: cover;">';
                    $html .= '</div>';
                } else {
                    $html .= '<div class="flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 80px; height: 60px; background: #f5f5f5; border-radius: 4px;">';
                    $html .= '<i class="uil uil-image-slash text-muted" style="font-size: 24px;"></i>';
                    $html .= '</div>';
                }
                
                // Title, Position and Type
                $html .= '<div class="flex-grow-1">';
                
                // Title and Subtitle
                $title = $ad->title ? truncateString($ad->title, 30) : '-';
                $html .= '<div class="mb-2"><strong>' . $title . '</strong></div>';
                if ($ad->subtitle) {
                    $subtitle = truncateString($ad->subtitle, 40);
                    $html .= '<div class="text-muted small mb-2">' . $subtitle . '</div>';
                }
                
                // Position
                $positionName = $ad->adPosition ? $ad->adPosition->position : '-';
                $html .= '<div class="mb-1">';
                $html .= '<span class="badge badge-round badge-lg badge-info">' . $positionName . '</span>';
                $html .= '</div>';
                
                // Type badges
                if ($ad->type && is_array($ad->type)) {
                    $html .= '<div>';
                    foreach ($ad->type as $type) {
                        $color = $type == 'mobile' ? 'primary' : 'secondary';
                        $html .= '<span class="badge badge-round badge-sm badge-' . $color . ' me-1">' . __('systemsetting::ads.' . $type) . '</span>';
                    }
                    $html .= '</div>';
                }
                
                $html .= '</div>';
                $html .= '</div>';
                
                return $html;
            })
            ->addColumn('status_badge', function ($ad) {
                if ($ad->active) {
                    return '<span class="badge badge-round badge-lg badge-success">' . __('systemsetting::ads.active') . '</span>';
                }
                return '<span class="badge badge-round badge-lg badge-danger">' . __('systemsetting::ads.inactive') . '</span>';
            })
            ->addColumn('created_date', function ($ad) {
                return $ad->created_at ? $ad->created_at : '-';
            })
            ->addColumn('action', function ($ad) {
                $actions = '<div class="orderDatatable_actions d-inline-flex gap-1 justify-content-center">';
                if (auth()->user()->can('ads.show')) {
                    $actions .= '<a href="' . route('admin.system-settings.ads.show', $ad->id) . '" class="view btn btn-primary table_action_father" title="' . __('systemsetting::ads.view') . '">';
                    $actions .= '<i class="uil uil-eye table_action_icon"></i>';
                    $actions .= '</a>';
                }
                if (auth()->user()->can('ads.edit')) {
                    $actions .= '<a href="' . route('admin.system-settings.ads.edit', $ad->id) . '" class="edit btn btn-warning table_action_father" title="' . __('systemsetting::ads.edit') . '">';
                    $actions .= '<i class="uil uil-edit table_action_icon"></i>';
                    $actions .= '</a>';
                }
                if (auth()->user()->can('ads.delete')) {
                    $actions .= '<a href="javascript:void(0);" class="remove btn btn-danger table_action_father" data-bs-toggle="modal" data-bs-target="#modal-delete-ad" data-item-id="' . $ad->id . '" data-item-name="' . ($ad->title ?? 'Ad') . '" title="' . __('systemsetting::ads.delete') . '">';
                    $actions .= '<i class="uil uil-trash-alt table_action_icon"></i>';
                    $actions .= '</a>';
                }
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['ad_information', 'status_badge', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = Language::all();
        $positions = AdPosition::all();
        return view('systemsetting::ads.form', compact('languages', 'positions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdRequest $request)
    {
        try {
            $data = $request->validated();
            $data['image'] = $request->file('image');

            $ad = $this->adService->createAd($data);

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('systemsetting::ads.created_successfully'),
                    'redirect' => route('admin.system-settings.ads.index'),
                    'data' => $ad
                ]);
            }

            return redirect()
                ->route('admin.system-settings.ads.index')
                ->with('success', __('systemsetting::ads.created_successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('systemsetting::ads.error_creating') . ': ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('systemsetting::ads.error_creating') . ': ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($lang, $code, $id)
    {
        $ad = $this->adService->getAdById($id);
        $languages = Language::all();
        return view('systemsetting::ads.view', compact('ad', 'languages'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($lang, $code,$id)
    {
        $ad = $this->adService->getAdById($id);
        $languages = Language::all();
        $positions = AdPosition::all();
        return view('systemsetting::ads.form', compact('ad', 'languages', 'positions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdRequest $request,$lang, $code, $id)
    {
        try {
            $data = $request->validated();
            $data['image'] = $request->file('image');

            $ad = $this->adService->updateAd($id, $data);

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('systemsetting::ads.updated_successfully'),
                    'redirect' => route('admin.system-settings.ads.index'),
                    'data' => $ad
                ]);
            }

            return redirect()
                ->route('admin.system-settings.ads.index')
                ->with('success', __('systemsetting::ads.updated_successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('systemsetting::ads.error_updating') . ': ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('systemsetting::ads.error_updating') . ': ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($lang, $code, $id)
    {
        try {
            $this->adService->deleteAd($id);

            return response()->json([
                'success' => true,
                'message' => __('systemsetting::ads.deleted_successfully'),
                'redirect' => route('admin.system-settings.ads.index')
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => __('systemsetting::ads.not_found')
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('systemsetting::ads.error_deleting') . ': ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Toggle ad status
     */
    public function toggleStatus($lang, $code, $id, Request $request)
    {
        try {
            $status = $request->input('status');
            $this->adService->toggleStatus($id, $status);

            return response()->json([
                'success' => true,
                'message' => __('systemsetting::ads.status_updated_successfully'),
                'status' => $status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('systemsetting::ads.error_updating_status') . ': ' . $e->getMessage()
            ], 500);
        }
    }
}
