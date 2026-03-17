                            href="{{ route('admin.reports.registered-users') }}">
                            {{ trans('menu.reports.registerd users') }}
                        </a>
                    </li>
                    @endcan
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
