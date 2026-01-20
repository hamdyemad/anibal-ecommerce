@props([
    'refund' => null,
    'showButtons' => false,
])

{{-- Status Change Buttons (for show page) --}}
@if($showButtons && $refund && $refund->canChangeStatus() && count($refund->getNextStatuses()) > 0)
<div class="mt-4">
    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.actions') }}</label>
    <div class="d-grid gap-2">
        @foreach($refund->getNextStatuses() as $nextStatus)
            @php
                $statusConfig = [
                    'approved' => ['color' => 'info', 'icon' => 'uil-check'],
                    'cancelled' => ['color' => 'danger', 'icon' => 'uil-ban'],
                    'in_progress' => ['color' => 'primary', 'icon' => 'uil-sync'],
                    'picked_up' => ['color' => 'secondary', 'icon' => 'uil-package'],
                    'refunded' => ['color' => 'success', 'icon' => 'uil-check-circle'],
                ];
                $config = $statusConfig[$nextStatus] ?? ['color' => 'secondary', 'icon' => 'uil-arrow-right'];
            @endphp
            
            @if($nextStatus === 'cancelled')
                <button type="button" 
                    class="btn btn-{{ $config['color'] }} w-100" 
                    data-bs-toggle="modal" 
                    data-bs-target="#cancelModal"
                    data-refund-id="{{ $refund->id }}">
                    <i class="uil {{ $config['icon'] }} me-2"></i>{{ trans('refund::refund.statuses.' . $nextStatus) }}
                </button>
            @else
                <button type="button" 
                    class="btn btn-{{ $config['color'] }} w-100 change-refund-status" 
                    data-refund-id="{{ $refund->id }}"
                    data-status="{{ $nextStatus }}">
                    <i class="uil {{ $config['icon'] }} me-2"></i>{{ trans('refund::refund.statuses.' . $nextStatus) }}
                </button>
            @endif
        @endforeach
    </div>
</div>
@endif

