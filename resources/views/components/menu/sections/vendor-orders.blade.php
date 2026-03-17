                                $vendorQuotationsCount = \Modules\Order\app\Models\RequestQuotationVendor::where('vendor_id', $vendor->id)
                                    ->count();
                            }
                        @endphp
                        @if($vendorQuotationsCount > 0)
                            <span class="badge badge-round ms-1"
                                style="{{ getBadgeStyle(isMenuActive('admin.vendor.request-quotations.index', $currentRoute)) }}">{{ $vendorQuotationsCount }}</span>
                        @endif
                    </span>
                </a>
            </li>
        @endif

        <li
            class="has-child {{ isParentMenuOpen(['admin.refunds.index', 'admin.refunds.settings'], ['admin/refunds*']) ? 'open' : '' }}">
            <a href="#"
                class="{{ isParentMenuOpen(['admin.refunds.index', 'admin.refunds.settings'], ['admin/refunds*']) ? 'active' : '' }}">
                <span class="nav-icon uil uil-redo"></span>
                <span class="menu-text">{{ trans('menu.refunds.title') }}</span>
                <span class="toggle-icon"></span>
            </a>
            <ul class="px-0">
                {{-- All Refunds --}}
                <li class="l_sidebar">
                    <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.refunds.index', $currentRoute) && !request()->has('status') ? 'active' : '' }}"
                        href="{{ route('admin.refunds.index') }}">
                        {{ trans('menu.refunds.all') }}
                        <span class="badge badge-round ms-1"
                            style="{{ getBadgeStyle(isMenuActive('admin.refunds.index', $currentRoute) && !request()->has('status')) }}">
                            @php
                                $allRefundsCount = 0;
                                try {
                                    if (isAdmin()) {
                                        $allRefundsCount = \Modules\Refund\app\Models\RefundRequest::count();
                                    } else {
                                        $refundVendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
                                        if ($refundVendor) {
                                            $allRefundsCount = \Modules\Refund\app\Models\RefundRequest::where('vendor_id', $refundVendor->id)->count();
                                        }
                                    }
                                } catch (\Exception $e) {
                                    $allRefundsCount = 0;
                                }
                            @endphp
                            {{ $allRefundsCount }}
                        </span>
                    </a>
                </li>

                {{-- Dynamic Status Menu Items --}}
                @php
                    $refundStatuses = \Modules\Refund\app\Models\RefundRequest::STATUSES;
                @endphp

                @foreach($refundStatuses as $statusKey => $statusLabel)
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ request()->get('status') == $statusKey ? 'active' : '' }}"
                            href="{{ route('admin.refunds.index', ['status' => $statusKey]) }}">
                            {{ trans('refund::refund.statuses.' . $statusKey) }}
                            <span class="badge badge-round ms-1"
                                style="{{ getBadgeStyle(request()->get('status') == $statusKey, $statusKey == 'rejected' ? 'secondary' : 'primary') }}">
                                @php
                                    $statusCount = 0;
                                    try {
                                        if (isAdmin()) {
                                            $statusCount = \Modules\Refund\app\Models\RefundRequest::where('status', $statusKey)->count();
                                        } else {
                                            $refundVendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
                                            if ($refundVendor) {
                                                $statusCount = \Modules\Refund\app\Models\RefundRequest::where('vendor_id', $refundVendor->id)->where('status', $statusKey)->count();
                                            }
                                        }
                                    } catch (\Exception $e) {
                                        $statusCount = 0;
                                    }
                                @endphp
                                {{ $statusCount }}
                            </span>
                        </a>
                    </li>
                @endforeach

                {{-- Settings (Admin Only) --}}
                @if(isAdmin())
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.refunds.admin-settings.index', $currentRoute) ? 'active' : '' }}"
                            href="{{ route('admin.refunds.admin-settings.index') }}">
                            {{ trans('menu.refunds.admin_settings') }}
                        </a>
                    </li>
                @endif
                
                {{-- Vendor Settings --}}
                @if(!isAdmin())
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.refunds.settings', $currentRoute) ? 'active' : '' }}"
                            href="{{ route('admin.refunds.settings') }}">
                            {{ trans('menu.refunds.settings') }}
                        </a>
                    </li>
                @endif
            </ul>
        </li>

        @canany(['blog-categories.index', 'blogs.index'])
