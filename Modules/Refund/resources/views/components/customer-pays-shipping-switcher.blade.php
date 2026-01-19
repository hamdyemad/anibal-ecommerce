@props(['vendorId', 'vendorName', 'checked' => false])

<div class="d-flex justify-content-center">
    <div class="form-switch">
        <input class="form-check-input shipping-switcher" 
            type="checkbox" 
            id="shipping-switch-{{ $vendorId }}"
            data-vendor-id="{{ $vendorId }}" 
            data-vendor-name="{{ htmlspecialchars($vendorName, ENT_QUOTES) }}"
            {{ $checked ? 'checked' : '' }}
            style="cursor: pointer;">
        <label class="form-check-label" for="shipping-switch-{{ $vendorId }}"></label>
    </div>
</div>

<style>
    /* Shipping switcher styling */
    .form-switch {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 1.5rem;
    }
    
    .form-switch .form-check-input.shipping-switcher {
        width: 3rem;
        height: 1.5rem;
        cursor: pointer;
        background-color: #e3e6ef;
        border: none;
        transition: all 0.3s ease;
        margin: 0;
    }
    
    .form-switch .form-check-input.shipping-switcher:checked {
        background-color: #5f63f2;
    }
    
    .form-switch .form-check-input.shipping-switcher:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .form-switch .form-check-input.shipping-switcher:focus {
        box-shadow: 0 0 0 0.2rem rgba(95, 99, 242, 0.15);
    }
</style>

<script>
if (typeof window.shippingSwitcherInitialized === 'undefined') {
    window.shippingSwitcherInitialized = true;
    
    $(document).ready(function() {
        // Handle customer pays shipping switcher change
        $(document).on('change', '.shipping-switcher', function() {
            const switcher = $(this);
            const vendorId = switcher.data('vendor-id');
            const vendorName = switcher.data('vendor-name');
            const checked = switcher.is(':checked');
            
            // Disable switcher during request
            switcher.prop('disabled', true);
            
            // Show loading overlay
            if (typeof LoadingOverlay !== 'undefined') {
                LoadingOverlay.show({
                    text: '{{ trans("refund::refund.messages.settings_updated") }}',
                    subtext: '{{ trans("common.please_wait") }}...'
                });
            }
            
            $.ajax({
                url: '{{ route("admin.refunds.admin-settings.update-customer-pays-shipping", ":vendor") }}'.replace(':vendor', vendorId),
                method: 'PUT',
                data: {
                    customer_pays_return_shipping: checked ? 1 : 0,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    // Hide loading overlay
                    if (typeof LoadingOverlay !== 'undefined') {
                        LoadingOverlay.hide();
                    }
                    
                    if (response.success) {
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
                        // Revert switcher state
                        switcher.prop('checked', !checked);
                        
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ trans("common.error") }}',
                                text: response.message
                            });
                        }
                    }
                },
                error: function(xhr) {
                    // Hide loading overlay
                    if (typeof LoadingOverlay !== 'undefined') {
                        LoadingOverlay.hide();
                    }
                    
                    // Revert switcher state
                    switcher.prop('checked', !checked);
                    
                    let errorMessage = '{{ trans("common.error") }}';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ trans("common.error") }}',
                            text: errorMessage
                        });
                    }
                },
                complete: function() {
                    // Re-enable switcher
                    switcher.prop('disabled', false);
                }
            });
        });
    });
}
</script>