{{-- Include modals and JS only once per page --}}
@once
    @push('after-body')
        {{-- Refund Status Change Confirmation Modal --}}
        <div class="modal fade" id="refundStatusChangeModal" tabindex="-1" aria-labelledby="refundStatusChangeModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="refundStatusChangeModalLabel">
                            <i class="uil uil-exclamation-triangle me-2"></i>{{ trans('common.confirm_action') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="refundStatusChangeId">
                        <input type="hidden" id="refundStatusChangeTarget">
                        <div class="alert alert-info mb-3">
                            <i class="uil uil-info-circle me-2"></i>
                            {{ trans('refund::refund.actions.status_change_warning') }}
                        </div>
                        <p id="refundStatusChangeMessage" class="mb-0 fs-16"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                        <button type="button" class="btn btn-primary"
                            id="confirmRefundStatusChangeBtn">{{ __('common.confirm') }}</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cancel Modal (with cancellation reason) --}}
        <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="cancelRefundForm" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="cancelModalLabel">
                                <i class="uil uil-ban me-2"></i>{{ trans('refund::refund.actions.cancel') }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="il-gray fs-14 fw-500 mb-10">{{ trans('refund::refund.fields.cancellation_reason') }} <span class="text-danger">*</span></label>
                                <textarea name="cancellation_reason" class="form-control" rows="4" required placeholder="{{ trans('refund::refund.fields.cancellation_reason') }}"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('common.cancel') }}</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="uil uil-ban me-1"></i>{{ trans('refund::refund.actions.cancel') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endpush

    {{-- JavaScript for Refund Actions --}}
    @push('scripts')
        <script>
            $(document).ready(function() {
                // Status labels and routes
                const statusConfig = {
                    'pending': {
                        label: '{{ trans("refund::refund.statuses.pending") }}'
                    },
                    'approved': {
                        label: '{{ trans("refund::refund.statuses.approved") }}',
                        route: '{{ route("admin.refunds.approve", ":id") }}'
                    },
                    'in_progress': {
                        label: '{{ trans("refund::refund.statuses.in_progress") }}',
                        route: '{{ route("admin.refunds.in-progress", ":id") }}'
                    },
                    'picked_up': {
                        label: '{{ trans("refund::refund.statuses.picked_up") }}',
                        route: '{{ route("admin.refunds.picked-up", ":id") }}'
                    },
                    'refunded': {
                        label: '{{ trans("refund::refund.statuses.refunded") }}',
                        route: '{{ route("admin.refunds.refunded", ":id") }}'
                    },
                    'cancelled': {
                        label: '{{ trans("refund::refund.statuses.cancelled") }}',
                        route: '{{ route("admin.refunds.cancel", ":id") }}'
                    },
                };

                // Handle cancel modal - set form action dynamically
                $('#cancelModal').on('show.bs.modal', function(event) {
                    const button = $(event.relatedTarget);
                    const refundId = button.data('refund-id') || '{{ $refund->id ?? "" }}';
                    
                    if (refundId) {
                        const cancelUrl = statusConfig.cancelled.route.replace(':id', refundId);
                        $('#cancelRefundForm').attr('action', cancelUrl);
                        $('#cancelRefundForm').data('refund-id', refundId);
                    }
                });

                // Handle cancel form submission via AJAX
                $('#cancelRefundForm').on('submit', function(e) {
                    e.preventDefault();
                    
                    const form = $(this);
                    const url = form.attr('action');
                    const cancellationReason = form.find('textarea[name="cancellation_reason"]').val();
                    const submitBtn = form.find('button[type="submit"]');
                    
                    if (!cancellationReason) {
                        toastr.error('{{ trans("refund::refund.validation.cancellation_reason_required") }}');
                        return;
                    }
                    
                    // Disable submit button
                    submitBtn.prop('disabled', true);
                    submitBtn.html('<i class="uil uil-spinner-alt rotating me-1"></i>{{ trans("common.processing") }}');
                    
                    // Show loading overlay
                    if (typeof LoadingOverlay !== 'undefined') {
                        LoadingOverlay.show({
                            text: '{{ trans("common.processing") }}',
                            subtext: '{{ trans("common.please_wait") }}...'
                        });
                    }
                    
                    // Send AJAX request
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            cancellation_reason: cancellationReason
                        },
                        success: function(response) {
                            // Hide loading overlay
                            if (typeof LoadingOverlay !== 'undefined') {
                                LoadingOverlay.hide();
                            }
                            
                            // Hide modal
                            bootstrap.Modal.getInstance(document.getElementById('cancelModal')).hide();
                            
                            toastr.success(response.message || '{{ trans("refund::refund.messages.cancelled_successfully") }}');
                            
                            // Check if we're on the show page or list page
                            if (window.dataTable) {
                                // List page - reload datatable
                                window.dataTable.ajax.reload(null, false);
                            } else {
                                // Show page - reload the page
                                setTimeout(function() {
                                    window.location.reload();
                                }, 1000);
                            }
                            
                            // Reset form
                            form[0].reset();
                            submitBtn.prop('disabled', false);
                            submitBtn.html('<i class="uil uil-ban me-1"></i>{{ trans("refund::refund.actions.cancel") }}');
                        },
                        error: function(xhr) {
                            // Hide loading overlay
                            if (typeof LoadingOverlay !== 'undefined') {
                                LoadingOverlay.hide();
                            }
                            
                            let errorMessage = '{{ trans("refund::refund.actions.status_change_failed") }}';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            toastr.error(errorMessage);
                            
                            // Reset button
                            submitBtn.prop('disabled', false);
                            submitBtn.html('<i class="uil uil-ban me-1"></i>{{ trans("refund::refund.actions.cancel") }}');
                        }
                    });
                });

                // Handle status change button clicks - show modal
                $(document).on('click', '.change-refund-status', function() {
                    const button = $(this);
                    const refundId = button.data('refund-id');
                    const newStatus = button.data('status');
                    const statusLabel = statusConfig[newStatus]?.label || newStatus;
                    
                    // Set modal data
                    $('#refundStatusChangeId').val(refundId);
                    $('#refundStatusChangeTarget').val(newStatus);
                    $('#refundStatusChangeMessage').html(
                        '<strong>{{ trans("refund::refund.actions.confirm_status_change_to") }}:</strong> ' + statusLabel
                    );
                    
                    // Show modal
                    const modal = new bootstrap.Modal(document.getElementById('refundStatusChangeModal'));
                    modal.show();
                });

                // Handle confirm button click in modal
                $('#confirmRefundStatusChangeBtn').on('click', function() {
                    const button = $(this);
                    const refundId = $('#refundStatusChangeId').val();
                    const newStatus = $('#refundStatusChangeTarget').val();
                    
                    // Get the route for this status
                    const statusRoute = statusConfig[newStatus]?.route;
                    if (!statusRoute) {
                        toastr.error('Invalid status');
                        return;
                    }
                    
                    const url = statusRoute.replace(':id', refundId);
                    
                    // Disable button and show loading
                    button.prop('disabled', true);
                    button.html('<i class="uil uil-spinner-alt rotating me-1"></i>{{ trans("common.processing") }}');
                    
                    // Show loading overlay
                    if (typeof LoadingOverlay !== 'undefined') {
                        LoadingOverlay.show({
                            text: '{{ trans("common.processing") }}',
                            subtext: '{{ trans("common.please_wait") }}...'
                        });
                    }
                    
                    // Send AJAX request
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            // Hide loading overlay
                            if (typeof LoadingOverlay !== 'undefined') {
                                LoadingOverlay.hide();
                            }
                            
                            // Hide modal
                            bootstrap.Modal.getInstance(document.getElementById('refundStatusChangeModal')).hide();
                            
                            if (response.success || response.status) {
                                toastr.success(response.message || '{{ trans("refund::refund.actions.status_changed_successfully") }}');
                                
                                // Check if we're on the show page or list page
                                if (window.dataTable) {
                                    // List page - reload datatable
                                    window.dataTable.ajax.reload(null, false);
                                } else {
                                    // Show page - reload the page
                                    setTimeout(function() {
                                        window.location.reload();
                                    }, 1000);
                                }
                            } else {
                                toastr.error(response.message || '{{ trans("refund::refund.actions.status_change_failed") }}');
                            }
                            
                            // Reset button
                            button.prop('disabled', false);
                            button.html('{{ __("common.confirm") }}');
                        },
                        error: function(xhr) {
                            // Hide loading overlay
                            if (typeof LoadingOverlay !== 'undefined') {
                                LoadingOverlay.hide();
                            }
                            
                            // Hide modal
                            bootstrap.Modal.getInstance(document.getElementById('refundStatusChangeModal')).hide();
                            
                            let errorMessage = '{{ trans("refund::refund.actions.status_change_failed") }}';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            toastr.error(errorMessage);
                            
                            // Reset button
                            button.prop('disabled', false);
                            button.html('{{ __("common.confirm") }}');
                        }
                    });
                });
            });
        </script>

        <style>
            @keyframes rotating {
                from {
                    transform: rotate(0deg);
                }
                to {
                    transform: rotate(360deg);
                }
            }

            .rotating {
                animation: rotating 1s linear infinite;
            }
        </style>
    @endpush
@endonce
