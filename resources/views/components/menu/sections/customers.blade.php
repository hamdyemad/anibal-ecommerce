                <li class="menu-title mt-30">
                    <span>{{ trans('menu.sections.order and fulfillment') }}</span>
                </li>
                <li
                    class="has-child {{ isParentMenuOpen(['admin.orders.index', 'admin.orders.create'], ['admin/orders*']) ? 'open' : '' }}">
                    <a href="#"
                        class="{{ isParentMenuOpen(['admin.orders.index', 'admin.orders.create'], ['admin/orders*']) ? 'active' : '' }}">
                        <span class="nav-icon uil uil-shopping-cart"></span>
                        <span class="menu-text">{{ trans('menu.orders.title') }}</span>
                        <span class="toggle-icon"></span>
                    </a>
                    <ul class="px-0">
                        <li class="l_sidebar">
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.orders.index', $currentRoute) && !request()->has('stage') ? 'active' : '' }}"
                                href="{{ route('admin.orders.index') }}">
                                {{ trans('menu.orders.all') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive('admin.orders.index', $currentRoute) && !request()->has('stage')) }}">
                                    @php
                                        $allOrdersCount = 0;
                                        if (isAdmin()) {
                                            $allOrdersCount = \Modules\Order\app\Models\Order::count();
                                        } else {
                                            $orderVendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
                                            if ($orderVendor) {
                                                $allOrdersCount = \Modules\Order\app\Models\Order::whereHas('products', function($q) use ($orderVendor) {
                                                    $q->where('vendor_id', $orderVendor->id);
                                                })->count();
                                            }
                                        }
                                    @endphp
                                    {{ $allOrdersCount }}
                                </span>
                            </a>
                        </li>
                        @if(isAdmin())
                            @can('orders.create')
                                <li class="l_sidebar">
                                    <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.orders.create', $currentRoute) ? 'active' : '' }}"
                                        href="{{ route('admin.orders.create') }}">
                                        {{ trans('menu.orders.create') }}
                                    </a>
                                </li>
                            @endcan
                        @endif

                        {{-- Order Stages --}}
                        @php
                            $countryCode = session('country_code');
                            $countryId = $countryCode
                                ? \Modules\AreaSettings\app\Models\Country::where('code', $countryCode)->value('id')
                                : null;

                            $currentLocale = app()->getLocale();
                            $currentLangId = \App\Models\Language::where('code', $currentLocale)->value('id');

                            $orderStages = \Modules\Order\app\Models\OrderStage::withoutCountryFilter()
                            ->with(['translations' => function($query) use ($currentLangId) {
                                $query->where('lang_id', $currentLangId);
                            }])
                            ->where(function ($q) use ($countryId) {
                                $q->where(function ($sub) {
                                    $sub->whereNull('country_id')->where('is_system', 1);
                                });

                                if ($countryId) {
                                    $q->orWhere('country_id', $countryId);
                                }
                            })
                                ->orderBy('sort_order')
                                ->get();

                            // Get vendor for stage counts
                            $stageVendor = null;
                            if (!isAdmin()) {
                                $stageVendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
                            }
                        @endphp
                        @foreach ($orderStages as $stage)
                            <li class="l_sidebar">
