<?php

namespace Modules\SystemSetting\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\SystemSetting\app\Actions\SliderAction;
use Modules\SystemSetting\app\Http\Requests\SliderRequest;
use Modules\SystemSetting\app\Models\Slider;
use Modules\SystemSetting\app\Services\SliderService;
use Yajra\DataTables\Facades\DataTables;

class SliderController extends Controller
{
    protected $sliderService;
    protected $sliderAction;

    public function __construct(SliderService $sliderService, SliderAction $sliderAction)
    {
        $this->sliderService = $sliderService;
        $this->sliderAction = $sliderAction;
    }

    public function index()
    {
        return view('systemsetting::sliders.index');
    }

    public function datatable(Request $request)
    {
        $query = Slider::with('attachments');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('slider_link', 'like', "%{$search}%");
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
            ->addColumn('image_preview', function ($slider) {
                $image = ($slider->attachments->first()) ? asset('storage/' . $slider->attachments->first()->path) : null;
                if ($image) {
                    return '<div class="userDatatable-content"><img src="' . $image . '" alt="Slider" style="max-width: 100px; max-height: 60px; border-radius: 4px;"></div>';
                }
                return '<span class="text-muted">-</span>';
            })
            ->addColumn('link_display', function ($slider) {
                $html = '<div class="userDatatable-content d-flex justify-content-center">';
                if ($slider->slider_link) {
                    $html .= '<a href="' . $slider->slider_link . '" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3">';
                    $html .= '<i class="uil uil-external-link-alt me-1"></i>' . __('systemsetting::sliders.visit_link');
                    $html .= '</a>';
                } else {
                    $html .= '<span class="text-muted">-</span>';
                }
                $html .= '</div>';
                return $html;
            })
            ->addColumn('status_badge', function ($slider) {
                if ($slider->active) {
                    return '<span class="badge badge-round badge-lg badge-success">' . __('systemsetting::sliders.active') . '</span>';
                }
                return '<span class="badge badge-round badge-lg badge-danger">' . __('systemsetting::sliders.inactive') . '</span>';
            })
            ->addColumn('created_date', function ($slider) {
                return $slider->created_at ? $slider->created_at : '-';
            })
            ->addColumn('action', function ($slider) {
                $actions = '<div class="orderDatatable_actions d-inline-flex gap-1 justify-content-center">';
                $actions .= '<a href="' . route('admin.system-settings.sliders.show', $slider->id) . '" class="view btn btn-primary table_action_father" title="' . __('systemsetting::sliders.view') . '">';
                $actions .= '<i class="uil uil-eye table_action_icon"></i>';
                $actions .= '</a>';
                $actions .= '<a href="' . route('admin.system-settings.sliders.edit', $slider->id) . '" class="edit btn btn-warning table_action_father" title="' . __('systemsetting::sliders.edit') . '">';
                $actions .= '<i class="uil uil-edit table_action_icon"></i>';
                $actions .= '</a>';
                $actions .= '<a href="javascript:void(0);" class="remove btn btn-danger table_action_father" data-bs-toggle="modal" data-bs-target="#modal-delete-slider" data-item-id="' . $slider->id . '" data-item-name="Slider" title="' . __('systemsetting::sliders.delete') . '">';
                $actions .= '<i class="uil uil-trash-alt table_action_icon"></i>';
                $actions .= '</a>';
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['image_preview', 'link_display', 'status_badge', 'action'])
            ->make(true);
    }

    public function create()
    {
        return view('systemsetting::sliders.form');
    }

    public function store(SliderRequest $request)
    {
        try {
            $data = $request->validated();
            $slider = $this->sliderService->createSlider($data);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('systemsetting::sliders.created_successfully'),
                    'redirect' => route('admin.system-settings.sliders.index'),
                    'data' => $slider
                ]);
            }

            return redirect()
                ->route('admin.system-settings.sliders.index')
                ->with('success', __('systemsetting::sliders.created_successfully'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('systemsetting::sliders.error_creating') . ': ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('systemsetting::sliders.error_creating') . ': ' . $e->getMessage());
        }
    }

    public function show($lang, $code, $id)
    {
        $slider = $this->sliderService->getSliderById($id);
        return view('systemsetting::sliders.view', compact('slider'));
    }

    public function edit($lang, $code, $id)
    {
        $slider = $this->sliderService->getSliderById($id);
        return view('systemsetting::sliders.form', compact('slider'));
    }

    public function update(SliderRequest $request, $lang, $code, $id)
    {
        try {
            $data = $request->validated();
            $slider = $this->sliderService->updateSlider($id, $data);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('systemsetting::sliders.updated_successfully'),
                    'redirect' => route('admin.system-settings.sliders.index'),
                    'data' => $slider
                ]);
            }

            return redirect()
                ->route('admin.system-settings.sliders.index')
                ->with('success', __('systemsetting::sliders.updated_successfully'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('systemsetting::sliders.error_updating') . ': ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('systemsetting::sliders.error_updating') . ': ' . $e->getMessage());
        }
    }

    public function destroy($lang, $code, $id)
    {
        try {
            $this->sliderService->deleteSlider($id);

            return response()->json([
                'success' => true,
                'message' => __('systemsetting::sliders.deleted_successfully'),
                'redirect' => route('admin.system-settings.sliders.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('systemsetting::sliders.error_deleting') . ': ' . $e->getMessage()
            ], 500);
        }
    }
}
