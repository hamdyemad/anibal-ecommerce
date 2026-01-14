@props([
    'order' => null,
    'orderStages' => [],
    'isVendorUser' => false,
    'currentVendorId' => null,
    'vendorStage' => null,
    'showViewButton' => true,
    'context' => 'list' // 'list' or 'show'
])

@php
    // Get vendor stages info
    $vendorStages = $order ? ($order->vendorStages ?? collect()) : collect();
    $hasVendorStages = $vendorStages->count() > 0;
    
    // For vendor user
    $vendorStageType = null;
    $vendorStageId = null;
    $isVendorFinalStage = false;
    
    if ($isVendorUser && $currentVendorId && $order) {
        $currentVendorStage = $vendorStages->firstWhere('vendor_id', $currentVendorId);
        if ($currentVendorStage && $currentVendorStage->stage) {
            $vendorStageType = $currentVendorStage->stage->type;
            $vendorStageId = $currentVendorStage->stage_id;
            $isVendorFinalStage = in_array($vendorStageType, ['deliver', 'cancel', 'refund']);
        }
    }
    
    // For admin - check all vendor stages
    $allNew = $hasVendorStages && $vendorStages->every(fn($vs) => $vs->stage?->type === 'new');
    $allInProgress = $hasVendorStages && $vendorStages->every(fn($vs) => $vs->stage?->type === 'in_progress');
    $hasInProgress = $hasVendorStages && $vendorStages->contains(fn($vs) => $vs->stage?->type === 'in_progress');
    $hasNonFinal = $hasVendorStages && $vendorStages->contains(fn($vs) => !in_array($vs->stage?->type, ['deliver', 'cancel', 'refund']));
    $allFinal = $hasVendorStages && $vendorStages->every(fn($vs) => in_array($vs->stage?->type, ['deliver', 'cancel', 'refund']));
    
    // For admin - find the "lowest" (earliest in workflow) stage among all vendors
    $adminCurrentStageType = null;
    $adminCurrentStageId = null;
    if ($hasVendorStages) {
        $stageSteps = ['new' => 1, 'in_progress' => 2, 'deliver' => 3, 'cancel' => 3, 'refund' => 4];
        $lowestStep = 999;
        foreach ($vendorStages as $vs) {
            $stageType = $vs->stage?->type;
            $stageId = $vs->stage_id;
            if ($stageType && isset($stageSteps[$stageType])) {
                $step = $stageSteps[$stageType];
                if ($step < $lowestStep) {
                    $lowestStep = $step;
                    $adminCurrentStageType = $stageType;
                    $adminCurrentStageId = $stageId;
                }
            }
        }
    }
@endphp

@if($order)

