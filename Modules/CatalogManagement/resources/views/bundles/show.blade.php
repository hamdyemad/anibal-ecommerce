@extends('layout.app')
@section('title', $bundle->getTranslation('name', app()->getLocale()))

@push('styles')
<style>
    .info-section {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 2rem;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid #e9ecef;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #495057;
        min-width: 200px;
    }

    .info-value {
        color: #212529;
        flex: 1;
    }

    .badge-group {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .language-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        background: #e3f2fd;
        color: #1565c0;
        border-radius: 4px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .nav-tabs .nav-link {
        color: #6c757d;
        border: none;
        border-bottom: 2px solid transparent;
        padding: 0.75rem 1.5rem;
    }

    .nav-tabs .nav-link.active {
        color: #007bff;
        border-bottom-color: #007bff;
        background: none;
    }

    .tab-content {
        background: white;
        padding: 2rem;
        border-radius: 8px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ $bundle->getTranslation('name', app()->getLocale()) }}</h5>
                    <div class="btn-group" role="group">
                        <a href="{{ route('bundles.edit', ['lang' => app()->getLocale(), 'countryCode' => session('country_code'), 'id' => $bundle->id]) }}"
                            class="btn btn-warning btn-sm">
                            <i class="uil uil-edit"></i> Edit
                        </a>
                        <a href="{{ route('bundles.index', ['lang' => app()->getLocale(), 'countryCode' => session('country_code')]) }}"
                            class="btn btn-secondary btn-sm">
                            <i class="uil uil-arrow-left"></i> Back
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Basic Information -->
                    <div class="info-section">
                        <h6 class="mb-3">{{ trans('catalogmanagement::bundle.basic_information') }}</h6>

                        <div class="info-row">
                            <span class="info-label">SKU:</span>
                            <span class="info-value">{{ $bundle->sku }}</span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">{{ trans('catalogmanagement::bundle.vendor') }}:</span>
                            <span class="info-value">{{ $bundle->vendor->getTranslation('name', app()->getLocale()) ?? 'N/A' }}</span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">{{ trans('catalogmanagement::bundle.category') }}:</span>
                            <span class="info-value">{{ $bundle->bundleCategory->getTranslation('name', app()->getLocale()) ?? 'N/A' }}</span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">{{ trans('catalogmanagement::bundle.status') }}:</span>
                            <span class="info-value">
                                @if($bundle->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">{{ trans('catalogmanagement::bundle.created_at') }}:</span>
                            <span class="info-value">{{ $bundle->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                    </div>

                    <!-- Translations -->
                    <div class="info-section">
                        <h6 class="mb-3">{{ trans('catalogmanagement::bundle.translations') }}</h6>

                        <ul class="nav nav-tabs" role="tablist">
                            @foreach($languages as $index => $language)
                                <li class="nav-item">
                                    <a class="nav-link {{ $index === 0 ? 'active' : '' }}"
                                        data-bs-toggle="tab" href="#lang-{{ $language->code }}">
                                        {{ strtoupper($language->code) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                        <div class="tab-content">
                            @foreach($languages as $index => $language)
                                <div id="lang-{{ $language->code }}" class="tab-pane {{ $index === 0 ? 'active' : '' }}">
                                    <div class="info-row">
                                        <span class="info-label">{{ trans('catalogmanagement::bundle.name') }}:</span>
                                        <span class="info-value">{{ $bundle->getTranslation('name', $language->code) ?? 'N/A' }}</span>
                                    </div>

                                    <div class="info-row">
                                        <span class="info-label">{{ trans('catalogmanagement::bundle.description') }}:</span>
                                        <span class="info-value">
                                            {!! nl2br($bundle->getTranslation('description', $language->code) ?? 'N/A') !!}
                                        </span>
                                    </div>

                                    <hr class="my-3">

                                    <h6 class="mb-3">{{ trans('catalogmanagement::bundle.seo_information') }}</h6>

                                    <div class="info-row">
                                        <span class="info-label">{{ trans('catalogmanagement::bundle.seo_title') }}:</span>
                                        <span class="info-value">{{ $bundle->getTranslation('seo_title', $language->code) ?? 'N/A' }}</span>
                                    </div>

                                    <div class="info-row">
                                        <span class="info-label">{{ trans('catalogmanagement::bundle.seo_description') }}:</span>
                                        <span class="info-value">{{ $bundle->getTranslation('seo_description', $language->code) ?? 'N/A' }}</span>
                                    </div>

                                    <div class="info-row">
                                        <span class="info-label">{{ trans('catalogmanagement::bundle.seo_keywords') }}:</span>
                                        <span class="info-value">
                                            @php
                                                $keywords = $bundle->getTranslation('seo_keywords', $language->code);
                                                if ($keywords) {
                                                    $keywordArray = array_filter(array_map('trim', explode(',', $keywords)));
                                                    if (!empty($keywordArray)) {
                                                        echo '<div class="badge-group">';
                                                        foreach ($keywordArray as $keyword) {
                                                            echo '<span class="badge bg-info">' . htmlspecialchars($keyword) . '</span>';
                                                        }
                                                        echo '</div>';
                                                    } else {
                                                        echo 'N/A';
                                                    }
                                                } else {
                                                    echo 'N/A';
                                                }
                                            @endphp
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Bundle Products -->
                    <div class="info-section">
                        <h6 class="mb-3">{{ trans('catalogmanagement::bundle.bundle_products') }}</h6>

                        @if($bundle->bundleProducts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Product Variant</th>
                                            <th>Price</th>
                                            <th>Min Quantity</th>
                                            <th>Limitation Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($bundle->bundleProducts as $product)
                                            <tr>
                                                <td>{{ $product->vendorProductVariant->product->getTranslation('name', app()->getLocale()) ?? 'N/A' }}</td>
                                                <td>{{ number_format($product->price, 2) }}</td>
                                                <td>{{ $product->min_quantity }}</td>
                                                <td>{{ $product->limitation_quantity ?? 'Unlimited' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">No products added to this bundle yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
