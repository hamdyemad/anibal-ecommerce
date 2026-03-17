                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.vendors.create', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.vendors.create') }}">
                                {{ trans('menu.vendors.create') }}
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @can('vendor-reviews.index')
            @php
                try {
                    $vendor_reviews_all = Modules\CatalogManagement\app\Models\Review::where('reviewable_type', Modules\Vendor\app\Models\Vendor::class)->count();
                    $vendor_reviews_accepted = Modules\CatalogManagement\app\Models\Review::where('reviewable_type', Modules\Vendor\app\Models\Vendor::class)->where('status', 'approved')->count();
                    $vendor_reviews_rejected = Modules\CatalogManagement\app\Models\Review::where('reviewable_type', Modules\Vendor\app\Models\Vendor::class)->where('status', 'rejected')->count();
                } catch (\Exception $e) {
                    $vendor_reviews_all = $vendor_reviews_accepted = $vendor_reviews_rejected = 0;
                }
            @endphp
            <li class="has-child {{ isParentMenuOpen(['admin.vendor-reviews.index'], ['admin/vendor-reviews*']) ? 'open' : '' }}">
                <a href="#"
                    class="{{ isParentMenuOpen(['admin.vendor-reviews.index'], ['admin/vendor-reviews*']) ? 'active' : '' }}">
                    <span class="nav-icon uil uil-star"></span>
                    <span class="menu-text">{{ trans('menu.vendor_reviews.title') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.vendor-reviews.index', $currentRoute) && request()->query('status') === 'all' ? 'active' : '' }}"
                            href="{{ route('admin.vendor-reviews.index', ['status' => 'all']) }}">
                            {{ trans('menu.vendor_reviews.all') }}
                            <span class="badge badge-round ms-1"
                                style="{{ getBadgeStyle(isMenuActive('admin.vendor-reviews.index', $currentRoute) && request()->query('status') === 'all') }}">{{ $vendor_reviews_all }}</span>
