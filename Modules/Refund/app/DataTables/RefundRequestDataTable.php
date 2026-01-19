<?php

namespace Modules\Refund\app\DataTables;

use Illuminate\Http\Request;
use Modules\Refund\app\Models\RefundRequest;

class RefundRequestDataTable
{
    /**
     * Handle the datatable request
     */
    public function handle(Request $request): array
    {
        try {
            // Get pagination parameters from DataTables
            $perPage = isset($request->length) && $request->length > 0 ? (int)$request->length : 10;
            $start = isset($request->start) && $request->start >= 0 ? (int)$request->start : 0;
            $page = $perPage > 0 ? floor($start / $perPage) + 1 : 1;

            // Build filters array
            $filters = $this->buildFilters($request);
            
            // Build query with scopeFilters
            $query = RefundRequest::with(['order', 'customer', 'vendor', 'items.orderProduct'])
                ->filter($filters);
            
            // Get total and filtered counts
            $totalRecords = RefundRequest::count();
            $filteredRecords = $query->count();
            
            // Get paginated results
            $refundRequests = $query->latest()->paginate($perPage, ['*'], 'page', $page);
            
            // Format data for DataTables
            $data = $this->formatData($refundRequests, $start);
            
            return [
                'draw' => intval($request->draw ?? 1),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data,
            ];
            
        } catch (\Exception $e) {
            \Log::error('RefundRequestDataTable Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'draw' => intval($request->draw ?? 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Build filters array from request
     */
    protected function buildFilters(Request $request): array
    {
        $filters = [
            'status' => $request->status_filter ?? null,
            'search' => $request->input('search.value') ?? $request->search ?? null,
            'date_from' => $request->created_date_from ?? null,
            'date_to' => $request->created_date_to ?? null,
        ];
        
        // Add vendor filter if not admin
        if (!isAdmin()) {
            $user = auth()->user();
            if ($user) {
                $vendor = $user->vendorByUser ?? $user->vendorById ?? null;
                if ($vendor) {
                    $filters['current_vendor_id'] = $vendor->id;
                }
            }
        }

        return $filters;
    }

    /**
     * Format data for DataTables
     */
    protected function formatData($refundRequests, int $start): array
    {
        $data = [];
        $index = $start + 1;
        
        foreach ($refundRequests as $refund) {
            $data[] = [
                'index' => $index++,
                'refund_info' => $this->buildRefundInfo($refund),
                'status' => $this->buildStatusBadge($refund->status),
                'actions' => $this->buildActions($refund),
            ];
        }

        return $data;
    }

    /**
     * Build refund info HTML
     */
    protected function buildRefundInfo($refund): string
    {
        $customerName = optional($refund->customer)->full_name ?? '-';
        $vendorName = optional($refund->vendor)->name ?? '-';
        $orderNumber = optional($refund->order)->order_number ?? '-';
        $currency = trans('common.currency') !== 'common.currency' ? trans('common.currency') : 'EGP';
        
        $html = '<div class="refund-info">';
        $html .= '<div class="mb-1"><strong>' . trans('refund::refund.fields.refund_number') . ':</strong> ' . e($refund->refund_number) . '</div>';
        $html .= '<div class="mb-1"><strong>' . trans('refund::refund.fields.order_number') . ':</strong> ' . e($orderNumber) . '</div>';
        $html .= '<div class="mb-1"><strong>' . trans('refund::refund.fields.customer') . ':</strong> ' . e($customerName) . '</div>';
        
        if (isAdmin()) {
            $html .= '<div class="mb-1"><strong>' . trans('refund::refund.fields.vendor') . ':</strong> ' . e($vendorName) . '</div>';
        }
        
        $html .= '<div class="mb-1"><strong>' . trans('refund::refund.fields.total_refund_amount') . ':</strong> ' . number_format($refund->total_refund_amount, 2) . ' ' . $currency . '</div>';
        $html .= '<div><strong>' . trans('common.created_at') . ':</strong> ' . $refund->created_at->format('Y-m-d H:i') . '</div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Build status badge HTML
     */
    protected function buildStatusBadge(string $status): string
    {
        $badges = [
            'pending' => '<span class="badge badge-warning badge-round badge-lg"><i class="uil uil-clock"></i> ' . trans('refund::refund.statuses.pending') . '</span>',
            'approved' => '<span class="badge badge-info badge-round badge-lg"><i class="uil uil-check"></i> ' . trans('refund::refund.statuses.approved') . '</span>',
            'in_progress' => '<span class="badge badge-primary badge-round badge-lg"><i class="uil uil-sync"></i> ' . trans('refund::refund.statuses.in_progress') . '</span>',
            'picked_up' => '<span class="badge badge-secondary badge-round badge-lg"><i class="uil uil-package"></i> ' . trans('refund::refund.statuses.picked_up') . '</span>',
            'refunded' => '<span class="badge badge-success badge-round badge-lg"><i class="uil uil-check-circle"></i> ' . trans('refund::refund.statuses.refunded') . '</span>',
            'rejected' => '<span class="badge badge-danger badge-round badge-lg"><i class="uil uil-times-circle"></i> ' . trans('refund::refund.statuses.rejected') . '</span>',
        ];

        return $badges[$status] ?? $status;
    }

    /**
     * Build actions HTML
     */
    protected function buildActions($refund): string
    {
        $showUrl = route('admin.refunds.show', $refund->id);
        
        $html = '<div class="orderDatatable_actions d-inline-flex gap-1 justify-content-center">';
        $html .= '<a href="' . $showUrl . '" class="view btn btn-sm btn-primary" title="' . trans('common.view') . '">';
        $html .= '<i class="uil uil-eye m-0"></i>';
        $html .= '</a>';
        $html .= '</div>';

        return $html;
    }
}