<div class="d-flex gap-2 flex-wrap align-items-center {{ $context === 'show' ? 'justify-content-center' : '' }}">
    {{-- View Button (only in list context) --}}
    @if($showViewButton && $context === 'list')
        @can('orders.view')
            <a href="{{ route('admin.orders.show', $order->id) }}"
               class="btn btn-primary table_action_father"
               title="{{ trans('order::order.view_order') }}">
                <i class="uil uil-eye table_action_icon"></i>
            </a>
        @endcan
    @endif

    {{-- Admin Actions --}}
    @if(isAdmin())
        {{-- Edit Button (only if all vendors are in 'new' stage) --}}
        @if($allNew)
            @can('orders.update')
                <a href="{{ route('admin.orders.edit', $order->id) }}"
                   class="btn btn-info table_action_father"
                   title="{{ trans('order::order.edit_order') }}">
                    <i class="uil uil-edit table_action_icon"></i>
                </a>
            @endcan
        @endif
        
        {{-- Change Stage Button (for all vendors) --}}
        @if(!$allFinal && count($orderStages) > 0)
            <button type="button"
                    class="btn btn-warning table_action_father btn-admin-change-stage"
                    data-order-id="{{ $order->id }}"
                    data-current-stage-type="{{ $adminCurrentStageType }}"
                    data-current-stage-id="{{ $adminCurrentStageId }}"
                    title="{{ trans('order::order.change_stage_all_vendors') }}">
                <i class="uil uil-exchange table_action_icon"></i>
            </button>
        @endif

        {{-- Quick Actions --}}
        @if($hasVendorStages)
            {{-- Deliver button (only if all vendors are in 'in_progress') --}}
            @if($allInProgress)
                <button type="button"
                        class="btn btn-success table_action_father btn-admin-quick-stage"
                        data-order-id="{{ $order->id }}"
                        data-target-type="deliver"
                        title="{{ trans('order::order.mark_delivered') }}">
                    <i class="uil uil-check-circle table_action_icon"></i>
                </button>
            @endif

            {{-- Cancel button (if any vendor is not in final stage) --}}
            @if($hasNonFinal)
                <button type="button"
                        class="btn btn-danger table_action_father btn-admin-quick-stage"
                        data-order-id="{{ $order->id }}"
                        data-target-type="cancel"
                        title="{{ trans('order::order.cancel_order') }}">
                    <i class="uil uil-times-circle table_action_icon"></i>
                </button>
            @endif
        @endif

        {{-- Payment Button (only for online payments) --}}
        @if($order->payment_type === 'online')
            @can('orders.view')
                <a href="{{ route('admin.orders.payments', $order->id) }}"
                   class="btn btn-success table_action_father"
                   title="{{ trans('order::order.view_online_payment') }}">
                    <i class="uil uil-credit-card table_action_icon"></i>
                </a>
            @endcan
        @endif

        {{-- Allocate Button (when any vendor is in_progress) --}}
        @if($hasInProgress)
            <a href="{{ route('admin.order-fulfillments.allocate', $order->id) }}"
               class="btn btn-info table_action_father"
               title="{{ trans('order::order.allocate') }}">
                <i class="uil uil-box table_action_icon"></i>
            </a>
        @endif
    @endif

    {{-- Vendor Actions --}}
    @if($isVendorUser && !$isVendorFinalStage)
        {{-- Change Stage Button --}}
        @if(count($orderStages) > 0)
            <button type="button"
                    class="btn btn-warning table_action_father btn-change-vendor-stage"
                    data-order-id="{{ $order->id }}"
                    data-stage-id="{{ $vendorStageId }}"
                    data-stage-type="{{ $vendorStageType }}"
                    title="{{ trans('order::order.change_stage') }}">
                <i class="uil uil-exchange table_action_icon"></i>
            </button>
        @endif

        {{-- Deliver button (if vendor is in_progress) --}}
        @if($vendorStageType === 'in_progress')
            <button type="button"
                    class="btn btn-success table_action_father btn-vendor-quick-stage"
                    data-order-id="{{ $order->id }}"
                    data-target-type="deliver"
                    title="{{ trans('order::order.mark_delivered') }}">
                <i class="uil uil-check-circle table_action_icon"></i>
            </button>
        @endif

        {{-- Cancel button --}}
        @if(!in_array($vendorStageType, ['deliver', 'cancel', 'refund']))
            <button type="button"
                    class="btn btn-danger table_action_father btn-vendor-quick-stage"
                    data-order-id="{{ $order->id }}"
                    data-target-type="cancel"
                    title="{{ trans('order::order.cancel_order') }}">
                <i class="uil uil-times-circle table_action_icon"></i>
            </button>
        @endif

        {{-- Allocate Button (if vendor is in_progress) --}}
        @if($vendorStageType === 'in_progress')
            <a href="{{ route('admin.order-fulfillments.allocate', $order->id) }}"
               class="btn btn-info table_action_father"
               title="{{ trans('order::order.allocate') }}">
                <i class="uil uil-box table_action_icon"></i>
            </a>
        @endif
    @endif
</div>
@endif

