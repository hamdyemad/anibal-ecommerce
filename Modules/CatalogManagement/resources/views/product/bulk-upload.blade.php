@extends('layout.app')
@section('title', __('catalogmanagement::product.bulk_upload'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <x-breadcrumb :items="[
                ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                ['title' => __('catalogmanagement::product.products_management'), 'url' => route('admin.products.index')],
                ['title' => __('catalogmanagement::product.bulk_upload')],
            ]" />
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="userDatatable global-shadow border-0 p-30 bg-white radius-xl w-100 mb-30">
                <div class="d-flex justify-content-between align-items-center mb-25">
                    <h4 class="mb-0 fw-600 text-primary">
                        <i class="uil uil-upload me-2"></i>{{ __('catalogmanagement::product.import_products_from_excel') }}
                    </h4>
                    <a href="{{ route('admin.products.download-demo') }}" 
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

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="uil uil-exclamation-triangle me-2"></i>
                        {{ session('warning') }}
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
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <h6 class="alert-heading"><i class="uil uil-exclamation-triangle me-2"></i>{{ __('catalogmanagement::product.import_errors') }}</h6>
                        <div class="table-responsive mt-3" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th style="width: 80px;">{{ __('common.sheet') }}</th>
                                        <th style="width: 60px;">{{ __('common.row') }}</th>
                                        <th style="width: 120px;">{{ __('catalogmanagement::product.sku') }}</th>
                                        <th>{{ __('common.error') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(session('import_errors') as $error)
                                        <tr>
                                            <td><span class="badge bg-secondary">{{ $error['sheet'] ?? 'products' }}</span></td>
                                            <td class="text-center">{{ $error['row'] }}</td>
                                            <td><code>{{ $error['sku'] ?? $error['id'] ?? $error['variant_id'] ?? $error['product_id'] ?? $error['occasion_id'] ?? '-' }}</code></td>
                                            <td>
                                                @if(is_array($error['errors']))
                                                    <ul class="mb-0 ps-3">
                                                        @foreach($error['errors'] as $err)
                                                            <li>{{ $err }}</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    {{ $error['errors'] }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Upload Form --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form action="{{ route('admin.products.bulk-upload.store') }}" method="POST" enctype="multipart/form-data" id="bulkUploadForm">
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
                                    <small class="text-muted">{{ __('catalogmanagement::product.accepted_formats') }}: .xlsx, .xls ({{ __('catalogmanagement::product.max_file_size') }})</small>
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
                                    <a href="{{ route('admin.products.index') }}" class="btn btn-light btn-squared px-5">
                                        <i class="uil uil-times"></i> {{ __('common.cancel') }}
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Instructions --}}
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header ">
                        <h6 class="mb-0"><i class="uil uil-info-circle me-2"></i>{{ __('catalogmanagement::product.import_instructions') }}</h6>
                    </div>
                    <div class="card-body">
                        <ol class="mb-0">
                            <li>{{ __('catalogmanagement::product.instruction_1') }}</li>
                            <li>{{ __('catalogmanagement::product.instruction_2') }}</li>
                            <li>{{ __('catalogmanagement::product.instruction_3') }}</li>
                            <li>{{ __('catalogmanagement::product.instruction_4') }}</li>
                            <li>{{ __('catalogmanagement::product.instruction_5') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('file').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name;
    if (fileName) {
        document.getElementById('fileName').textContent = '{{ __("catalogmanagement::product.selected_file") }}: ' + fileName;
    }
});

document.getElementById('bulkUploadForm').addEventListener('submit', function() {
    const btn = document.getElementById('importBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>{{ __("common.importing") }}...';
});
</script>
@endpush
@endsection
