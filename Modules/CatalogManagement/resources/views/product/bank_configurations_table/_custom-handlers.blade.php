{{-- Bank Products DataTable Custom Event Handlers --}}
<script>
// Define columns for DataTable
window.datatableColumns = [
    {
        data: 'index',
        name: 'index',
        orderable: false,
        searchable: false,
        className: 'text-center fw-bold'
    },
    {
        data: 'product_information',
        name: 'product_information',
        orderable: false,
        searchable: true,
        className: 'text-start',
        render: renderBankProductInformation
    },
    {
        data: 'active',
        name: 'active',
        orderable: false,
        searchable: false,
        className: 'text-center',
        render: renderBankActivation
    },
    {
        data: 'created_at',
        name: 'created_at',
        searchable: false,
        orderable: false
    },
    @can('products.bank')
    {
        data: null,
        orderable: false,
        searchable: false,
        className: 'text-center',
        render: renderBankActions
    }
    @endcan
];

$(document).ready(function() {
    // Activation switcher handler (admin only)
    @can('products.bank.change-activation')
        $(document).on('change', '.activation-switcher', function() {
            const switcher = $(this);
            const productId = switcher.data('product-id');
            const productName = switcher.data('product-name');
            const newStatus = switcher.is(':checked') ? 1 : 2;

            switcher.prop('disabled', true);

            if (typeof LoadingOverlay !== 'undefined') {
                LoadingOverlay.show({
                    text: '{{ __('catalogmanagement::product.change_activation') }}',
                    subtext: '{{ __('common.please_wait') ?? 'Please wait' }}...'
                });
            }

            $.ajax({
                url: '{{ route('admin.products.change-bank-activation', ':id') }}'.replace(':id', productId),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: newStatus
                },
                success: function(response) {
                    if (typeof LoadingOverlay !== 'undefined') {
                        LoadingOverlay.hide();
                    }

                    if (response.success) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: '{{ __('common.success') ?? 'Success' }}',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });
                        } else if (typeof toastr !== 'undefined') {
                            toastr.success(response.message);
                        }
                        $('#bankProductsDataTable').DataTable().ajax.reload(null, false);
                    } else {
                        switcher.prop('checked', !switcher.is(':checked'));
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('common.error') ?? 'Error' }}',
                                text: response.message
                            });
                        } else if (typeof toastr !== 'undefined') {
                            toastr.error(response.message);
                        }
                    }
                },
                error: function(xhr) {
                    if (typeof LoadingOverlay !== 'undefined') {
                        LoadingOverlay.hide();
                    }
                    switcher.prop('checked', !switcher.is(':checked'));
                    let errorMessage = '{{ __('catalogmanagement::product.error_changing_activation') }}';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('common.error') ?? 'Error' }}',
                            text: errorMessage
                        });
                    } else if (typeof toastr !== 'undefined') {
                        toastr.error(errorMessage);
                    }
                },
                complete: function() {
                    switcher.prop('disabled', false);
                }
            });
        });
    @endcan
});
</script>
