<?php

namespace Modules\Order\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Order\app\Models\RequestQuotation;
use Yajra\DataTables\Facades\DataTables;

class RequestQuotationController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:request-quotations.index')->only(['index', 'archived', 'datatable']);
        $this->middleware('can:request-quotations.archive')->only(['archive']);
    }

    public function index()
    {
        return view('order::request-quotations.index', ['isArchived' => false]);
    }

    public function archived()
    {
        return view('order::request-quotations.index', ['isArchived' => true]);
    }

    public function datatable(Request $request)
    {
        try {
            $isArchived = $request->boolean('is_archived');
            
            $query = RequestQuotation::with('order')->latest();
            
            if ($isArchived) {
                $query->archived();
            } else {
                $query->notArchived();
            }

            // Apply filters
            if ($request->filled('search_text')) {
                $search = $request->input('search_text');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%");
                });
            } elseif ($request->filled('search.value')) {
                $search = $request->input('search.value');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            if ($request->filled('status') && $request->input('status') !== 'all') {
                $query->where('status', $request->input('status'));
            }

            if ($request->filled('created_date_from')) {
                $query->whereDate('created_at', '>=', $request->input('created_date_from'));
            }

            if ($request->filled('created_date_to')) {
                $query->whereDate('created_at', '<=', $request->input('created_date_to'));
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('name_info', function ($quotation) {
                    return '<div>
                        <strong>' . e($quotation->name) . '</strong>
                        <br><small class="text-muted">' . e($quotation->email) . '</small>
                    </div>';
                })
                ->addColumn('contact_info', function ($quotation) {
                    return '<div>
                        <i class="uil uil-phone me-1"></i>' . e($quotation->phone) . '
                        <br><small class="text-muted"><i class="uil uil-map-marker me-1"></i>' . truncateString($quotation->address, 30) . '</small>
                    </div>';
                })
                ->addColumn('status_badge', function ($quotation) {
                    $badges = [
                        'not_created' => '<span class="badge badge-warning badge-round">' . __('order::request-quotation.status_not_created') . '</span>',
                        'created' => '<span class="badge badge-success badge-round">' . __('order::request-quotation.status_created') . '</span>',
                        'archived' => '<span class="badge badge-secondary badge-round">' . __('order::request-quotation.status_archived') . '</span>',
                    ];
                    return $badges[$quotation->status] ?? '-';
                })
                ->addColumn('order_number', function ($quotation) {
                    if ($quotation->order) {
                        $orderUrl = route('admin.orders.show', ['order' => $quotation->order_id]);
                        return '<a href="' . $orderUrl . '" class="text-primary fw-500">' . e($quotation->order->order_number) . '</a>';
                    }
                    return '-';
                })
                ->addColumn('created_date', function ($quotation) {
                    return $quotation->created_at ? $quotation->created_at : '-';
                })
                ->addColumn('actions', function ($quotation) use ($isArchived) {
                    $html = '<div class="d-flex gap-2 justify-content-center">';
                    
                    // Create Order button (+ icon) - only for non-archived and not already created
                    if (!$isArchived && $quotation->status === 'not_created') {
                        $orderUrl = route('admin.orders.create') . '?' . http_build_query([
                            'quotation_id' => $quotation->id,
                            'name' => $quotation->name,
                            'email' => $quotation->email,
                            'phone' => $quotation->phone,
                            'address' => $quotation->address,
                            'notes' => $quotation->notes,
                        ]);
                        $html .= '<a href="' . $orderUrl . '" class="btn btn-sm btn-success" title="' . __('order::request-quotation.create_order') . '">
                            <i class="uil uil-plus m-0"></i>
                        </a>';
                    }
                    
                    // Download file button
                    if ($quotation->file) {
                        $html .= '<a href="' . asset('storage/' . $quotation->file) . '" class="btn btn-sm btn-info" download title="' . __('order::request-quotation.download_file') . '">
                            <i class="uil uil-download-alt m-0"></i>
                        </a>';
                    }
                    
                    // Archive button - only for non-archived and not already created (with permission check)
                    if (!$isArchived && $quotation->status === 'not_created') {
                        if(auth()->user()->can('request-quotations.archive')) {
                            $html .= '<button type="button" class="btn btn-sm btn-warning btn-archive" data-id="' . $quotation->id . '" title="' . __('order::request-quotation.archive') . '">
                                <i class="uil uil-archive m-0"></i>
                            </button>';
                        }
                    }

                    // View details button
                    $quotationData = [
                        'name' => $quotation->name,
                        'email' => $quotation->email,
                        'phone' => $quotation->phone,
                        'address' => $quotation->address,
                        'notes' => $quotation->notes,
                        'status' => $quotation->status,
                        'created_at' => $quotation->created_at ? $quotation->created_at : '-',
                    ];
                    $html .= '<button type="button" class="btn btn-sm btn-primary btn-view" data-quotation=\'' . json_encode($quotationData) . '\' title="' . __('common.view') . '">
                        <i class="uil uil-eye m-0"></i>
                    </button>';
                    
                    $html .= '</div>';
                    return $html;
                })
                ->rawColumns(['name_info', 'contact_info', 'status_badge', 'order_number', 'actions'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    public function archive($lang, $countryCode, $id)
    {
        $quotation = RequestQuotation::findOrFail($id);
        $quotation->update(['status' => RequestQuotation::STATUS_ARCHIVED]);

        return response()->json([
            'status' => true,
            'message' => __('order::request-quotation.archived_successfully'),
        ]);
    }
}
