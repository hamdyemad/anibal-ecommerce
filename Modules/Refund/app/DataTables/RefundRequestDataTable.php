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
        
        // Calculate total refunded items quantity
        $totalRefundedQuantity = $refund->items->sum('quantity');
        
        $html = '<div class="refund-info">';
        $html .= '<div class="mb-1"><strong>' . trans('refund::refund.fields.refund_number') . ':</strong> ' . e($refund->refund_number) . '</div>';
        $html .= '<div class="mb-1"><strong>' . trans('refund::refund.fields.order_number') . ':</strong> ' . e($orderNumber) . '</div>';
        $html .= '<div class="mb-1"><strong>' . trans('refund::refund.fields.customer') . ':</strong> ' . e($customerName) . '</div>';
        
        if (isAdmin()) {
            $html .= '<div class="mb-1"><strong>' . trans('refund::refund.fields.vendor') . ':</strong> ' . e($vendorName) . '</div>';
        }
        
        $html .= '<div class="mb-1"><strong>' . trans('refund::refund.fields.refunded_items') . ':</strong> <span class="badge badge-danger badge-round badge-lg"><i class="uil uil-redo"></i> ' . $totalRefundedQuantity . '</span></div>';
        $html .= '<div class="mb-1"><strong>' . trans('refund::refund.fields.total_refund_amount') . ':</strong> ' . number_format($refund->total_refund_amount, 2) . ' ' . $currency . '</div>';
        $html .= '<div><strong>' . trans('common.created_at') . ':</strong> ' . $refund->created_at . '</div>';
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
            'cancelled' => '<span class="badge badge-danger badge-round badge-lg"><i class="uil uil-ban"></i> ' . trans('refund::refund.statuses.cancelled') . '</span>',
        ];

        return $badges[$status] ?? $status;
    }

    /**
     * Build actions HTML with status change buttons
     */
    protected function buildActions($refund): string
    {
        $showUrl = route('admin.refunds.show', ['lang' => app()->getLocale(), 'countryCode' => strtolower(session('country_code', 'eg')), 'id' => $refund->id]);
        
        $html = '<div class="orderDatatable_actions d-flex flex-column gap-2 align-items-center">';
        
        // View button
        $html .= '<div class="d-inline-flex gap-1">';
        $html .= '<a href="' . $showUrl . '" class="view btn btn-sm btn-primary" title="' . trans('common.view') . '">';
        $html .= '<i class="uil uil-eye m-0"></i>';
        $html .= '</a>';
        $html .= $this->buildStatusChangeButtons($refund);
        $html .= '</div>';

        return $html;
    }
    
    /**
     * Build status change buttons based on current status
     * Note: The modals and JavaScript are handled by the refund-actions component in index.blade.php
     */
    protected function buildStatusChangeButtons($refund): string
    {
        if (!$refund->canChangeStatus()) {
            return '';
        }

        $html = '';
        $nextStatuses = $refund->getNextStatuses();
        
        // Status button configuration
        $statusConfig = [
            'approved' => ['color' => 'info', 'icon' => 'uil-check'],
            'cancelled' => ['color' => 'danger', 'icon' => 'uil-ban'],
            'in_progress' => ['color' => 'primary', 'icon' => 'uil-sync'],
            'picked_up' => ['color' => 'secondary', 'icon' => 'uil-package'],
            'refunded' => ['color' => 'success', 'icon' => 'uil-check-circle'],
        ];
        
        foreach ($nextStatuses as $nextStatus) {
            $config = $statusConfig[$nextStatus] ?? ['color' => 'secondary', 'icon' => 'uil-arrow-right'];
            $label = trans('refund::refund.statuses.' . $nextStatus);
            
            // Cancel button opens modal (handled by component)
            if ($nextStatus === 'cancelled') {
                $html .= '<button type="button" ';
                $html .= 'class="btn btn-sm btn-' . $config['color'] . '" ';
                $html .= 'data-bs-toggle="modal" ';
                $html .= 'data-bs-target="#cancelModal" ';
                $html .= 'data-refund-id="' . $refund->id . '" ';
                $html .= 'title="' . $label . '">';
                $html .= '<i class="uil ' . $config['icon'] . ' m-0"></i>';
                $html .= '</button>';
            } else {
                // Other status changes use the change-refund-status class (handled by component JS)
                $html .= '<button type="button" ';
                $html .= 'class="btn btn-sm btn-' . $config['color'] . ' change-refund-status" ';
                $html .= 'data-refund-id="' . $refund->id . '" ';
                $html .= 'data-status="' . $nextStatus . '" ';
                $html .= 'title="' . $label . '">';
                $html .= '<i class="uil ' . $config['icon'] . ' m-0"></i>';
                $html .= '</button>';
            }
        }
        
        return $html;
    }
}
