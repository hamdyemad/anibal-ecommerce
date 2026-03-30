        @canany(['reports.registered_users.view', 'reports.area_users.view', 'reports.orders.view', 'reports.products.view', 'reports.points.view'])
            <li class="menu-title mt-30">
                <span>{{ trans('menu.sections.reports') }}</span>
            </li>
            <li class="has-child {{ isParentMenuOpen(['admin.reports.index', 'admin.reports.registered-users', 'admin.reports.financial', 'admin.reports.area-users', 'admin.reports.orders', 'admin.reports.products', 'admin.reports.points'], ['admin/reports*']) ? 'open' : '' }}">
                <a href="#"
                    class="{{ isParentMenuOpen(['admin.reports.index', 'admin.reports.registered-users', 'admin.reports.financial', 'admin.reports.area-users', 'admin.reports.orders', 'admin.reports.products', 'admin.reports.points'], ['admin/reports*']) ? 'active' : '' }}">
                    <span class="nav-icon uil uil-chart-line"></span>
                    <span class="menu-text">{{ trans('menu.reports.title') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    @can('reports.registered_users.view')
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.reports.registered-users', $currentRoute) ? 'active' : '' }}"
