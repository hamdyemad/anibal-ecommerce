@extends('layout.app')

@section('title', trans('catalogmanagement::occasion.view_occasion'))

@push('styles')
<style>
/* Occasion View HTML Content Styling */
.fs-15.color-dark {
    line-height: 1.6;
}

.fs-15.color-dark table {
    width: 100%;
    border-collapse: collapse;
    margin: 10px 0;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.fs-15.color-dark table th,
.fs-15.color-dark table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #e3e6f0;
}

.fs-15.color-dark table th {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    color: white;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 12px;
}

.fs-15.color-dark table tr:hover {
    background-color: #f8f9fa;
}

.fs-15.color-dark table tr:last-child td {
    border-bottom: none;
}

.fs-15.color-dark strong {
    color: #2c3e50;
    font-weight: 600;
}

.fs-15.color-dark em {
    color: #7f8c8d;
    font-style: italic;
}

.fs-15.color-dark ul,
.fs-15.color-dark ol {
    margin: 10px 0;
    padding-left: 20px;
}

.fs-15.color-dark li {
    margin-bottom: 5px;
    line-height: 1.5;
}

.fs-15.color-dark p {
    margin-bottom: 10px;
    line-height: 1.6;
}

.fs-15.color-dark h1,
.fs-15.color-dark h2,
.fs-15.color-dark h3,
.fs-15.color-dark h4,
.fs-15.color-dark h5,
.fs-15.color-dark h6 {
    margin: 15px 0 10px 0;
    color: #2c3e50;
    font-weight: 600;
}

.fs-15.color-dark blockquote {
    border-left: 4px solid #4e73df;
    padding-left: 15px;
    margin: 15px 0;
    background: #f8f9fa;
    padding: 15px;
    border-radius: 4px;
}

.fs-15.color-dark a {
    color: #4e73df;
    text-decoration: none;
}

.fs-15.color-dark a:hover {
    color: #224abe;
    text-decoration: underline;
}

