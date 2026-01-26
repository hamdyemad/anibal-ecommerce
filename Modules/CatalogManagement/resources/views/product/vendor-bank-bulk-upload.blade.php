@extends('layout.app')
@section('title', __('catalogmanagement::product.vendor_bank_bulk_upload'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <x-breadcrumb :items="[
                ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                ['title' => __('catalogmanagement::product.vendor_bank_products'), 'url' => route('admin.products.vendor-bank')],
                ['title' => __('catalogmanagement::product.bulk_upload')],
            ]" />
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="userDatatable global-shadow border-0 p-30 bg-white radius-xl w-100 mb-30">
                <div class="d-flex justify-content-between align-items-center mb-25">
                    <h4 class="mb-0 fw-600 text-primary">
                        <i class="uil uil-upload me-2"></i>{{ __('catalogmanagement::product.import_vendor_bank_products') }}
                    </h4>
                    <a href="{{ route('admin.products.vendor-bank.download-demo') }}" 
                        class="btn btn-success btn-squared shadow-sm px-4">
                        <i class="uil uil-download-alt"></i> {{ __('catalogmanagement::product.download_demo_excel') }}
                    </a>
                </div>

                {{-- Alert Messages --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="uil uil-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="uil uil-times-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="uil uil-times-circle me-2"></i>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Import Errors --}}
                @if(session('import_errors'))
                    <div class="alert alert-danger border-0 shadow-sm mb-3" role="alert">
                        <div class="d-flex align-items-center">
                            <div class="alert-icon me-3">
                                <i class="uil uil-exclamation-triangle fs-3 text-danger"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading mb-1 fw-bold">{{ __('catalogmanagement::product.import_errors') }}</h5>
                                <p class="mb-0 text-muted small">{{ __('catalogmanagement::product.import_errors_description') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                                <table class="table table-hover table-bordered mb-0">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th style="width: 120px;" class="text-center">{{ __('catalogmanagement::product.sheet') }}</th>
                                            <th style="width: 80px;" class="text-center">{{ __('catalogmanagement::product.row') }}</th>
                                            <th style="width: 180px;">{{ __('catalogmanagement::product.sku') }}</th>
                                            <th>{{ __('catalogmanagement::product.error') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(session('import_errors') as $error)
                                            <tr>
                                                <td class="text-center align-middle">
                                                    <span class="badge badge-round badge-lg bg-info text-white">{{ $error['sheet'] ?? 'variants' }}</span>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <span class="badge bg-light text-dark border">{{ $error['row'] }}</span>
                                                </td>
                                                <td class="align-middle">
                                                    <code class="text-danger fw-bold">{{ $error['sku'] ?? '-' }}</code>
                                                </td>
                                                <td class="align-middle">
                                                    @if(is_array($error['errors']))
                                                        <ul class="mb-0 ps-3 text-dark">
                                                            @foreach($error['errors'] as $err)
                                                                <li class="mb-1">{{ $err }}</li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <span class="text-dark">{{ $error['errors'] }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Upload Form --}}
                <div class="card border-0 shadow-sm" id="uploadFormCard">
                    <div class="card-body p-4">
                        <form action="{{ route('admin.products.vendor-bank.bulk-upload.store') }}" method="POST" enctype="multipart/form-data" id="bulkUploadForm">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <label for="file" class="il-gray fs-14 fw-500 mb-10">
                                        <i class="uil uil-file-upload me-1"></i> {{ __('catalogmanagement::product.choose_excel_file') }}
                                    </label>
                                    <input type="file" 
                                        class="form-control @error('file') is-invalid @enderror" 
                                        id="file" 
                                        name="file" 
                                        accept=".xlsx,.xls"
                                        required>
                                    <small class="text-muted">{{ __('catalogmanagement::product.accepted_formats') }}: .xlsx, .xls</small>
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div id="fileName" class="mt-2 text-primary"></div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 d-flex gap-3">
                                    <button type="submit" class="btn btn-primary btn-squared px-5" id="importBtn">
                                        <i class="uil uil-import"></i> {{ __('catalogmanagement::product.import') }}
                                    </button>
                                    <a href="{{ route('admin.products.vendor-bank') }}" class="btn btn-light btn-squared px-5">
                                        <i class="uil uil-times"></i> {{ __('common.cancel') }}
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Instructions --}}
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="uil uil-info-circle me-2"></i>{{ __('catalogmanagement::product.import_instructions') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6 class="fw-bold mb-2">{{ __('catalogmanagement::product.vendor_bank_import_note') }}</h6>
                            <p class="mb-0">{{ __('catalogmanagement::product.vendor_bank_import_description') }}</p>
                        </div>

                        <h6 class="fw-bold mt-3 mb-2">{{ __('catalogmanagement::product.excel_structure') }}:</h6>
                        <ul>
                            <li><strong>variants</strong> - {{ __('catalogmanagement::product.variants_sheet_description') }}</li>
                            <li><strong>variant_stock</strong> - {{ __('catalogmanagement::product.variant_stock_sheet_description') }}</li>
                        </ul>

                        <h6 class="fw-bold mt-3 mb-2">{{ __('catalogmanagement::product.important_notes') }}:</h6>
                        <ol>
                            <li>{{ __('catalogmanagement::product.vendor_bank_note_1') }}</li>
                            <li>{{ __('catalogmanagement::product.vendor_bank_note_2') }}</li>
                            <li>{{ __('catalogmanagement::product.vendor_bank_note_3') }}</li>
                            <li>{{ __('catalogmanagement::product.vendor_bank_note_4') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // File input change handler
        $('#file').on('change', function() {
            const fileName = $(this).val().split('\\').pop();
            $('#fileName').text(fileName ? `{{ __("catalogmanagement::product.selected_file") }}: ${fileName}` : '');
        });

        // Form submission handler - SYNCHRONOUS IMPORT
        $('#bulkUploadForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const $importBtn = $('#importBtn');
            const originalBtnText = $importBtn.html();
            
            // Disable button and show loading
            $importBtn.prop('disabled', true).html('<i class="uil uil-spinner-alt rotating"></i> {{ __("catalogmanagement::product.importing") }}...');
            
            // Show loading message
            toastr.info('Processing import... This may take a few moments.', 'Please Wait', {
                timeOut: 0,
                extendedTimeOut: 0,
                closeButton: false,
                tapToDismiss: false
            });
            
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                timeout: 300000, // 5 minutes timeout
                success: function(response) {
                    console.log('Import response:', response);
                    
                    // Clear loading message
                    toastr.clear();
                    
                    // Re-enable button
                    $importBtn.prop('disabled', false).html(originalBtnText);
                    
                    if (response.success) {
                        const importedCount = response.imported_count || 0;
                        const totalErrors = response.total_errors || 0;
                        
                        console.log('Imported:', importedCount, 'Errors:', totalErrors);
                        
                        if (totalErrors === 0) {
                            toastr.success(`Successfully imported ${importedCount} products!`);
                            
                            // Clear file input
                            $('#file').val('');
                            $('#fileName').text('');
                            
                            // Optionally reload page after 2 seconds
                            setTimeout(function() {
                                window.location.reload();
                            }, 2000);
                        } else {
                            console.log('Displaying errors:', response.errors);
                            toastr.warning(`Imported ${importedCount} products with ${totalErrors} errors. Check details below.`);
                            
                            // Clear file input
                            $('#file').val('');
                            $('#fileName').text('');
                            
                            // Display errors
                            if (typeof displayImportErrors === 'function') {
                                displayImportErrors(response.errors);
                            } else {
                                console.error('displayImportErrors function not found!');
                            }
                            
                            // Scroll to errors
                            setTimeout(function() {
                                const errorAlert = $('.import-errors-container').first();
                                if (errorAlert.length) {
                                    $('html, body').animate({
                                        scrollTop: errorAlert.offset().top - 100
                                    }, 500);
                                }
                            }, 200);
                        }
                    } else {
                        toastr.error(response.message || '{{ __("catalogmanagement::product.import_error") }}');
                    }
                },
                error: function(xhr) {
                    // Clear loading message
                    toastr.clear();
                    
                    let error = '{{ __("catalogmanagement::product.import_error") }}';
                    
                    if (xhr.status === 0) {
                        error = 'Request timeout or network error. The file might be too large or the server is not responding.';
                    } else if (xhr.responseJSON?.error) {
                        error = xhr.responseJSON.error;
                    } else if (xhr.responseJSON?.message) {
                        error = xhr.responseJSON.message;
                    } else if (xhr.statusText) {
                        error = xhr.statusText;
                    }
                    
                    toastr.error(error, 'Import Failed', {
                        timeOut: 10000
                    });
                    
                    $importBtn.prop('disabled', false).html(originalBtnText);
                }
            });
        });
        
        // Function to display import errors
        function displayImportErrors(errors) {
            if (!errors || errors.length === 0) return;
            
            // Remove any existing error displays
            $('.import-errors-container').remove();
            
            // Build error HTML
            let html = '<div class="import-errors-container">';
            html += '<div class="alert alert-danger border-0 shadow-sm mb-3" role="alert">';
            html += '<div class="d-flex align-items-center">';
            html += '<div class="alert-icon me-3"><i class="uil uil-exclamation-triangle fs-3 text-danger"></i></div>';
            html += '<div><h5 class="alert-heading mb-1 fw-bold">{{ __("catalogmanagement::product.import_errors") }}</h5>';
            html += '<p class="mb-0 text-muted small">{{ __("catalogmanagement::product.import_errors_description") }}</p></div>';
            html += '</div></div>';
            
            html += '<div class="card border-0 shadow-sm mb-4">';
            html += '<div class="card-body p-0">';
            html += '<div class="table-responsive" style="max-height: 500px; overflow-y: auto;">';
            html += '<table class="table table-hover table-bordered mb-0">';
            html += '<thead class="table-light sticky-top" style="box-shadow: 0 2px 4px rgba(0,0,0,0.05);">';
            html += '<tr><th class="text-center" style="width: 120px;">{{ __("catalogmanagement::product.sheet") }}</th>';
            html += '<th class="text-center" style="width: 80px;">{{ __("catalogmanagement::product.row") }}</th>';
            html += '<th style="width: 180px;">{{ __("catalogmanagement::product.sku") }}</th>';
            html += '<th>{{ __("catalogmanagement::product.error") }}</th></tr></thead><tbody>';
            
            errors.forEach(function(error) {
                // Dynamic badge colors based on sheet type
                const sheetBadgeClass = {
                    'variants': 'bg-info',
                    'variant_stock': 'bg-warning text-dark'
                }[error.sheet] || 'bg-secondary';
                
                const identifier = error.sku || error.id || error.variant_id || '-';
                const errorMessages = Array.isArray(error.errors) ? error.errors.join('<br>') : error.errors;
                const sheetName = error.sheet || 'unknown';
                
                html += '<tr>';
                html += `<td class="text-center align-middle">`;
                html += `<span class="badge badge-round ${sheetBadgeClass} badge-lg">${sheetName}</span>`;
                html += `</td>`;
                html += `<td class="text-center align-middle"><span class="badge bg-light text-dark border badge-round">${error.row}</span></td>`;
                html += `<td class="align-middle"><code class="text-danger fw-bold">${identifier}</code></td>`;
                html += `<td class="align-middle">${errorMessages}</td>`;
                html += '</tr>';
            });
            
            html += '</tbody></table></div></div></div>';
            html += '</div>';
            
            console.log('Inserting error HTML before upload form');
            
            // Insert errors before the upload form card
            $('#uploadFormCard').before(html);
            
            console.log('Error HTML inserted, container count:', $('.import-errors-container').length);
        }
        
        // Show toastr notifications for session messages
        @if(session('success'))
            toastr.success('{{ session('success') }}', '{{ __('common.success') ?? 'Success' }}', {
                closeButton: true,
                progressBar: true,
                timeOut: 5000
            });
        @endif

        @if(session('warning'))
            toastr.warning('{{ session('warning') }}', '{{ __('common.warning') ?? 'Warning' }}', {
                closeButton: true,
                progressBar: true,
                timeOut: 8000
            });
        @endif

        @if(session('error'))
            toastr.error('{{ session('error') }}', '{{ __('common.error') ?? 'Error' }}', {
                closeButton: true,
                progressBar: true,
                timeOut: 8000
            });
        @endif

        @if(session('info'))
            toastr.info('{{ session('info') }}', '{{ __('common.info') ?? 'Info' }}', {
                closeButton: true,
                progressBar: true,
                timeOut: 5000
            });
        @endif
    });
</script>

<style>
    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .rotating {
        animation: rotate 1s linear infinite;
        display: inline-block;
    }
    
    /* Sticky table header */
    .sticky-top {
        position: sticky;
        top: 0;
        z-index: 10;
    }
</style>
@endpush
