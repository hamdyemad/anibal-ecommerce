            'products.index', 'products.bank', 'products.vendor-bank',
            'variant-keys.index', 'variant-keys.create',
            'variants-configurations.index', 'variants-configurations.create',
        ])
            <li
                class="has-child {{ isParentMenuOpen(['admin.products.index', 'admin.products.pending', 'admin.products.rejected', 'admin.products.accepted', 'admin.products.create', 'admin.products.show', 'admin.products.edit', 'admin.products.bank', 'admin.products.vendor-bank', 'admin.variant-keys.index', 'admin.variants-configurations.index'], ['admin/products*', 'admin/variant*']) ? 'open' : '' }}">
                <a href="#"
                    class="{{ isParentMenuOpen(['admin.products.index', 'admin.products.pending', 'admin.products.rejected', 'admin.products.accepted', 'admin.products.create', 'admin.products.show', 'admin.products.edit', 'admin.products.bank', 'admin.products.vendor-bank', 'admin.variant-keys.index', 'admin.variants-configurations.index'], ['admin/products*', 'admin/variant*']) ? 'active' : '' }}">
                    <span class="nav-icon uil uil-box"></span>
                    <span class="menu-text">{{ trans('menu.products.title') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    @can('products.bank')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.products.bank', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.products.bank') }}">
                                {{ trans('menu.products.bank_products') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive('admin.products.bank', $currentRoute)) }}">
                                    @php
                                        $bankProductsQuery = \Modules\CatalogManagement\app\Models\Product::where('type', 'bank');
                                        
                                        // Filter by vendor's departments if user is a vendor
                                        if (isVendor()) {
                                            $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById ?? auth()->user()->vendor;
                                            if ($vendor) {
                                                $departmentIds = $vendor->departments()->pluck('departments.id')->toArray();
                                                if (!empty($departmentIds)) {
                                                    $bankProductsQuery->whereIn('department_id', $departmentIds);
                                                } else {
                                                    $bankProductsQuery->whereRaw('1 = 0');
                                                }
                                            }
                                        }
                                        
                                        $bankProductsCount = $bankProductsQuery->count();
                                    @endphp
                                    {{ $bankProductsCount }}
                                </span>
                            </a>
                        </li>
                    @endcan

                    {{-- Vendor Bank Products - Only for vendors --}}
                    @if(isVendor())
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.products.vendor-bank', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.products.vendor-bank') }}">
                                {{ trans('menu.products.vendor_bank_products') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive('admin.products.vendor-bank', $currentRoute)) }}">
                                    @php
                                        $vendorBankProductsCount = 0;
                                        $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById ?? auth()->user()->vendor;
                                        
                                        if ($vendor) {
                                            // Count bank products that belong to this vendor through vendor_products table
                                            $vendorBankProductsCount = \Modules\CatalogManagement\app\Models\Product::where('type', 'bank')
                                                ->whereHas('vendors', function($query) use ($vendor) {
                                                    $query->where('vendor_products.vendor_id', $vendor->id)
                                                          ->whereNull('vendor_products.deleted_at');
                                                })
                                                ->count();
                                        }
                                    @endphp
                                    {{ $vendorBankProductsCount }}
                                </span>
                            </a>
                        </li>
                    @endif

                    @can('products.index')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.products.index', 'admin.products.create', 'admin.products.show', 'admin.products.edit'], $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.products.index') }}">
                                {{ trans('menu.products.all_products') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive(['admin.products.index', 'admin.products.create', 'admin.products.show', 'admin.products.edit'], $currentRoute)) }}">
                                    @if (in_array($user_type_id, \App\Models\UserType::adminIds()))
                                        {{ \Modules\CatalogManagement\app\Models\Product::count() }}
                                    @else
                                        @php
                                            // For vendors, exclude bank products from count
                                            $vendorProductsCount = \Modules\CatalogManagement\app\Models\VendorProduct::where('vendor_id', auth()->user()->vendor->id ?? 0)
                                                ->whereHas('product', function($q) {
                                                    $q->where('type', '!=', 'bank');
                                                })
                                                ->count();
                                        @endphp
                                        {{ $vendorProductsCount }}
                                    @endif
                                </span>
                            </a>
                        </li>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.products.pending', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.products.pending') }}">
                                {{ trans('menu.products.pending_products') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive('admin.products.pending', $currentRoute)) }}">
                                    @if (in_array($user_type_id, \App\Models\UserType::adminIds()))
                                        {{ \Modules\CatalogManagement\app\Models\VendorProduct::where('status', 'pending')->count() }}
                                    @else
                                        @php
                                            // For vendors, exclude bank products from pending count
                                            $vendorPendingCount = \Modules\CatalogManagement\app\Models\VendorProduct::where('status', 'pending')
                                                ->where('vendor_id', auth()->user()->vendor->id ?? 0)
                                                ->whereHas('product', function($q) {
                                                    $q->where('type', '!=', 'bank');
                                                })
                                                ->count();
                                        @endphp
                                        {{ $vendorPendingCount }}
                                    @endif
                                </span>
                            </a>
                        </li>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.products.rejected', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.products.rejected') }}">
                                {{ trans('menu.products.rejected_products') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive('admin.products.rejected', $currentRoute), 'secondary') }}">
                                    @if (in_array($user_type_id, \App\Models\UserType::adminIds()))
                                        {{ \Modules\CatalogManagement\app\Models\VendorProduct::where('status', 'rejected')->count() }}
                                    @else
                                        @php
                                            // For vendors, exclude bank products from rejected count
                                            $vendorRejectedCount = \Modules\CatalogManagement\app\Models\VendorProduct::where('status', 'rejected')
                                                ->where('vendor_id', auth()->user()->vendor->id ?? 0)
                                                ->whereHas('product', function($q) {
                                                    $q->where('type', '!=', 'bank');
                                                })
                                                ->count();
                                        @endphp
                                        {{ $vendorRejectedCount }}
                                    @endif
                                </span>
                            </a>
                        </li>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.products.accepted', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.products.accepted') }}">
                                {{ trans('menu.products.accepted_products') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive('admin.products.accepted', $currentRoute)) }}">
                                    @if (isAdmin())
                                        {{ \Modules\CatalogManagement\app\Models\VendorProduct::where('status', 'approved')->count() }}
                                    @else
                                        @php
                                            // For vendors, exclude bank products from approved count
                                            $vendorApprovedCount = \Modules\CatalogManagement\app\Models\VendorProduct::where('status', 'approved')
                                                ->where('vendor_id', auth()->user()->vendor->id ?? 0)
                                                ->whereHas('product', function($q) {
                                                    $q->where('type', '!=', 'bank');
                                                })
                                                ->count();
                                        @endphp
                                        {{ $vendorApprovedCount }}
                                    @endif
                                </span>
                            </a>
                        </li>

                    @endcan

                    @canany(['variant-keys.index', 'variant-keys.create'])
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.variant-keys.index', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.variant-keys.index') }}">
                                {{ trans('menu.variant configurations.variant config keys') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive('admin.variant-keys.index', $currentRoute)) }}">{{ \Modules\CatalogManagement\app\Models\VariantConfigurationKey::count() }}</span>
                            </a>
                        </li>
                    @endcanany
                    @canany(['variants-configurations.index', 'variants-configurations.create'])
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.variants-configurations.index', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.variants-configurations.index') }}">
                                {{ trans('menu.variant configurations.variant config') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive('admin.variants-configurations.index', $currentRoute)) }}">{{ \Modules\CatalogManagement\app\Models\VariantsConfiguration::count() }}</span>
                            </a>
                        </li>
                    @endcanany
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.system-catalog.index', $currentRoute) ? 'active' : '' }}"
                            href="{{ route('admin.system-catalog.index') }}">
                            {{ trans('menu.system_catalog.title') }}
                        </a>
                    </li>
                </ul>
            </li>
        @endcanany


        {{-- Bundles Menu --}}
        @canany(['bundle-categories.index', 'bundles.index'])
            <li
                class="has-child {{ isParentMenuOpen(['admin.bundle-categories.index', 'admin.bundle-categories.create', 'admin.bundle-categories.show', 'admin.bundle-categories.edit', 'admin.bundles.index', 'admin.bundles.create', 'admin.bundles.show', 'admin.bundles.edit'], ['admin/bundle-categories*', 'admin/bundles*']) ? 'open' : '' }}">
                <a href="#"
                    class="{{ isParentMenuOpen(['admin.bundle-categories.index', 'admin.bundle-categories.create', 'admin.bundle-categories.show', 'admin.bundle-categories.edit', 'admin.bundles.index', 'admin.bundles.create', 'admin.bundles.show', 'admin.bundles.edit'], ['admin/bundle-categories*', 'admin/bundles*']) ? 'active' : '' }}">
                    <span class="nav-icon uil uil-package"></span>
                    <span class="menu-text">{{ trans('menu.bundles.title') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    @can('bundle-categories.index')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.bundle-categories.index', 'admin.bundle-categories.create', 'admin.bundle-categories.show', 'admin.bundle-categories.edit'], $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.bundle-categories.index') }}">
                                {{ trans('menu.bundles.bundle_categories') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive(['admin.bundle-categories.index', 'admin.bundle-categories.create', 'admin.bundle-categories.show', 'admin.bundle-categories.edit'], $currentRoute)) }}">
                                    {{ \Modules\CatalogManagement\app\Models\BundleCategory::count() }}
                                </span>
                            </a>
                        </li>
                    @endcan
                    @can('bundles.index')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.bundles.index', 'admin.bundles.create', 'admin.bundles.show', 'admin.bundles.edit'], $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.bundles.index') }}">
                                {{ trans('menu.bundles.all_bundles') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive(['admin.bundles.index', 'admin.bundles.create', 'admin.bundles.show', 'admin.bundles.edit'], $currentRoute)) }}">
                                    @if (in_array($user_type_id, \App\Models\UserType::adminIds()))
                                        {{ \Modules\CatalogManagement\app\Models\Bundle::count() }}
                                    @else
                                        {{ \Modules\CatalogManagement\app\Models\Bundle::where('vendor_id', auth()->user()->vendor->id ?? 0)->count() }}
                                    @endif
                                </span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        {{-- Taxes Menu --}}
        @can('taxes.index')
            <li
                class="{{ isMenuActive(['admin.taxes.index', 'admin.taxes.create', 'admin.taxes.show', 'admin.taxes.edit'], $currentRoute) ? 'active' : '' }}">
                <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.taxes.index', 'admin.taxes.create', 'admin.taxes.show', 'admin.taxes.edit'], $currentRoute) ? 'active' : '' }}"
                    href="{{ route('admin.taxes.index') }}">
                    <span class="d-flex align-items-center">
                        <span class="nav-icon uil uil-bill"></span>
                        <span class="menu-text">{{ trans('menu.taxes.title') }}</span>
                    </span>
                    <span class="badge badge-round ms-1"
                        style="{{ getBadgeStyle(isMenuActive(['admin.taxes.index', 'admin.taxes.create', 'admin.taxes.show', 'admin.taxes.edit'], $currentRoute)) }}">
                        {{ \Modules\CatalogManagement\app\Models\Tax::count() }}
                    </span>
                </a>
            </li>
        @endcan

        {{-- Occasions Menu (Admin Only) --}}
        @if(isAdmin())
        @can('occasions.index')
            <li
                class="{{ isMenuActive(['admin.occasions.index', 'admin.occasions.create', 'admin.occasions.show', 'admin.occasions.edit'], $currentRoute) ? 'active' : '' }}">
                <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.occasions.index', 'admin.occasions.create', 'admin.occasions.show', 'admin.occasions.edit'], $currentRoute) ? 'active' : '' }}"
                    href="{{ route('admin.occasions.index') }}">
                    <span class="d-flex align-items-center">
                        <span class="nav-icon uil uil-calendar-alt"></span>
                        <span class="menu-text">{{ trans('menu.occasions') }}</span>
                    </span>
                    <span class="badge badge-round ms-1"
                        style="{{ getBadgeStyle(isMenuActive(['admin.occasions.index', 'admin.occasions.create', 'admin.occasions.show', 'admin.occasions.edit'], $currentRoute)) }}">
                        @php
                            $occasions_count = \Modules\CatalogManagement\app\Models\Occasion::count();
                        @endphp
                        {{ $occasions_count }}
                    </span>
                </a>
            </li>
        @endcan
        @endif
        @can('product-reviews.index')
            @php
                try {
                    $reviews_all = Modules\CatalogManagement\app\Models\Review::where('reviewable_type', Modules\CatalogManagement\app\Models\VendorProduct::class)->count();
                    $reviews_accepted = Modules\CatalogManagement\app\Models\Review::where('reviewable_type', Modules\CatalogManagement\app\Models\VendorProduct::class)->where(
                        'status',
                        'approved',
                    )->count();
                    $reviews_rejected = Modules\CatalogManagement\app\Models\Review::where('reviewable_type', Modules\CatalogManagement\app\Models\VendorProduct::class)->where(
                        'status',
                        'rejected',
                    )->count();
                } catch (\Exception $e) {
                    $reviews_all = $reviews_accepted = $reviews_rejected = 0;
                }
            @endphp
            <li class="has-child {{ isParentMenuOpen(['admin.reviews.index'], ['admin/reviews*']) ? 'open' : '' }}">
                <a href="#"
                    class="{{ isParentMenuOpen(['admin.reviews.index'], ['admin/reviews*']) ? 'active' : '' }}">
                    <span class="nav-icon uil uil-star"></span>
                    <span class="menu-text">{{ trans('menu.product_reviews.title') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.reviews.index', $currentRoute) && request()->query('status') === 'all' ? 'active' : '' }}"
                            href="{{ route('admin.reviews.index', ['status' => 'all']) }}">
                            {{ trans('menu.product_reviews.all') }}
                            <span class="badge badge-round ms-1"
                                style="{{ getBadgeStyle(isMenuActive('admin.reviews.index', $currentRoute) && request()->query('status') === 'all') }}">{{ $reviews_all }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.reviews.index', $currentRoute) && request()->query('status') === 'approved' ? 'active' : '' }}"
                            href="{{ route('admin.reviews.index', ['status' => 'approved']) }}">
                            {{ trans('menu.product_reviews.accepted') }}
                            <span class="badge badge-round ms-1"
                                style="{{ getBadgeStyle(isMenuActive('admin.reviews.index', $currentRoute) && request()->query('status') === 'approved') }}">{{ $reviews_accepted }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.reviews.index', $currentRoute) && request()->query('status') === 'rejected' ? 'active' : '' }}"
                            href="{{ route('admin.reviews.index', ['status' => 'rejected']) }}">
                            {{ trans('menu.product_reviews.rejected') }}
                            <span class="badge badge-round ms-1"
                                style="{{ getBadgeStyle(isMenuActive('admin.reviews.index', $currentRoute) && request()->query('status') === 'rejected', 'secondary') }}">{{ $reviews_rejected }}</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endcan

        @can('brands.index')
            <li>
                <a href="{{ route('admin.brands.index') }}"
                    class="{{ isMenuActive(['admin.brands.index', 'admin.brands.create', 'admin.brands.show', 'admin.brands.edit'], $currentRoute) ? 'active' : '' }}">
                    <span class="d-flex align-items-center justify-content-between fw-bold w-100">
                        <span class="d-flex align-items-center">
                            <span class="nav-icon uil uil-ticket"></span>
                            <span class="menu-text fw-bold">{{ trans('menu.brands.title') }}</span>
                        </span>
                        <span class="badge badge-round ms-1"
                            style="{{ getBadgeStyle(isMenuActive(['admin.brands.index', 'admin.brands.create', 'admin.brands.show', 'admin.brands.edit'], $currentRoute)) }}">{{ \Modules\CatalogManagement\app\Models\Brand::count() }}</span>
                    </span>
                </a>
            </li>
        @endcan

        @can('promocodes.index')
            <li>
                <a href="{{ route('admin.promocodes.index') }}"
                    class="{{ isMenuActive(['admin.promocodes.index', 'admin.promocodes.create', 'admin.promocodes.edit', 'admin.promocodes.show'], $currentRoute) ? 'active' : '' }}">
                    <span class="d-flex align-items-center justify-content-between fw-bold w-100">
                        <span class="d-flex align-items-center">
                            <span class="nav-icon uil uil-ticket"></span>
                            <span class="menu-text">{{ trans('menu.promocodes.title') }}</span>
                        </span>
                        <span class="badge badge-round ms-1"
                            style="{{ getBadgeStyle(isMenuActive(['admin.promocodes.index', 'admin.promocodes.create', 'admin.promocodes.edit', 'admin.promocodes.show'], $currentRoute)) }}">{{ \Modules\CatalogManagement\app\Models\Promocode::count() }}</span>
                    </span>
                </a>
            </li>
        @endcan

        @canany(['points-settings.index', 'points-settings.user-points.index'])