/* Arabic content styling */
.fs-15.color-dark[style*="direction: rtl"] {
    font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.fs-15.color-dark[style*="direction: rtl"] table th,
.fs-15.color-dark[style*="direction: rtl"] table td {
    text-align: right;
}
</style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    [
                        'title' => trans('catalogmanagement::occasion.occasions_management'),
                        'url' => route('admin.occasions.index'),
                    ],
                    ['title' => trans('catalogmanagement::occasion.view_occasion')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ trans('catalogmanagement::occasion.occasion_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.occasions.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ trans('common.back_to_list') }}
                            </a>
                            <a href="{{ route('admin.occasions.edit', $occasion->id) }}" class="btn btn-primary btn-sm">
                                <i class="uil uil-edit me-2"></i>{{ trans('common.edit') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8 order-2 order-md-1">
                                {{-- Basic Information --}}
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-info-circle me-1"></i>{{ trans('catalogmanagement::occasion.basic_information') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Occasion Name --}}
                                            <div class="col-md-12">
                                                <div class="view-item box-items-translations">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::occasion.name') }}</label>
                                                    <div class="row">
                                                        @foreach ($languages as $lang)
                                                            @php
                                                                $translation = $occasion->getTranslation('name', $lang->code);
                                                            @endphp
                                                            <div class="col-md-6 mb-3">
                                                                <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; @if ($lang->code == 'ar') border-right: 3px solid #5f63f2; @else border-left: 3px solid #5f63f2; @endif">
                                                                    <small class="text-muted d-block mb-2" style="@if ($lang->code == 'ar') direction: rtl; text-align: right; @endif">
                                                                        <span class="badge @if ($lang->code == 'en') bg-primary @else bg-success @endif text-white px-2 py-1 round-pill fw-bold" style="font-size: 10px;">{{ strtoupper($lang->code) }}</span>
                                                                    </small>
                                                                    <div class="fs-15 color-dark mb-0 fw-500" style="@if ($lang->code == 'ar') direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">
                                                                        @if($translation)
                                                                            {{ $translation }}
                                                                        @else
                                                                            <span class="text-muted">—</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Occasion Title --}}
                                            <div class="col-md-12">
                                                <div class="view-item box-items-translations">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::occasion.title') }}</label>
                                                    <div class="row">
                                                        @foreach ($languages as $lang)
                                                            @php
                                                                $translation = $occasion->getTranslation('title', $lang->code);
                                                            @endphp
                                                            <div class="col-md-6 mb-3">
                                                                <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; @if ($lang->code == 'ar') border-right: 3px solid #5f63f2; @else border-left: 3px solid #5f63f2; @endif">
                                                                    <small class="text-muted d-block mb-2" style="@if ($lang->code == 'ar') direction: rtl; text-align: right; @endif">
                                                                        <span class="badge @if ($lang->code == 'en') bg-primary @else bg-success @endif text-white px-2 py-1 round-pill fw-bold" style="font-size: 10px;">{{ strtoupper($lang->code) }}</span>
                                                                    </small>
                                                                    <div class="fs-15 color-dark mb-0 fw-500" style="@if ($lang->code == 'ar') direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">
                                                                        @if($translation)
                                                                            {{ $translation }}
                                                                        @else
                                                                            <span class="text-muted">—</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Occasion Sub Title --}}
                                            <div class="col-md-12">
                                                <div class="view-item box-items-translations">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::occasion.sub_title') }}</label>
                                                    <div class="row">
                                                        @foreach ($languages as $lang)
                                                            @php
                                                                $translation = $occasion->getTranslation('sub_title', $lang->code);
                                                            @endphp
                                                            <div class="col-md-6 mb-3">
                                                                <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; @if ($lang->code == 'ar') border-right: 3px solid #5f63f2; @else border-left: 3px solid #5f63f2; @endif">
                                                                    <small class="text-muted d-block mb-2" style="@if ($lang->code == 'ar') direction: rtl; text-align: right; @endif">
                                                                        <span class="badge @if ($lang->code == 'en') bg-primary @else bg-success @endif text-white px-2 py-1 round-pill fw-bold" style="font-size: 10px;">{{ strtoupper($lang->code) }}</span>
                                                                    </small>
                                                                    <div class="fs-15 color-dark mb-0 fw-500" style="@if ($lang->code == 'ar') direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">
                                                                        @if($translation)
                                                                            {{ $translation }}
                                                                        @else
                                                                            <span class="text-muted">—</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Vendor --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::occasion.vendor') }}</label>
                                                    <p class="fs-15">
                                                        @if ($occasion->vendor)
                                                            <span class="badge badge-round badge-primary badge-lg">
                                                                {{ $occasion->vendor->name ?? '-' }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Slug --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::occasion.slug') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        <code>{{ $occasion->slug ?? '-' }}</code>
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Start Date --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::occasion.start_date') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        <i class="uil uil-calendar-alt me-1"></i>{{ $occasion->start_date ? $occasion->start_date : '-' }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- End Date --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::occasion.end_date') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        <i class="uil uil-calendar-alt me-1"></i>{{ $occasion->end_date ? $occasion->end_date : '-' }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Status --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::occasion.status') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        @if ($occasion->is_active)
                                                            <span class="badge badge-round badge-success badge-lg">
                                                                <i class="uil uil-check-circle me-1"></i>{{ trans('catalogmanagement::occasion.active') }}
                                                            </span>
                                                        @else
                                                            <span class="badge badge-round badge-danger badge-lg">
                                                                <i class="uil uil-times-circle me-1"></i>{{ trans('catalogmanagement::occasion.inactive') }}
                                                            </span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Created At --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::occasion.created_at') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        <i class="uil uil-calendar-alt me-1"></i>{{ $occasion->created_at ? $occasion->created_at : '-' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Sidebar --}}
                            <div class="col-md-4 order-1 order-md-2">
                                {{-- Image --}}
                                <div class="card card-holder mb-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-image me-1"></i>{{ trans('catalogmanagement::occasion.image') }}
                                        </h3>
                                    </div>
                                    <div class="card-body text-center">
                                        @php
                                            $imageAttachment = $occasion->attachments()->where('type', 'image')->first();
                                        @endphp
                                        @if($imageAttachment)
                                            <img src="{{ asset('storage/' . $imageAttachment->path) }}" alt="Occasion Image" class="img-fluid round" style="max-width: 100%; max-height: 300px; object-fit: cover;">
                                        @else
                                            <div class="p-5 bg-light round">
                                            <img src="{{ asset('assets/img/default.png') }}" alt="Occasion Image" class="img-fluid round" style="max-width: 100%; max-height: 300px; object-fit: cover;">
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Quick Info --}}
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-info-circle me-1"></i>{{ trans('catalogmanagement::occasion.quick_info') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <small class="text-muted d-block mb-1">{{ trans('catalogmanagement::occasion.total_variants') }}</small>
                                            <div class="fw-bold text-primary" style="font-size: 18px;">
                                                <i class="uil uil-box me-1"></i>{{ $occasion->occasionProducts->count() }}
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="mb-3">
                                            <small class="text-muted d-block mb-1">{{ trans('catalogmanagement::occasion.duration') }}</small>
                                            <div class="fw-bold text-info" style="font-size: 14px;">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                @if($occasion->start_date && $occasion->end_date)
                                                    {{ $occasion->start_date->format('M d, Y') }} - {{ $occasion->end_date->format('M d, Y') }}
                                                @else
                                                    -
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            {{-- SEO Information --}}
                            <div class="card card-holder mt-3">
                                <div class="card-header">
                                    <h3>
                                        <i class="uil uil-search me-1"></i>{{ trans('catalogmanagement::occasion.seo_information') }}
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        {{-- SEO Title --}}
                                        <div class="col-md-12">
                                            <div class="view-item box-items-translations">
                                                <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::occasion.seo_title') }}</label>
                                                <div class="row">
                                                    @foreach ($languages as $lang)
                                                        @php
                                                            $translation = $occasion->getTranslation('seo_title', $lang->code);
                                                        @endphp
                                                        <div class="col-md-6 mb-3">
                                                            <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; @if ($lang->code == 'ar') border-right: 3px solid #5f63f2; @else border-left: 3px solid #5f63f2; @endif">
                                                                <small class="text-muted d-block mb-2" style="@if ($lang->code == 'ar') direction: rtl; text-align: right; @endif">
                                                                    <span class="badge @if ($lang->code == 'en') bg-primary @else bg-success @endif text-white px-2 py-1 round-pill fw-bold" style="font-size: 10px;">{{ strtoupper($lang->code) }}</span>
                                                                </small>
                                                                <div class="fs-15 color-dark mb-0 fw-500" style="@if ($lang->code == 'ar') direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">
                                                                    @if($translation)
                                                                        {{ $translation }}
                                                                    @else
                                                                        <span class="text-muted">—</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                        {{-- SEO Description --}}
                                        <div class="col-md-12">
                                            <div class="view-item box-items-translations">
                                                <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::occasion.seo_description') }}</label>
                                                <div class="row">
                                                    @foreach ($languages as $lang)
                                                        @php
                                                            $translation = $occasion->getTranslation('seo_description', $lang->code);
                                                        @endphp
                                                        <div class="col-md-6 mb-3">
                                                            <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; @if ($lang->code == 'ar') border-right: 3px solid #5f63f2; @else border-left: 3px solid #5f63f2; @endif">
                                                                <small class="text-muted d-block mb-2" style="@if ($lang->code == 'ar') direction: rtl; text-align: right; @endif">
                                                                    <span class="badge @if ($lang->code == 'en') bg-primary @else bg-success @endif text-white px-2 py-1 round-pill fw-bold" style="font-size: 10px;">{{ strtoupper($lang->code) }}</span>
                                                                </small>
                                                                <div class="fs-15 color-dark mb-0 fw-500" style="@if ($lang->code == 'ar') direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">
                                                                    @if($translation)
                                                                        {{ $translation }}
                                                                    @else
                                                                        <span class="text-muted">—</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                        {{-- SEO Keywords --}}
                                        <div class="col-md-12">
                                            <div class="view-item box-items-translations">
                                                <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::occasion.seo_keywords') }}</label>
                                                <div class="row">
                                                    @foreach ($languages as $lang)
                                                        @php
                                                            $translation = $occasion->getTranslation('seo_keywords', $lang->code);
                                                        @endphp
                                                        <div class="col-md-6 mb-3">
                                                            <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; @if ($lang->code == 'ar') border-right: 3px solid #5f63f2; @else border-left: 3px solid #5f63f2; @endif">
                                                                <small class="text-muted d-block mb-2" style="@if ($lang->code == 'ar') direction: rtl; text-align: right; @endif">
                                                                    <span class="badge @if ($lang->code == 'en') bg-primary @else bg-success @endif text-white px-2 py-1 round-pill fw-bold" style="font-size: 10px;">{{ strtoupper($lang->code) }}</span>
                                                                </small>
                                                                <div class="fs-15 color-dark mb-0 fw-500" style="@if ($lang->code == 'ar') direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; @endif">
                                                                    @if($translation)
                                                                        {{ $translation }}
                                                                    @else
                                                                        <span class="text-muted">—</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Occasion Products (Variants) --}}
                            <div class="card card-holder mt-3">
                                <div class="card-header">
                                    <h3>
                                        <i class="uil uil-box me-1"></i>{{ trans('catalogmanagement::occasion.product_variants') }}
                                    </h3>
                                </div>
                                <div class="card-body">
                                    @if($occasion->occasionProducts->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered" id="occasionProductsTable">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>{{ trans('catalogmanagement::occasion.product') }}</th>
                                                        <th>{{ trans('catalogmanagement::occasion.variant_name') }}</th>
                                                        <th>{{ trans('catalogmanagement::occasion.sku') }}</th>
                                                        <th>{{ trans('catalogmanagement::occasion.original_price') }}</th>
                                                        <th>{{ trans('catalogmanagement::occasion.special_price') }}</th>
                                                        <th>{{ trans('catalogmanagement::occasion.position') }}</th>
                                                        <th>{{ __('common.actions') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="occasionProductsBody" class="sortable-tbody">
                                                    @foreach($occasion->occasionProducts as $index => $product)
                                                        <tr class="draggable-row" data-product-id="{{ $product->id }}" data-position="{{ $product->position }}" draggable="true">
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>
                                                                <strong>{{ $product->vendorProductVariant->vendorProduct->product->name ?? '-' }}</strong>
                                                            </td>
                                                            <td>
                                                                {{ $product->vendorProductVariant->variantConfiguration->name ?? 'Default' }}
                                                            </td>
                                                            <td>
                                                                <code>{{ $product->vendorProductVariant->sku ?? '-' }}</code>
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-lg badge-round badge-info">{{ number_format($product->vendorProductVariant->price ?? 0, 2) }} {{ trans('common.egp') }}</span>
                                                            </td>
                                                            <td>
                                                                @if($product->special_price)
                                                                    <span class="badge badge-lg badge-round badge-success">{{ number_format($product->special_price, 2) }} {{ trans('common.egp') }}</span>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-lg badge-round badge-primary">{{ $product->position }}</span>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex gap-2 justify-content-center">
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-danger delete-occasion-product"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#modal-delete-product"
                                                                        data-product-id="{{ $product->id }}"
                                                                        data-occasion-id="{{ $occasion->id }}"
                                                                        data-product-name="{{ $product->vendorProductVariant->vendorProduct->product->name ?? 'Product' }}"
                                                                        title="{{ __('common.delete') }}">
                                                                        <i class="uil uil-trash-alt"></i>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info" role="alert">
                                            <i class="uil uil-info-circle me-2"></i>{{ trans('catalogmanagement::occasion.no_variants') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
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
        let draggedElement = null;
        let draggedOverElement = null;
        let deleteProductData = {};

        // Drag and Drop functionality
        $(document).on('dragstart', '.draggable-row', function(e) {
            draggedElement = this;
            $(this).addClass('dragging').css('opacity', '0.5');
            e.originalEvent.dataTransfer.effectAllowed = 'move';
        });

        $(document).on('dragend', '.draggable-row', function(e) {
            $(this).removeClass('dragging').css('opacity', '1');
            $('.draggable-row').removeClass('drag-over');
            draggedElement = null;
            draggedOverElement = null;
        });

        $(document).on('dragover', '.draggable-row', function(e) {
            e.preventDefault();
            e.originalEvent.dataTransfer.dropEffect = 'move';

            if (this !== draggedElement) {
                $(this).addClass('drag-over');
                draggedOverElement = this;
            }
        });

        $(document).on('dragleave', '.draggable-row', function(e) {
            $(this).removeClass('drag-over');
        });

        $(document).on('drop', '.draggable-row', function(e) {
            e.preventDefault();

            if (this !== draggedElement) {
                // Swap rows
                $(draggedElement).insertBefore($(this));
                updatePositions();
            }
        });

        // Update positions after drag and drop
        function updatePositions() {
            const positions = [];

            $('#occasionProductsBody .draggable-row').each(function(index) {
                const productId = $(this).data('product-id');
                positions.push({
                    product_id: productId,
                    position: index
                });
            });

            // Send update to server
            $.ajax({
                url: `{{ route('admin.occasions.update-positions', $occasion->id) }}`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    positions: positions
                },
                success: function(response) {
                    if (response.status) {
                        toastr.success(response.message || '{{ __("common.order_updated_successfully") }}');
                    } else {
                        toastr.error(response.message || '{{ __("common.error_updating_order") }}');
                    }
                },
                error: function(xhr) {
                    toastr.error('{{ __("common.error_updating_order") }}');
                }
            });
        }

        // Store delete product data when modal is opened
        $(document).on('click', '.delete-occasion-product', function(e) {
            deleteProductData = {
                productId: $(this).data('product-id'),
                occasionId: $(this).data('occasion-id'),
                productName: $(this).data('product-name')
            };

            // Update modal content
            $('#delete-product-name').text(deleteProductData.productName);
        });

        // Confirm delete button in modal
        $(document).on('click', '#confirmDeleteProductBtn', function(e) {
            e.preventDefault();

            if (!deleteProductData.productId) return;

            // Show loading
            LoadingOverlay.show({
                text: '{{ __("main.deleting") }}',
                subtext: '{{ __("main.please wait") }}'
            });

            // Send delete request
            $.ajax({
                url: `{{ route('admin.occasions.products.destroy', ['occasion' => $occasion->id, 'product' => ':product']) }}`.replace(':product', deleteProductData.productId),
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    LoadingOverlay.hide();
                    if (response.status) {
                        toastr.success(response.message || '{{ trans("catalogmanagement::occasion.product_deleted_successfully") }}');
                        // Close modal
                        bootstrap.Modal.getInstance(document.getElementById('modal-delete-product')).hide();
                        // Reload page after 1 second
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        toastr.error(response.message || '{{ trans("catalogmanagement::occasion.error_deleting_product") }}');
                    }
                },
                error: function(xhr) {
                    LoadingOverlay.hide();
                    const message = xhr.responseJSON?.message || '{{ trans("catalogmanagement::occasion.error_deleting_product") }}';
                    toastr.error(message);
                }
            });
        });
    });
</script>

{{-- Delete Product Modal --}}
<div class="modal fade" id="modal-delete-product" tabindex="-1" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="deleteProductModalLabel">
                    <i class="uil uil-trash-alt me-2"></i>{{ __('main.confirm delete') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning" role="alert">
                    <i class="uil uil-exclamation-triangle me-2"></i>
                    {{ __('main.are you sure you want to delete this') }} <strong id="delete-product-name">Product</strong>?
                </div>
                <p class="text-muted mb-0">{{ __('main.this action cannot be undone') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('main.cancel') }}</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteProductBtn">
                    <i class="uil uil-trash-alt me-2"></i>{{ __('main.delete') }}
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .draggable-row {
        transition: all 0.2s ease;
    }

    .draggable-row.dragging {
        background-color: #f0f0f0 !important;
        opacity: 0.5;
    }

    .draggable-row.drag-over {
        border-top: 3px solid #5f63f2 !important;
        background-color: #f8f9ff !important;
    }

    .drag-handle {
        cursor: move;
        user-select: none;
        transition: all 0.2s ease;
    }

    .drag-handle:hover {
        color: #5f63f2 !important;
        transform: scale(1.2);
    }

    .draggable-row:hover .drag-handle i {
        color: #5f63f2 !important;
    }
</style>
@endpush
