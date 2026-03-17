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
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.vendor-reviews.index', $currentRoute) && request()->query('status') === 'approved' ? 'active' : '' }}"
                            href="{{ route('admin.vendor-reviews.index', ['status' => 'approved']) }}">
                            {{ trans('menu.vendor_reviews.accepted') }}
                            <span class="badge badge-round ms-1"
                                style="{{ getBadgeStyle(isMenuActive('admin.vendor-reviews.index', $currentRoute) && request()->query('status') === 'approved') }}">{{ $vendor_reviews_accepted }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.vendor-reviews.index', $currentRoute) && request()->query('status') === 'rejected' ? 'active' : '' }}"
                            href="{{ route('admin.vendor-reviews.index', ['status' => 'rejected']) }}">
                            {{ trans('menu.vendor_reviews.rejected') }}
                            <span class="badge badge-round ms-1"
                                style="{{ getBadgeStyle(isMenuActive('admin.vendor-reviews.index', $currentRoute) && request()->query('status') === 'rejected', 'secondary') }}">{{ $vendor_reviews_rejected }}</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endcan


        @can('vendor-requests.index')
            <li
                class="has-child {{ Request::is(LaravelLocalization::getCurrentLocale() . '/admin/vendor-requests*') ? 'open' : '' }}">
                <a href="#"
                    class="{{ Request::is(LaravelLocalization::getCurrentLocale() . '/admin/vendor-requests*') ? 'active' : '' }}">
                    <span class="nav-icon uil uil-clipboard-notes"></span>
                    <span class="menu-text">{{ trans('menu.become a vendor requests.title') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.vendor-requests.index', $currentRoute) && !request()->has('status') ? 'active' : '' }}"
                            href="{{ route('admin.vendor-requests.index') }}">
                            {{ trans('common.all') }}
                            <span class="badge badge-round ms-1"
                                style="{{ getBadgeStyle(isMenuActive('admin.vendor-requests.index', $currentRoute) && !request()->has('status')) }}">{{ \Modules\Vendor\app\Models\VendorRequest::count() }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.vendor-requests.index', $currentRoute) && !request()->has('status') ? 'active' : '' }}"
                            href="{{ route('admin.vendor-requests.index') }}">
                            {{ trans('menu.become a vendor requests.new') }}
                            <span class="badge badge-round ms-1"
                                style="{{ getBadgeStyle(isMenuActive('admin.vendor-requests.index', $currentRoute) && !request()->has('status')) }}">{{ \Modules\Vendor\app\Models\VendorRequest::pending()->count() }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ request()->get('status') === 'approved' ? 'active' : '' }}"
                            href="{{ route('admin.vendor-requests.index') }}?status=approved">
                            {{ trans('menu.become a vendor requests.accepted') }}
                            <span class="badge badge-round ms-1"
                                style="{{ getBadgeStyle(request()->get('status') === 'approved') }}">{{ \Modules\Vendor\app\Models\VendorRequest::approved()->count() }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ request()->get('status') === 'rejected' ? 'active' : '' }}"
                            href="{{ route('admin.vendor-requests.index') }}?status=rejected">
                            {{ trans('menu.become a vendor requests.rejected') }}
                            <span class="badge badge-round ms-1"
                                style="{{ getBadgeStyle(request()->get('status') === 'rejected', 'secondary') }}">{{ \Modules\Vendor\app\Models\VendorRequest::rejected()->count() }}</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endcan

        @if(isAdmin())
            @can('customers.index')
                <li
                    class="has-child {{ Request::is(LaravelLocalization::getCurrentLocale() . '/admin/customers*') ? 'open' : '' }}">
                    <a href="#"
                        class="{{ Request::is(LaravelLocalization::getCurrentLocale() . '/admin/customers*') ? 'active' : '' }}">
                        <span class="nav-icon uil uil-user-circle"></span>
                        <span class="menu-text">{{ trans('menu.customers.title') }}</span>
                        <span class="toggle-icon"></span>
                    </a>
                    <ul class="px-0">
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.customers.index', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.customers.index') }}">
                                {{ trans('menu.customers.all') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive('admin.customers.index', $currentRoute)) }}">
                                    @php
                                        $customerCount = \Modules\Customer\app\Models\Customer::count();
                                    @endphp
                                    {{ $customerCount }}
                                </span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan
        @endif

        @canany(['orders.index', 'orders.create'])
