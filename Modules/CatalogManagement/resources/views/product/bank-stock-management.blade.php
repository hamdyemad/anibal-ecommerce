@extends('layout.app')

@section('title', __('catalogmanagement::product.import_product_from_bank'))

@push('styles')
@vite(['Modules/CatalogManagement/resources/assets/scss/product-form.scss'])
<style>
    .product-preview-card {
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        padding: 20px;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    }
    .product-preview-card.has-product {
        border-color: #28a745;
    }
    .product-preview-card .product-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
    }
    .vendor-product-status {
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
    }
    .vendor-product-status.new {
        background-color: #e3f2fd;
        color: #1976d2;
    }
    .vendor-product-status.existing {
        background-color: #e8f5e9;
        color: #388e3c;
    }
    .selection-step {
        padding: 20px;
        border: 2px dashed #dee2e6;
        border-radius: 12px;
        margin-bottom: 20px;
    }
    .selection-step.completed {
        border-color: #28a745;
        border-style: solid;
        background-color: #f8fff8;
    }
    .selection-step .step-number {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #6c757d;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        margin-right: 12px;
    }
    .selection-step.completed .step-number {
        background: #28a745;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <x-breadcrumb :items="[
                ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                ['title' => __('catalogmanagement::product.bank_products'), 'url' => route('admin.products.bank')],
                ['title' => __('catalogmanagement::product.import_product_from_bank')]
            ]" />
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title m-0">
                        <i class="uil uil-box me-2"></i>
                        {{ __('catalogmanagement::product.import_product_from_bank') }}
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Step 1: Select Bank Product -->
                    <div class="selection-step" id="step-product">
                        <div class="d-flex align-items-center mb-3">
                            <span class="step-number">1</span>
                            <h5 class="mb-0">{{ __('catalogmanagement::product.select_bank_product') }}</h5>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <select id="bank_product_select" class="form-control select2" style="width: 100%;">
                                    <option value="">{{ __('catalogmanagement::product.search_bank_product') }}</option>
                                </select>
                            </div>
                        </div>
                        <div id="product-preview" class="mt-4" style="display: none;">
                            <div class="product-preview-card">
                                <div class="d-flex align-items-center">
                                    <img id="preview-image" src="" alt="Product" class="product-image me-3">
                                    <div class="product-info">
                                        <h5 id="preview-title-en" class="mb-1"></h5>
                                        <h6 id="preview-title-ar" class="text-muted mb-1"></h6>
                                        <div class="mt-2">
                                            <span class="badge bg-info badge-lg badge-round" id="preview-brand"></span>
                                            <span class="badge bg-secondary badge-lg badge-round" id="preview-category"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Select Vendor -->
                    <div class="selection-step" id="step-vendor" style="opacity: 0.5; pointer-events: none;">
                        <div class="d-flex align-items-center mb-3">
                            <span class="step-number">2</span>
                            <h5 class="mb-0">{{ __('catalogmanagement::product.select_vendor') }}</h5>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <select id="vendor_select" class="form-control select2" style="width: 100%;">
                                    <option value="">{{ __('catalogmanagement::product.select_vendor') }}</option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor['id'] }}">{{ $vendor['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div id="vendor-product-status" class="mt-3" style="display: none;">
                            <span id="status-badge" class="vendor-product-status"></span>
                            <span id="status-message" class="ms-3"></span>
                        </div>
                    </div>

                    <!-- Step 3: Stock Management Form -->
                    <div id="stock-management-section" style="display: none;">
                        @include('catalogmanagement::product.partials.bank-stock-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@include('catalogmanagement::product.partials.bank-stock-scripts')
@endpush
