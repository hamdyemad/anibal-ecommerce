<?php

namespace Modules\SystemSetting\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\SystemSetting\app\Actions\FaqAction;
use Modules\SystemSetting\app\Http\Requests\FaqRequest;
use Modules\SystemSetting\app\Models\Faq;
use Modules\SystemSetting\app\Services\FaqService;
use Yajra\DataTables\Facades\DataTables;

class FaqController extends Controller
{
    protected $faqService;
    protected $faqAction;

    public function __construct(FaqService $faqService, FaqAction $faqAction)
    {
        $this->faqService = $faqService;
        $this->faqAction = $faqAction;
    }

    public function index()
    {
        return view('systemsetting::faqs.index');
    }

    public function datatable(Request $request)
    {
        $query = Faq::with('translations')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('translations', function($query) use ($search) {
                    $query->where('lang_value', 'like', "%{$search}%");
                });
            });
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
            ->addColumn('question_answer', function ($faq) {
                $html = '<div class="userDatatable-content">';
                $html .= '<div class="mb-2"><strong>' . ($faq->question ?? '-') . '</strong></div>';
                if ($faq->answer) {
                    $html .= '<div class="text-muted small">' . \Str::limit(strip_tags($faq->answer), 100) . '</div>';
                }
                $html .= '</div>';
                return $html;
            })
            ->addColumn('status_badge', function ($faq) {
                if ($faq->active) {
                    return '<span class="badge badge-round badge-lg badge-success">' . __('systemsetting::faqs.active') . '</span>';
                }
                return '<span class="badge badge-round badge-lg badge-danger">' . __('systemsetting::faqs.inactive') . '</span>';
            })
            ->addColumn('created_date', function ($faq) {
                return $faq->created_at ? $faq->created_at : '-';
            })
            ->addColumn('action', function ($faq) {
                $actions = '<div class="orderDatatable_actions d-inline-flex gap-1 justify-content-center">';
                // $actions .= '<a href="' . route('admin.system-settings.faqs.show', $faq->id) . '" class="view btn btn-primary table_action_father" title="' . __('systemsetting::faqs.view') . '">';
                // $actions .= '<i class="uil uil-eye table_action_icon"></i>';
                // $actions .= '</a>';
                $actions .= '<a href="' . route('admin.system-settings.faqs.edit', $faq->id) . '" class="edit btn btn-warning table_action_father" title="' . __('systemsetting::faqs.edit') . '">';
                $actions .= '<i class="uil uil-edit table_action_icon"></i>';
                $actions .= '</a>';
                $actions .= '<a href="javascript:void(0);" class="remove btn btn-danger table_action_father" data-bs-toggle="modal" data-bs-target="#modal-delete-faq" data-item-id="' . $faq->id . '" data-item-name="' . ($faq->question ?? 'FAQ') . '" title="' . __('systemsetting::faqs.delete') . '">';
                $actions .= '<i class="uil uil-trash-alt table_action_icon"></i>';
                $actions .= '</a>';
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['question_answer', 'status_badge', 'action'])
            ->make(true);
    }

    public function create()
    {
        $languages = Language::all();
        return view('systemsetting::faqs.form', compact('languages'));
    }

    public function store(FaqRequest $request)
    {
        try {
            $data = $request->validated();
            $faq = $this->faqService->createFaq($data);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('systemsetting::faqs.created_successfully'),
                    'redirect' => route('admin.system-settings.faqs.index'),
                    'data' => $faq
                ]);
            }

            return redirect()
                ->route('admin.system-settings.faqs.index')
                ->with('success', __('systemsetting::faqs.created_successfully'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('systemsetting::faqs.error_creating') . ': ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('systemsetting::faqs.error_creating') . ': ' . $e->getMessage());
        }
    }

    public function show($lang, $code, $id)
    {
        $faq = $this->faqService->getFaqById($id);
        $languages = Language::all();
        return view('systemsetting::faqs.view', compact('faq', 'languages'));
    }

    public function edit($lang, $code, $id)
    {
        $faq = $this->faqService->getFaqById($id);
        $languages = Language::all();
        return view('systemsetting::faqs.form', compact('faq', 'languages'));
    }

    public function update(FaqRequest $request, $lang, $code, $id)
    {
        try {
            $data = $request->validated();
            $faq = $this->faqService->updateFaq($id, $data);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('systemsetting::faqs.updated_successfully'),
                    'redirect' => route('admin.system-settings.faqs.index'),
                    'data' => $faq
                ]);
            }

            return redirect()
                ->route('admin.system-settings.faqs.index')
                ->with('success', __('systemsetting::faqs.updated_successfully'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('systemsetting::faqs.error_updating') . ': ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('systemsetting::faqs.error_updating') . ': ' . $e->getMessage());
        }
    }

    public function destroy($lang, $code, $id)
    {
        try {
            $this->faqService->deleteFaq($id);

            return response()->json([
                'success' => true,
                'message' => __('systemsetting::faqs.deleted_successfully'),
                'redirect' => route('admin.system-settings.faqs.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('systemsetting::faqs.error_deleting') . ': ' . $e->getMessage()
            ], 500);
        }
    }
}