{{-- Include modals and JS only once per page (outside any hidden containers) --}}
@once
@push('after-body')
    {{-- Admin Modals --}}
    @if(isAdmin() && !empty($orderStages))
        {{-- Admin Quick Stage Change Confirmation Modal --}}
        <div class="modal fade" id="adminQuickStageModal" tabindex="-1" aria-labelledby="adminQuickStageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="adminQuickStageModalLabel">
                            <i class="uil uil-exclamation-triangle me-2"></i>{{ trans('common.confirm_action') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="adminQuickStageOrderId">
                        <input type="hidden" id="adminQuickStageTargetType">
                        <div class="alert alert-info mb-3">
                            <i class="uil uil-info-circle me-2"></i>
                            {{ trans('order::order.admin_stage_change_warning') }}
                        </div>
                        <p id="adminQuickStageMessage" class="mb-0 fs-16"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                        <button type="button" class="btn btn-primary" id="confirmAdminQuickStageBtn">{{ __('common.confirm') }}</button>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Admin Change Stage Modal (for all vendors) --}}
        <div class="modal fade" id="adminChangeStageModal" tabindex="-1" aria-labelledby="adminChangeStageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="adminChangeStageModalLabel">
                            <i class="uil uil-exchange me-2"></i>{{ trans('order::order.change_stage_all_vendors') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="adminStageOrderId">
                        <input type="hidden" id="adminCurrentStageType">
                        <input type="hidden" id="adminCurrentStageId">
                        <div class="alert alert-info mb-3">
                            <i class="uil uil-info-circle me-2"></i>
                            {{ trans('order::order.admin_stage_change_warning') }}
                        </div>
                        <div class="form-group">
                            <label for="adminNewStage" class="form-label fw-bold">{{ trans('order::order.select_new_stage') }}</label>
                            <select id="adminNewStage" class="form-select" required>
                                <option value="">{{ trans('order::order.select_stage') }}</option>
                            </select>
                            <div id="adminStageWarning" class="alert alert-warning mt-2" style="display: none;"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                        <button type="button" class="btn btn-primary" id="confirmAdminStageBtn">{{ trans('order::order.update_stage') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    {{-- Vendor Modals --}}
    @if(!isAdmin() && !empty($orderStages))
        {{-- Vendor Quick Stage Change Confirmation Modal --}}
        <div class="modal fade" id="vendorQuickStageModal" tabindex="-1" aria-labelledby="vendorQuickStageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="vendorQuickStageModalLabel">
                            <i class="uil uil-exclamation-triangle me-2"></i>{{ trans('common.confirm_action') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="vendorQuickStageOrderId">
                        <input type="hidden" id="vendorQuickStageTargetType">
                        <p id="vendorQuickStageMessage" class="mb-0 fs-16"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                        <button type="button" class="btn btn-primary" id="confirmVendorQuickStageBtn">{{ __('common.confirm') }}</button>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Vendor Change Stage Modal --}}
        <div class="modal fade" id="vendorChangeStageModal" tabindex="-1" aria-labelledby="vendorChangeStageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="vendorChangeStageModalLabel">
                            <i class="uil uil-exchange me-2"></i>{{ trans('order::order.change_stage') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="vendorStageOrderId">
                        <input type="hidden" id="vendorCurrentStageId">
                        <input type="hidden" id="vendorCurrentStageType">
                        <div class="form-group">
                            <label for="vendorNewStage" class="form-label fw-bold">{{ trans('order::order.select_new_stage') }}</label>
                            <select id="vendorNewStage" class="form-select" required>
                                <option value="">{{ trans('order::order.select_stage') }}</option>
                            </select>
                            <div id="vendorStageWarning" class="alert alert-warning mt-2" style="display: none;"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                        <button type="button" class="btn btn-primary" id="confirmVendorStageBtn">{{ trans('order::order.update_stage') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endpush
    
    {{-- JavaScript for Order Actions --}}
    @push('scripts')
    <script>
    // Order Actions Helper - generates action buttons HTML for DataTable
    window.OrderActionsHelper = {
        routes: {
            show: "{{ route('admin.orders.show', ':id') }}",
            edit: "{{ route('admin.orders.edit', ':id') }}",
            payments: "{{ route('admin.orders.payments', ':id') }}",
            allocate: "{{ route('admin.order-fulfillments.allocate', ':id') }}"
        },
        translations: {
            viewOrder: "{{ trans('order::order.view_order') }}",
            changeStage: "{{ trans('order::order.change_stage') }}",
            changeStageAllVendors: "{{ trans('order::order.change_stage_all_vendors') }}",
            markInProgress: "{{ trans('order::order.mark_in_progress') }}",
            markDelivered: "{{ trans('order::order.mark_delivered') }}",
            cancelOrder: "{{ trans('order::order.cancel_order') }}",
            editOrder: "{{ trans('order::order.edit_order') }}",
            viewOnlinePayment: "{{ trans('order::order.view_online_payment') }}",
            allocate: "{{ trans('order::order.allocate') }}"
        },
        
        generate: function(row, options = {}) {
            const isVendorUser = options.isVendorUser || false;
            const canView = options.canView !== false;
            const canEdit = options.canEdit !== false;
            
            const showUrl = this.routes.show.replace(':id', row.id);
            const editUrl = this.routes.edit.replace(':id', row.id);
            const paymentsUrl = this.routes.payments.replace(':id', row.id);
            const allocateUrl = this.routes.allocate.replace(':id', row.id);
            
            // Check if ALL product stages are in final stages
            const finalStages = ['deliver', 'cancel', 'refund'];
            const isFinalStage = row.product_stages && row.product_stages.length > 0 
                ? row.product_stages.every(ps => ps.slug && finalStages.includes(ps.slug))
                : false;
            
            // For vendors: check if order belongs exclusively to them
            const canEditDelete = isVendorUser ? row.is_exclusive_to_vendor : true;
            
            // Check if order has online payment
            const hasOnlinePayment = row.payment_type === 'online';
            
            // Vendor stage info
            const vendorStage = row.vendor_stage || null;
            const vendorStageType = vendorStage ? vendorStage.type : null;
            const vendorStageId = vendorStage ? vendorStage.id : null;
            const isVendorFinalStage = vendorStageType && finalStages.includes(vendorStageType);

            let html = '<div class="orderDatatable_actions d-inline-flex gap-1 justify-content-center">';
            
            // View button - always shown
            if (canView) {
                html += `<a href="${showUrl}" class="view btn btn-primary table_action_father" title="${this.translations.viewOrder}"><i class="uil uil-eye table_action_icon"></i></a>`;
            }
            
            // For vendors: show stage change button
            if (isVendorUser && !isVendorFinalStage) {
                html += `<button type="button" class="btn btn-warning table_action_father btn-change-vendor-stage" data-order-id="${row.id}" data-stage-id="${vendorStageId || ''}" data-stage-type="${vendorStageType || ''}" title="${this.translations.changeStage}"><i class="uil uil-exchange table_action_icon"></i></button>`;
            }
            
            // For admin: show stage change button for all vendors
            if (!isVendorUser && !isFinalStage) {
                html += `<button type="button" class="btn btn-warning table_action_father btn-admin-change-stage" data-order-id="${row.id}" title="${this.translations.changeStageAllVendors}"><i class="uil uil-exchange table_action_icon"></i></button>`;
            }
            
            // Admin quick actions
            if (!isVendorUser && row.vendors_with_stages && row.vendors_with_stages.length > 0) {
                const allNew = row.vendors_with_stages.every(v => v.stage?.type === 'new');
                const allInProgress = row.vendors_with_stages.every(v => v.stage?.type === 'in_progress');
                const hasNonFinal = row.vendors_with_stages.some(v => !finalStages.includes(v.stage?.type));
                
                // Edit button for admin (only if all vendors are in 'new' stage)
                if (allNew && canEdit) {
                    html += `<a href="${editUrl}" class="btn btn-info table_action_father" title="${this.translations.editOrder}"><i class="uil uil-edit table_action_icon"></i></a>`;
                }
                
                // In Progress button
                if (allNew) {
                    html += `<button type="button" class="btn btn-secondary table_action_father btn-admin-quick-stage" data-order-id="${row.id}" data-target-type="in_progress" title="${this.translations.markInProgress}"><i class="uil uil-process table_action_icon"></i></button>`;
                }
                
                // Deliver button
                if (allInProgress) {
                    html += `<button type="button" class="btn btn-success table_action_father btn-admin-quick-stage" data-order-id="${row.id}" data-target-type="deliver" title="${this.translations.markDelivered}"><i class="uil uil-check-circle table_action_icon"></i></button>`;
                }
                
                // Cancel button
                if (hasNonFinal) {
                    html += `<button type="button" class="btn btn-danger table_action_father btn-admin-quick-stage" data-order-id="${row.id}" data-target-type="cancel" title="${this.translations.cancelOrder}"><i class="uil uil-times-circle table_action_icon"></i></button>`;
                }
            }
            
            // Vendor quick actions
            if (isVendorUser && vendorStage) {
                if (vendorStageType === 'new') {
                    html += `<button type="button" class="btn btn-secondary table_action_father btn-vendor-quick-stage" data-order-id="${row.id}" data-target-type="in_progress" title="${this.translations.markInProgress}"><i class="uil uil-process table_action_icon"></i></button>`;
                }
                
                if (vendorStageType === 'in_progress') {
                    html += `<button type="button" class="btn btn-success table_action_father btn-vendor-quick-stage" data-order-id="${row.id}" data-target-type="deliver" title="${this.translations.markDelivered}"><i class="uil uil-check-circle table_action_icon"></i></button>`;
                }
                
                if (!isVendorFinalStage) {
                    html += `<button type="button" class="btn btn-danger table_action_father btn-vendor-quick-stage" data-order-id="${row.id}" data-target-type="cancel" title="${this.translations.cancelOrder}"><i class="uil uil-times-circle table_action_icon"></i></button>`;
                }
            }
            
            // Payments button (only for online payments)
            if (hasOnlinePayment) {
                html += `<a href="${paymentsUrl}" class="btn btn-success table_action_father" title="${this.translations.viewOnlinePayment}"><i class="uil uil-credit-card table_action_icon"></i></a>`;
            }
            
            // Allocate button - for admin when any vendor is in_progress
            if (!isVendorUser && row.vendors_with_stages && row.vendors_with_stages.length > 0) {
                const hasInProgress = row.vendors_with_stages.some(v => v.stage?.type === 'in_progress');
                if (hasInProgress) {
                    html += `<a href="${allocateUrl}" class="btn btn-info table_action_father" title="${this.translations.allocate}"><i class="uil uil-box table_action_icon"></i></a>`;
                }
            }
            
            // Allocate button - for vendors only when in_progress and not fully allocated
            if (isVendorUser && vendorStage && vendorStageType === 'in_progress' && !row.is_fully_allocated) {
                html += `<a href="${allocateUrl}" class="btn btn-info table_action_father" title="${this.translations.allocate}"><i class="uil uil-box table_action_icon"></i></a>`;
            }
            
            html += '</div>';
            return html;
        }
    };
    
    $(document).ready(function() {
        // Order stages data
        const orderStages = @json($orderStages);
        const isVendorUser = {{ $isVendorUser ? 'true' : 'false' }};
        const currentVendorId = {{ $currentVendorId ?? 'null' }};
        
        // Stage step mapping
        const STAGE_STEPS = {
            'new': 1,
            'in_progress': 2,
            'deliver': 3,
            'cancel': 3,
            'refund': 4
        };
        
        const FINAL_STAGES = ['deliver', 'cancel', 'refund'];
        
        function getStageStep(type) {
            if (!type) return 0;
            return STAGE_STEPS[type] || 0;
        }
        
        function isFinalStage(type) {
            if (!type) return false;
            return FINAL_STAGES.includes(type);
        }
        
        function canTransitionTo(currentType, newType, currentId, newId) {
            if (currentId == newId) return false;
            if (isFinalStage(currentType)) return false;
            if (!currentType) {
                const newStep = getStageStep(newType);
                return newStep <= 2;
            }
            if (!newType) return true;
            
            const currentStep = getStageStep(currentType);
            const newStep = getStageStep(newType);
            
            if (newStep < currentStep) return false;
            if (newType === 'cancel') return true;
            if (newStep > currentStep + 1) return false;
            if (newType === 'refund' && currentType !== 'deliver') return false;
            
            return true;
        }

        @if(isAdmin())
        // ===== ADMIN HANDLERS =====
        
        // Admin quick stage change - show modal
        $(document).on('click', '.btn-admin-quick-stage', function() {
            const orderId = $(this).data('order-id');
            const targetType = $(this).data('target-type');
            const targetStage = orderStages.find(s => s.type === targetType);
            
            if (!targetStage) {
                toastr.error('{{ trans("order::order.stage_not_found") }}');
                return;
            }

            let message = '';
            switch(targetType) {
                case 'in_progress':
                    message = '{{ trans("order::order.confirm_in_progress_all_vendors") }}';
                    break;
                case 'deliver':
                    message = '{{ trans("order::order.confirm_deliver_all_vendors") }}';
                    break;
                case 'cancel':
                    message = '{{ trans("order::order.confirm_cancel_all_vendors") }}';
                    break;
                default:
                    message = '{{ trans("order::order.confirm_stage_change_all_vendors") }}';
            }

            $('#adminQuickStageOrderId').val(orderId);
            $('#adminQuickStageTargetType').val(targetType);
            $('#adminQuickStageMessage').text(message);
            
            const modalEl = document.getElementById('adminQuickStageModal');
            if (!modalEl) {
                console.error('Modal element not found: adminQuickStageModal');
                toastr.error('{{ trans("common.error") }}');
                return;
            }
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        });
        
        // Confirm admin quick stage change
        $('#confirmAdminQuickStageBtn').on('click', function() {
            const orderId = $('#adminQuickStageOrderId').val();
            const targetType = $('#adminQuickStageTargetType').val();
            const targetStage = orderStages.find(s => s.type === targetType);
            
            if (!targetStage) {
                toastr.error('{{ trans("common.error") }}');
                return;
            }
            
            const $btn = $(this);
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
            
            if (typeof LoadingOverlay !== 'undefined') {
                LoadingOverlay.show({ text: '{{ trans("order::order.updating_stage") }}' });
            }
            
            $.ajax({
                url: '{{ route("admin.orders.change-all-vendor-stages", ["orderId" => "__ORDER__"]) }}'.replace('__ORDER__', orderId),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    stage_id: targetStage.id
                },
                success: function(response) {
                    if (typeof LoadingOverlay !== 'undefined') LoadingOverlay.hide();
                    bootstrap.Modal.getInstance(document.getElementById('adminQuickStageModal')).hide();
                    toastr.success(response.message || '{{ trans("order::order.stage_updated_successfully") }}');
                    
                    if (targetType === 'in_progress') {
                        setTimeout(function() {
                            window.location.href = '{{ route("admin.order-fulfillments.allocate", ["orderId" => "__ORDER__"]) }}'.replace('__ORDER__', orderId);
                        }, 500);
                    } else {
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: function(xhr) {
                    if (typeof LoadingOverlay !== 'undefined') LoadingOverlay.hide();
                    let errorMessage = '{{ trans("order::order.error_updating_stage") }}';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    toastr.error(errorMessage);
                },
                complete: function() {
                    $btn.prop('disabled', false).html('{{ __("common.confirm") }}');
                }
            });
        });
        
        // Admin change stage modal button click
        $(document).on('click', '.btn-admin-change-stage', function() {
            const orderId = $(this).data('order-id');
            $('#adminStageOrderId').val(orderId);
            $('#adminNewStage').val('');
            $('#adminStageWarning').hide();
            
            // Clear and populate select
            const $select = $('#adminNewStage');
            $select.find('option:not(:first)').remove();
            
            // Try to get current stage info from button data attributes first (for show page)
            let currentStageType = $(this).data('current-stage-type') || null;
            let currentStageId = $(this).data('current-stage-id') || null;
            
            // If not available from button, try to get from DataTable (for list page)
            if (!currentStageType) {
                let dataTable = window.ordersTable || null;
                if (!dataTable && $.fn.DataTable && $.fn.DataTable.isDataTable('#ordersDataTable')) {
                    dataTable = $('#ordersDataTable').DataTable();
                }
                
                if (dataTable) {
                    const rowData = dataTable.rows().data().toArray().find(r => r.id == orderId);
                    if (rowData && rowData.vendors_with_stages && rowData.vendors_with_stages.length > 0) {
                        // Find the "lowest" stage among all vendors (earliest in workflow)
                        let lowestStep = 999;
                        rowData.vendors_with_stages.forEach(v => {
                            const stageType = v.stage?.type;
                            const stageId = v.stage?.id;
                            if (stageType) {
                                const step = getStageStep(stageType);
                                if (step < lowestStep) {
                                    lowestStep = step;
                                    currentStageType = stageType;
                                    currentStageId = stageId;
                                }
                            }
                        });
                    }
                }
            }
            
            // Store current stage info
            $('#adminCurrentStageType').val(currentStageType || '');
            $('#adminCurrentStageId').val(currentStageId || '');
            
            // Check if all vendors are in final stage
            if (currentStageType && isFinalStage(currentStageType)) {
                $('#adminStageWarning').text('{{ trans("order::order.cannot_change_final_stage") }}').show();
            } else {
                // Add only valid transition stages
                let hasOptions = false;
                orderStages.forEach(stage => {
                    if (canTransitionTo(currentStageType, stage.type, currentStageId, stage.id)) {
                        $select.append(`<option value="${stage.id}" data-type="${stage.type}">${stage.name}</option>`);
                        hasOptions = true;
                    }
                });
                
                if (!hasOptions) {
                    $('#adminStageWarning').text('{{ trans("order::order.no_available_stages") }}').show();
                }
            }
            
            const modalEl = document.getElementById('adminChangeStageModal');
            if (!modalEl) {
                console.error('Modal element not found: adminChangeStageModal');
                toastr.error('{{ trans("common.error") }}');
                return;
            }
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        });
        
        // Confirm admin stage change for all vendors
        $('#confirmAdminStageBtn').on('click', function() {
            const orderId = $('#adminStageOrderId').val();
            const stageId = $('#adminNewStage').val();
            
            if (!stageId) {
                toastr.warning('{{ trans("order::order.select_stage") }}');
                return;
            }
            
            const $btn = $(this);
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
            
            if (typeof LoadingOverlay !== 'undefined') {
                LoadingOverlay.show({ text: '{{ trans("order::order.updating_stage") }}' });
            }
            
            const selectedStage = orderStages.find(s => s.id == stageId);
            
            $.ajax({
                url: '{{ route("admin.orders.change-all-vendor-stages", ["orderId" => "__ORDER__"]) }}'.replace('__ORDER__', orderId),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    stage_id: stageId
                },
                success: function(response) {
                    if (typeof LoadingOverlay !== 'undefined') LoadingOverlay.hide();
                    bootstrap.Modal.getInstance(document.getElementById('adminChangeStageModal')).hide();
                    toastr.success(response.message || '{{ trans("order::order.stage_updated_successfully") }}');
                    
                    if (selectedStage && (selectedStage.type === 'in_progress' || selectedStage.slug === 'in-progress')) {
                        setTimeout(function() {
                            window.location.href = '{{ route("admin.order-fulfillments.allocate", ["orderId" => "__ORDER__"]) }}'.replace('__ORDER__', orderId);
                        }, 500);
                    } else {
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: function(xhr) {
                    if (typeof LoadingOverlay !== 'undefined') LoadingOverlay.hide();
                    let errorMessage = '{{ trans("order::order.error_updating_stage") }}';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    toastr.error(errorMessage);
                },
                complete: function() {
                    $btn.prop('disabled', false).html('{{ trans("order::order.update_stage") }}');
                }
            });
        });
        @endif
        
        @if(!isAdmin())
        // ===== VENDOR HANDLERS =====
        
        // Vendor quick stage change - show modal
        $(document).on('click', '.btn-vendor-quick-stage', function() {
            const orderId = $(this).data('order-id');
            const targetType = $(this).data('target-type');
            const targetStage = orderStages.find(s => s.type === targetType);
            
            if (!targetStage) {
                toastr.error('{{ trans("order::order.stage_not_found") }}');
                return;
            }

            let message = '';
            switch(targetType) {
                case 'in_progress':
                    message = '{{ trans("order::order.confirm_mark_in_progress") }}';
                    break;
                case 'deliver':
                    message = '{{ trans("order::order.confirm_mark_delivered") }}';
                    break;
                case 'cancel':
                    message = '{{ trans("order::order.confirm_cancel") }}';
                    break;
            }

            $('#vendorQuickStageOrderId').val(orderId);
            $('#vendorQuickStageTargetType').val(targetType);
            $('#vendorQuickStageMessage').text(message);
            
            const modalEl = document.getElementById('vendorQuickStageModal');
            if (!modalEl) {
                console.error('Modal element not found: vendorQuickStageModal');
                toastr.error('{{ trans("common.error") }}');
                return;
            }
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        });
        
        // Confirm vendor quick stage change
        $('#confirmVendorQuickStageBtn').on('click', function() {
            const orderId = $('#vendorQuickStageOrderId').val();
            const targetType = $('#vendorQuickStageTargetType').val();
            const targetStage = orderStages.find(s => s.type === targetType);
            
            if (!targetStage) {
                toastr.error('{{ trans("common.error") }}');
                return;
            }
            
            const $btn = $(this);
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
            
            if (typeof LoadingOverlay !== 'undefined') {
                LoadingOverlay.show({ text: '{{ trans("order::order.updating_stage") }}' });
            }
            
            $.ajax({
                url: '{{ route("admin.orders.vendor-stage.change", ["orderId" => "__ORDER__", "vendorId" => "__VENDOR__"]) }}'
                    .replace('__ORDER__', orderId)
                    .replace('__VENDOR__', currentVendorId),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    stage_id: targetStage.id
                },
                success: function(response) {
                    if (typeof LoadingOverlay !== 'undefined') LoadingOverlay.hide();
                    bootstrap.Modal.getInstance(document.getElementById('vendorQuickStageModal')).hide();
                    toastr.success(response.message || '{{ trans("order::order.stage_updated_successfully") }}');
                    
                    if (targetType === 'in_progress') {
                        setTimeout(function() {
                            window.location.href = '{{ route("admin.order-fulfillments.allocate", ["orderId" => "__ORDER__"]) }}'.replace('__ORDER__', orderId);
                        }, 500);
                    } else {
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: function(xhr) {
                    if (typeof LoadingOverlay !== 'undefined') LoadingOverlay.hide();
                    let errorMessage = '{{ trans("order::order.error_updating_stage") }}';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    toastr.error(errorMessage);
                },
                complete: function() {
                    $btn.prop('disabled', false).html('{{ __("common.confirm") }}');
                }
            });
        });
        
        // Vendor change stage modal button click
        $(document).on('click', '.btn-change-vendor-stage', function() {
            const orderId = $(this).data('order-id');
            const stageId = $(this).data('stage-id');
            const stageType = $(this).data('stage-type');
            
            $('#vendorStageOrderId').val(orderId);
            $('#vendorCurrentStageId').val(stageId);
            $('#vendorCurrentStageType').val(stageType);
            
            // Populate stage select
            const $select = $('#vendorNewStage');
            const $warning = $('#vendorStageWarning');
            $select.find('option:not(:first)').remove();
            $warning.hide();
            
            if (isFinalStage(stageType)) {
                $warning.text('{{ trans("order::order.cannot_change_final_stage") }}').show();
            } else {
                let hasOptions = false;
                orderStages.forEach(stage => {
                    if (canTransitionTo(stageType, stage.type, stageId, stage.id)) {
                        $select.append(`<option value="${stage.id}" data-type="${stage.type}">${stage.name}</option>`);
                        hasOptions = true;
                    }
                });
                
                if (!hasOptions) {
                    $warning.text('{{ trans("order::order.no_available_stages") }}').show();
                }
            }
            
            const modalEl = document.getElementById('vendorChangeStageModal');
            if (!modalEl) {
                console.error('Modal element not found: vendorChangeStageModal');
                toastr.error('{{ trans("common.error") }}');
                return;
            }
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        });
        
        // Confirm vendor stage change
        $('#confirmVendorStageBtn').on('click', function() {
            const orderId = $('#vendorStageOrderId').val();
            const stageId = $('#vendorNewStage').val();
            
            if (!stageId) {
                toastr.warning('{{ trans("order::order.select_stage") }}');
                return;
            }
            
            if (!currentVendorId) {
                toastr.error('{{ trans("common.error") }}');
                return;
            }
            
            const $btn = $(this);
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
            
            if (typeof LoadingOverlay !== 'undefined') {
                LoadingOverlay.show({ text: '{{ trans("order::order.updating_stage") }}' });
            }
            
            const selectedStage = orderStages.find(s => s.id == stageId);
            
            $.ajax({
                url: '{{ route("admin.orders.vendor-stage.change", ["orderId" => "__ORDER__", "vendorId" => "__VENDOR__"]) }}'
                    .replace('__ORDER__', orderId)
                    .replace('__VENDOR__', currentVendorId),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    stage_id: stageId
                },
                success: function(response) {
                    if (typeof LoadingOverlay !== 'undefined') LoadingOverlay.hide();
                    bootstrap.Modal.getInstance(document.getElementById('vendorChangeStageModal')).hide();
                    toastr.success(response.message || '{{ trans("order::order.stage_updated_successfully") }}');
                    
                    if (selectedStage && (selectedStage.type === 'in_progress' || selectedStage.slug === 'in-progress')) {
                        setTimeout(function() {
                            window.location.href = '{{ route("admin.order-fulfillments.allocate", ["orderId" => "__ORDER__"]) }}'.replace('__ORDER__', orderId);
                        }, 500);
                    } else {
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: function(xhr) {
                    if (typeof LoadingOverlay !== 'undefined') LoadingOverlay.hide();
                    let errorMessage = '{{ trans("order::order.error_updating_stage") }}';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    toastr.error(errorMessage);
                },
                complete: function() {
                    $btn.prop('disabled', false).html('{{ trans("order::order.update_stage") }}');
                }
            });
        });
        @endif
    });
    </script>
    @endpush
@endonce
