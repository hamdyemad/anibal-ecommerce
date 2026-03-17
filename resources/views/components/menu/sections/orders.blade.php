                                <a class="d-flex align-items-center justify-content-between fw-bold {{ request()->get('stage') == $stage->id ? 'active' : '' }}"
                                    href="{{ route('admin.orders.index', ['stage' => $stage->id]) }}">
                                    {{ $stage->translations->where('lang_key', 'name')->first()?->lang_value ?? $stage->slug }}
                                    <span class="badge badge-round ms-1"
                                        style="{{ getBadgeStyle(request()->get('stage') == $stage->id) }}">
                                        @php
                                            $stageOrderCount = 0;
                                            if (isAdmin()) {
                                                $stageOrderCount = \Modules\Order\app\Models\Order::where('stage_id', $stage->id)->count();
                                            } elseif ($stageVendor) {
                                                $stageOrderCount = \Modules\Order\app\Models\Order::where('stage_id', $stage->id)
                                                    ->whereHas('products', function($q) use ($stageVendor) {
                                                        $q->where('vendor_id', $stageVendor->id);
                                                    })->count();
                                            }
                                        @endphp
                                        {{ $stageOrderCount }}
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
        @endcanany

        @can('order-stages.index')
            @php
                // Define orderStages if not already defined (when orders.index permission is not granted)
                if (!isset($orderStages)) {
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
                }
            @endphp
            <li>
                <a href="{{ route('admin.order-stages.index') }}"
                    class="{{ isMenuActive('admin.order-stages.index', $currentRoute) ? 'active' : '' }}">
                    <span class="d-flex align-items-center justify-content-between fw-bold w-100">
                        <span class="d-flex align-items-center">
                            <span class="nav-icon uil uil-process"></span>
                            <span class="menu-text">{{ trans('menu.orders.order stages') }}</span>
                        </span>
                        <span class="badge badge-round ms-1"
                            style="{{ getBadgeStyle(isMenuActive('admin.order-stages.index', $currentRoute)) }}">{{ $orderStages->count() }}</span>
                    </span>
                </a>
            </li>
        @endcan

        @can('shippings.index')
            <li>
                <a href="{{ route('admin.shippings.index') }}"
                    class="{{ isMenuActive('admin.shippings.index', $currentRoute) ? 'active' : '' }}">
                    <span class="d-flex align-items-center justify-content-between fw-bold w-100">
                        <span class="d-flex align-items-center">
                            <span class="nav-icon uil uil-truck"></span>
                            <span class="menu-text">{{ trans('menu.orders.shipping methods') }}</span>
                        </span>
                        <span class="badge badge-round ms-1"
                            style="{{ getBadgeStyle(isMenuActive('admin.shippings.index', $currentRoute)) }}">{{ \Modules\Order\app\Models\Shipping::count() }}</span>
                    </span>
                </a>
            </li>
        @endcan

        {{-- Request Quotations - For Vendors --}}
        @if(isVendor())
            <li>
                <a href="{{ route('admin.vendor.request-quotations.index') }}"
                    class="{{ isMenuActive('admin.vendor.request-quotations.index', $currentRoute) ? 'active' : '' }}">
                    <span class="d-flex align-items-center justify-content-between fw-bold w-100">
                        <span class="d-flex align-items-center">
                            <span class="nav-icon uil uil-file-question-alt"></span>
                            <span class="menu-text">{{ trans('order::request-quotation.my_quotations') }}</span>
                        </span>
                        @php
                            $vendorQuotationsCount = 0;
                            $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
                            if ($vendor) {
                                // Count all quotations for this vendor (all statuses)
