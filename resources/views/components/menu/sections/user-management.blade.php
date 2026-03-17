                                {{ trans('menu.admin managment.admin managment') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive(['admin.admin-management.admins.index', 'admin.admin-management.admins.create', 'admin.admin-management.admins.show', 'admin.admin-management.admins.edit'], $currentRoute)) }}">
                                    {{ $admins_count }}
                                </span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany
        {{-- Vendor Users Management --}}
        @canany(['vendor-user-roles.index', 'vendor-users.index'])
            <li
                class="has-child {{ isParentMenuOpen(['admin.vendor-users-management.roles.index', 'admin.vendor-users-management.vendor-users.index'], ['admin/vendor-users-management*']) ? 'open' : '' }}">
                <a href="#"
                    class="{{ isParentMenuOpen(['admin.vendor-users-management.roles.index', 'admin.vendor-users-management.vendor-users.index'], ['admin/vendor-users-management*']) ? 'active' : '' }}">
                    <span class="nav-icon uil uil-users-alt"></span>
                    <span class="menu-text">{{ trans('menu.admin managment.vendor users management') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    @can('vendor-user-roles.index')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.vendor-users-management.roles.index', 'admin.vendor-users-management.roles.create', 'admin.vendor-users-management.roles.show', 'admin.vendor-users-management.roles.edit'], $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.vendor-users-management.roles.index') }}">
                                {{ trans('menu.admin managment.vendor users roles management') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive(['admin.vendor-users-management.roles.index', 'admin.vendor-users-management.roles.create', 'admin.vendor-users-management.roles.show', 'admin.vendor-users-management.roles.edit'], $currentRoute)) }}">
                                    {{ $vendor_user_roles_count }}
                                </span>
                            </a>
                        </li>
                    @endcan

                    @can('vendor-users.index')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.vendor-users-management.vendor-users.index', 'admin.vendor-users-management.vendor-users.create', 'admin.vendor-users-management.vendor-users.show', 'admin.vendor-users-management.vendor-users.edit'], $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.vendor-users-management.vendor-users.index') }}">
                                {{ trans('menu.admin managment.vendor users management') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive(['admin.vendor-users-management.vendor-users.index', 'admin.vendor-users-management.vendor-users.create', 'admin.vendor-users-management.vendor-users.show', 'admin.vendor-users-management.vendor-users.edit'], $currentRoute)) }}">
                                    {{ $vendor_users_count }}
                                </span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany


        @canany(['vendors.index', 'vendors.create'])
            <li
                class="has-child {{ Request::is(LaravelLocalization::getCurrentLocale() . '/admin/vendors*') ? 'open' : '' }}">
                <a href="#"
                    class="{{ Request::is(LaravelLocalization::getCurrentLocale() . '/admin/vendors*') ? 'active' : '' }}">
                    <span class="nav-icon uil uil-users-alt"></span>
                    <span class="menu-text">{{ trans('menu.vendors.title') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    @can('vendors.index')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.vendors.index', 'admin.vendors.show', 'admin.vendors.edit'], $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.vendors.index') }}">
                                {{ trans('menu.vendors.all') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive(['admin.vendors.index', 'admin.vendors.show', 'admin.vendors.edit'], $currentRoute)) }}">
                                    {{ \Modules\Vendor\app\Models\Vendor::count() }}
                                </span>
                            </a>
                        </li>
                    @endcan

                    @can('vendors.create')
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
