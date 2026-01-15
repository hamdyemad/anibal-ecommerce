@extends('layout.app')

@section('title', __('customer::customer.view_customer'))

@section('content')
    <div class="container-fluid mb-2">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    ['title' => __('customer::customer.customers_management'), 'url' => route('admin.customers.index')],
                    ['title' => __('customer::customer.view_customer')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ __('customer::customer.customer_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.customers.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ __('customer::customer.back_to_list') }}
                            </a>
                            @if($canManage)
                            @can('customers.edit')
                            <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-primary btn-sm">
                                <i class="uil uil-edit me-2"></i>{{ __('customer::customer.edit_customer') }}
                            </a>
                            @endcan
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Basic Information --}}
                            <div class="col-md-6">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i
                                                class="uil uil-info-circle me-1"></i>{{ __('customer::customer.basic_information') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Full Name --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('customer::customer.full_name') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        {{ $customer->full_name ?? '-' }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- First Name --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('customer::customer.first_name') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        {{ $customer->first_name ?? '-' }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Last Name --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('customer::customer.last_name') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        {{ $customer->last_name ?? '-' }}
                                                    </p>
                                                </div>
                                            </div>


                                            {{-- Phone --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('customer::customer.phone') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        {{ $customer->phone ?? '-' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Account Information --}}
                            <div class="col-md-6">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i
                                                class="uil uil-lock me-1"></i>{{ __('customer::customer.account_information') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Email --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('customer::customer.email') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        {{ $customer->email ?? '-' }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Status --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('customer::customer.status') }}</label>
                                                    <p class="fs-15">
                                                        @if ($customer->status)
                                                            <span
                                                                class="badge badge-success badge-round badge-lg">{{ __('customer::customer.active') }}</span>
                                                        @else
                                                            <span
                                                                class="badge badge-danger badge-round badge-lg">{{ __('customer::customer.inactive') }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Email Verified --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('customer::customer.email_verified') }}</label>
                                                    <p class="fs-15">
                                                        @if ($customer->email_verified_at)
                                                            <span
                                                                class="badge badge-success badge-round badge-lg">{{ __('customer::customer.verified') }}</span>
                                                        @else
                                                            <span
                                                                class="badge badge-warning badge-round badge-lg">{{ __('customer::customer.pending') }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Email Verified At --}}
                                            @if ($customer->email_verified_at)
                                                <div class="col-md-6">
                                                    <div class="view-item">
                                                        <label
                                                            class="il-gray fs-14 fw-500 mb-10">{{ __('customer::customer.email_verified_at') }}</label>
                                                        <p class="fs-15 color-dark">
                                                            {{ $customer->email_verified_at }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Timestamps Section --}}
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-clock me-1"></i>{{ __('common.timestamps') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('common.created_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $customer->created_at }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ trans('common.updated_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $customer->updated_at }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Addresses Section (Paginated DataTable) --}}
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-map-marker me-1"></i>{{ __('customer::customer.addresses') }}
                                        </h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="userDatatable global-shadow border-light-0 bg-white w-100">
                                            <div class="table-responsive">
                                                <table class="table mb-0 table-bordered table-hover" id="addresses-table">
                                                    <thead>
                                                        <tr class="userDatatable-header">
                                                            <th><span class="userDatatable-title">#</span></th>
                                                            <th><span class="userDatatable-title">{{ __('customer::customer.address_title') }}</span></th>
                                                            <th><span class="userDatatable-title">{{ __('customer::customer.address') }}</span></th>
                                                            <th><span class="userDatatable-title">{{ __('customer::customer.country') }}</span></th>
                                                            <th><span class="userDatatable-title">{{ __('customer::customer.city') }}</span></th>
                                                            <th><span class="userDatatable-title">{{ __('customer::customer.region') }}</span></th>
                                                            <th><span class="userDatatable-title">{{ __('customer::customer.sub_region') }}</span></th>
                                                            <th><span class="userDatatable-title">{{ __('customer::customer.is_primary') }}</span></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="addresses-tbody">
                                                        {{-- Data loaded via AJAX --}}
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div id="addresses-pagination" class="d-flex justify-content-between align-items-center p-3">
                                                {{-- Pagination loaded via AJAX --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Order Statistics Section --}}
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-chart-bar me-1"></i>{{ __('customer::customer.order_statistics') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Total Orders --}}
                                            <div class="col-md-4 mb-3">
                                                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #5b69ff15 0%, #5b69ff05 100%);">
                                                    <div class="card-body text-center py-4">
                                                        <div class="mb-2">
                                                            <i class="uil uil-shopping-cart fs-1" style="color: #5b69ff;"></i>
                                                        </div>
                                                        <h3 class="mb-1 fw-bold" style="color: #5b69ff;">{{ $orderStats['total_orders'] ?? 0 }}</h3>
                                                        <p class="mb-0 text-muted small">{{ __('customer::customer.total_orders') }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Total Spent --}}
                                            <div class="col-md-4 mb-3">
                                                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #20c99715 0%, #20c99705 100%);">
                                                    <div class="card-body text-center py-4">
                                                        <div class="mb-2">
                                                            <i class="uil uil-money-bill fs-1" style="color: #20c997;"></i>
                                                        </div>
                                                        <h3 class="mb-1 fw-bold" style="color: #20c997;">{{ number_format($orderStats['total_spent'] ?? 0, 2) }} {{ currency() }}</h3>
                                                        <p class="mb-0 text-muted small">{{ __('customer::customer.total_spent') }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Average Order Value --}}
                                            <div class="col-md-4 mb-3">
                                                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #6f42c115 0%, #6f42c105 100%);">
                                                    <div class="card-body text-center py-4">
                                                        <div class="mb-2">
                                                            <i class="uil uil-calculator fs-1" style="color: #6f42c1;"></i>
                                                        </div>
                                                        <h3 class="mb-1 fw-bold" style="color: #6f42c1;">{{ number_format($orderStats['average_order_value'] ?? 0, 2) }} {{ currency() }}</h3>
                                                        <p class="mb-0 text-muted small">{{ __('customer::customer.average_order_value') }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Dynamic Stage Stats --}}
                                            @foreach($orderStats['stages'] ?? [] as $stage)
                                            <div class="col-md-4 mb-3">
                                                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, {{ $stage['color'] }}15 0%, {{ $stage['color'] }}05 100%);">
                                                    <div class="card-body text-center py-4">
                                                        <div class="mb-2">
                                                            <span class="badge badge-round" style="background-color: {{ $stage['color'] }}; width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center;">
                                                                <i class="uil uil-tag fs-4 text-white"></i>
                                                            </span>
                                                        </div>
                                                        <h3 class="mb-1 fw-bold" style="color: {{ $stage['color'] }};">{{ $stage['count'] }}</h3>
                                                        <p class="mb-0 text-muted small">{{ $stage['name'] }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Orders Table Section --}}
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-shopping-bag me-1"></i>{{ __('customer::customer.customer_orders') }}
                                        </h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="userDatatable global-shadow border-light-0 bg-white w-100">
                                            <div class="table-responsive">
                                                <table class="table mb-0 table-bordered table-hover">
                                                    <thead>
                                                        <tr class="userDatatable-header">
                                                            <th><span class="userDatatable-title">#</span></th>
                                                            <th><span class="userDatatable-title">{{ __('customer::customer.order_number') }}</span></th>
                                                            <th><span class="userDatatable-title">{{ __('customer::customer.order_total') }}</span></th>
                                                            <th><span class="userDatatable-title">{{ __('customer::customer.order_status') }}</span></th>
                                                            <th><span class="userDatatable-title">{{ __('customer::customer.order_date') }}</span></th>
                                                            <th><span class="userDatatable-title">{{ __('common.actions') }}</span></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($orders ?? [] as $index => $order)
                                                            <tr>
                                                                <td>
                                                                    <div class="userDatatable-content">{{ $orders->firstItem() + $index }}</div>
                                                                </td>
                                                                <td>
                                                                    <div class="userDatatable-content fw-medium">#{{ $order->order_number ?? $order->id }}</div>
                                                                </td>
                                                                <td>
                                                                    <div class="userDatatable-content fw-bold text-success">
                                                                        @if(isset($isVendor) && $isVendor && isset($order->vendor_total))
                                                                            {{ number_format($order->vendor_total, 2) }} {{ currency() }}
                                                                        @else
                                                                            {{ number_format($order->total_price ?? 0, 2) }} {{ currency() }}
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="userDatatable-content">
                                                                        @if(isset($isVendor) && $isVendor && isset($order->vendor_stage))
                                                                            <span class="badge badge-round badge-lg" style="background-color: {{ $order->vendor_stage->color ?? '#6c757d' }}">
                                                                                {{ $order->vendor_stage->getTranslation('name', app()->getLocale()) }}
                                                                            </span>
                                                                        @elseif($order->stage)
                                                                            <span class="badge badge-round badge-lg" style="background-color: {{ $order->stage->color ?? '#6c757d' }}">
                                                                                {{ $order->stage->getTranslation('name', app()->getLocale()) }}
                                                                            </span>
                                                                        @else
                                                                            <span class="badge badge-round badge-lg bg-secondary">-</span>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="userDatatable-content">{{ $order->created_at ? $order->created_at : '-' }}</div>
                                                                </td>
                                                                <td>
                                                                    <div class="userDatatable-content d-flex justify-content-center">
                                                                        <a href="{{ route('admin.orders.show', $order->id) }}" target="_blank" class="btn btn-sm btn-primary">
                                                                            <i class="uil uil-eye m-0"></i>
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="6" class="text-center text-muted py-4">{{ __('customer::customer.no_orders_found') }}</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                            @if($orders && $orders->hasPages())
                                                <div class="d-flex justify-content-end p-3">
                                                    {{ $orders->links('vendor.pagination.custom') }}
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
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Addresses DataTable
    const addressesConfig = {
        url: '{{ route("admin.customers.addresses-datatable", $customer->id) }}',
        currentPage: 1,
        perPage: 10,
        total: 0,
        loading: false
    };

    function truncateStr(str, maxLength = 30) {
        if (!str) return '-';
        return str.length > maxLength ? str.substring(0, maxLength) + '...' : str;
    }

    function loadAddresses(page = 1) {
        if (addressesConfig.loading) return;
        addressesConfig.loading = true;
        addressesConfig.currentPage = page;

        const tbody = document.getElementById('addresses-tbody');
        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4"><i class="uil uil-spinner-alt fa-spin"></i> {{ __("common.loading") }}...</td></tr>';

        fetch(`${addressesConfig.url}?page=${page}&per_page=${addressesConfig.perPage}`)
            .then(response => response.json())
            .then(data => {
                addressesConfig.total = data.total || 0;
                renderAddressesTable(data.data || []);
                renderAddressesPagination(data);
                addressesConfig.loading = false;
            })
            .catch(error => {
                console.error('Error loading addresses:', error);
                tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger py-4">{{ __("common.error_loading_data") }}</td></tr>';
                addressesConfig.loading = false;
            });
    }

    function renderAddressesTable(addresses) {
        const tbody = document.getElementById('addresses-tbody');
        
        if (addresses.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">{{ __("customer::customer.no_addresses_found") }}</td></tr>';
            return;
        }

        tbody.innerHTML = addresses.map(address => `
            <tr>
                <td><div class="userDatatable-content">${address.index}</div></td>
                <td><div class="userDatatable-content" title="${address.title || ''}">${truncateStr(address.title, 20)}</div></td>
                <td><div class="userDatatable-content" title="${address.address || ''}">${truncateStr(address.address, 40)}</div></td>
                <td><div class="userDatatable-content">${address.country_name || '-'}</div></td>
                <td><div class="userDatatable-content">${address.city_name || '-'}</div></td>
                <td><div class="userDatatable-content">${address.region_name || '-'}</div></td>
                <td><div class="userDatatable-content">${address.subregion_name || '-'}</div></td>
                <td><div class="userDatatable-content">
                    ${address.is_primary 
                        ? '<span class="badge badge-primary badge-round badge-sm">{{ __("customer::customer.primary") }}</span>'
                        : '<span class="badge badge-light badge-round badge-sm">{{ __("customer::customer.non_primary") }}</span>'
                    }
                </div></td>
            </tr>
        `).join('');
    }

    function renderAddressesPagination(data) {
        const container = document.getElementById('addresses-pagination');
        const totalPages = Math.ceil(data.total / addressesConfig.perPage);
        
        if (totalPages <= 1) {
            container.innerHTML = `<span class="text-muted small">{{ __("common.showing") }} ${data.data?.length || 0} {{ __("common.of") }} ${data.total || 0}</span>`;
            return;
        }

        let paginationHtml = `<span class="text-muted small">{{ __("common.showing") }} ${data.data?.length || 0} {{ __("common.of") }} ${data.total || 0}</span>`;
        paginationHtml += '<nav><ul class="pagination pagination-sm mb-0">';
        
        // Previous button
        paginationHtml += `<li class="page-item ${addressesConfig.currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="loadAddresses(${addressesConfig.currentPage - 1})">&laquo;</a>
        </li>`;
        
        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= addressesConfig.currentPage - 2 && i <= addressesConfig.currentPage + 2)) {
                paginationHtml += `<li class="page-item ${i === addressesConfig.currentPage ? 'active' : ''}">
                    <a class="page-link" href="javascript:void(0)" onclick="loadAddresses(${i})">${i}</a>
                </li>`;
            } else if (i === addressesConfig.currentPage - 3 || i === addressesConfig.currentPage + 3) {
                paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }
        
        // Next button
        paginationHtml += `<li class="page-item ${addressesConfig.currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="loadAddresses(${addressesConfig.currentPage + 1})">&raquo;</a>
        </li>`;
        
        paginationHtml += '</ul></nav>';
        container.innerHTML = paginationHtml;
    }

    // Load addresses on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadAddresses(1);
    });
</script>
@endpush
