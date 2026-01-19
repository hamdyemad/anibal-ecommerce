@props(['vendorId', 'days'])

<div class="d-flex justify-content-center">
    <input type="number" 
        class="form-control text-center refund-days-input" 
        data-vendor-id="{{ $vendorId }}" 
        data-original-value="{{ $days }}"
        value="{{ $days }}" 
        min="1" 
        max="365"
        style="cursor: pointer;">
</div>

<style>
    /* Refund days input styling */
    .refund-days-input {
        width: 100%;
        max-width: 120px;
        margin: 0 auto;
        text-align: center;
        font-weight: 500;
        border: 1px solid #e3e6ef;
        border-radius: 6px;
        padding: 8px 12px;
        transition: all 0.3s ease;
    }
    
    .refund-days-input:focus {
        border-color: #5f63f2;
        box-shadow: 0 0 0 0.2rem rgba(95, 99, 242, 0.15);
    }
    
    .refund-days-input:disabled {
        background-color: #f4f5f7;
        cursor: not-allowed;
    }
</style>

<script>
if (typeof window.refundDaysInputInitialized === 'undefined') {
    window.refundDaysInputInitialized = true;
    
    $(document).ready(function() {
        // Handle refund days input blur (when user finishes editing)
        $(document).on('blur', '.refund-days-input', function() {
            const input = $(this);
            const vendorId = input.data('vendor-id');
            const days = parseInt(input.val());
            const originalValue = parseInt(input.data('original-value'));
            
            // Skip if value hasn't changed
            if (days === originalValue) {
                return;
            }
            
            // Validate
            if (days < 1 || days > 365 || isNaN(days)) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ trans("common.error") }}',
                        text: '{{ trans("refund::refund.validation.refund_processing_days_min", ["min" => 1]) }}',
                        toast: true,
                        position: 'top-end',
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
                // Restore original value
                input.val(originalValue);
                return;
            }
            
            // Disable input during request
            input.prop('disabled', true);
            
            // Show loading overlay
            if (typeof LoadingOverlay !== 'undefined') {
                LoadingOverlay.show({
                    text: '{{ trans("refund::refund.messages.settings_updated") }}',
                    subtext: '{{ trans("common.please_wait") }}...'
                });
            }
            
            $.ajax({
                url: '{{ route("admin.refunds.admin-settings.update-refund-days", ":vendor") }}'.replace(':vendor', vendorId),
                method: 'PUT',
                data: {
                    refund_processing_days: days,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    // Hide loading overlay
                    if (typeof LoadingOverlay !== 'undefined') {
                        LoadingOverlay.hide();
                    }
                    
                    if (response.success) {
                        // Update original value
                        input.data('original-value', days);
                        
                        // Show success message
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: '{{ trans("common.success") }}',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });
                        }
                    } else {
                        // Restore original value
                        input.val(originalValue);
                        
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ trans("common.error") }}',
                                text: response.message || '{{ trans("common.error") }}'
                            });
                        }
                    }
                },
                error: function(xhr) {
                    // Hide loading overlay
                    if (typeof LoadingOverlay !== 'undefined') {
                        LoadingOverlay.hide();
                    }
                    
                    // Restore original value
                    input.val(originalValue);
                    
                    const error = xhr.responseJSON?.message || '{{ trans("common.error") }}';
                    
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ trans("common.error") }}',
                            text: error
                        });
                    }
                },
                complete: function() {
                    input.prop('disabled', false);
                }
            });
        });
    });
}
</script>
