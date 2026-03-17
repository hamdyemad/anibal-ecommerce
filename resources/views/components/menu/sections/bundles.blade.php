                <span>{{ trans('menu.sections.points system') }}</span>
            </li>
            <li
                class="has-child {{ isParentMenuOpen(['admin.points-settings.index', 'admin.points-settings.user-points.index', 'admin.points-settings.user-points.transactions'], ['admin/points-settings*']) ? 'open' : '' }}">
                <a href="#"
                    class="{{ isParentMenuOpen(['admin.points-settings.index', 'admin.points-settings.user-points.index', 'admin.points-settings.user-points.transactions'], ['admin/points-settings*']) ? 'active' : '' }}">
                    <span class="nav-icon uil uil-coins"></span>
                    <span class="menu-text">{{ trans('menu.point managment.title') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    @can('points-settings.index')
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.points-settings.index'], $currentRoute) ? 'active' : '' }}"
                            href="{{ route('admin.points-settings.index') }}">
                            {{ trans('menu.point managment.title') }}
                        </a>
                    </li>
                    @endcan
                    @can('points-settings.user-points.index')
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.points-settings.user-points.index', 'admin.points-settings.user-points.transactions'], $currentRoute) ? 'active' : '' }}"
                            href="{{ route('admin.points-settings.user-points.index') }}">
                            {{ trans('menu.point managment.users points') }}
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
        @endcanany
