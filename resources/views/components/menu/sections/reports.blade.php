                            href="{{ route('admin.reports.registered-users') }}">
                            {{ trans('menu.reports.registerd users') }}
                        </a>
                    </li>
                    @endcan
                    <li class="has-child">
                        <a href="#" class="d-flex align-items-center justify-content-between fw-bold">
                            {{ trans('menu.reports.financial reports') }}
                            <span class="toggle-icon"></span>
                        </a>
                        <ul class="px-0">
                            <li>
                                <a href="{{ route('admin.reports.profitability') }}"
                                    class="{{ isMenuActive('admin.reports.profitability', $currentRoute) ? 'active' : '' }}">
                                    <span class="fw-bold">{{ __('report::report.profitability_report') }}</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.reports.sales-analysis') }}"
                                    class="{{ isMenuActive('admin.reports.sales-analysis', $currentRoute) ? 'active' : '' }}">
                                    <span class="fw-bold">{{ __('report::report.sales_analysis') }}</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.reports.product-performance') }}"
                                    class="{{ isMenuActive('admin.reports.product-performance', $currentRoute) ? 'active' : '' }}">
                                    <span class="fw-bold">{{ __('report::report.product_performance') }}</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.reports.customer-analysis') }}"
                                    class="{{ isMenuActive('admin.reports.customer-analysis', $currentRoute) ? 'active' : '' }}">
                                    <span class="fw-bold">{{ __('report::report.customer_analysis') }}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    @can('reports.area_users.view')
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.reports.area-users', $currentRoute) ? 'active' : '' }}"
                            href="{{ route('admin.reports.area-users') }}">
                            {{ trans('menu.reports.area users') }}
                        </a>
                    </li>
                    @endcan
                    @can('reports.orders.view')
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.reports.orders', $currentRoute) ? 'active' : '' }}"
                            href="{{ route('admin.reports.orders') }}">
                            {{ trans('menu.reports.orders report') }}
                        </a>
                    </li>
                    @endcan
                    @can('reports.products.view')
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.reports.products', $currentRoute) ? 'active' : '' }}"
                            href="{{ route('admin.reports.products') }}">
                            {{ trans('menu.reports.product report') }}
                        </a>
                    </li>
                    @endcan
                    @can('reports.points.view')
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.reports.points', $currentRoute) ? 'active' : '' }}"
                            href="{{ route('admin.reports.points') }}">
                            {{ trans('menu.reports.points report') }}
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @canany(['ads.index', 'features.index', 'footer-content.index', 'faqs.index',
            'sliders.index', 'site-information.index', 'return-policy.index', 'service-terms.index',
            'privacy-policy.index', 'terms-conditions.index', 'area.country.index', 'system.currency.index',
            'system_log.view'])
