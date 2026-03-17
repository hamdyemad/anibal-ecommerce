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
