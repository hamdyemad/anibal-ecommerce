        @canany(['admin-roles.index', 'admins.index', 'vendor-user-roles.index', 'vendor-users.index'])
            <li class="menu-title mt-30">
                <span>{{ trans('menu.sections.user management') }}</span>
            </li>
        @endcanany
        {{-- Admin Management --}}
        @canany(['admin-roles.index', 'admins.index'])
            <li
                class="has-child {{ isParentMenuOpen(['admin.admin-management.roles.index', 'admin.admin-management.admins.index'], ['admin/admin-management*']) ? 'open' : '' }}">
                <a href="#"
                    class="{{ isParentMenuOpen(['admin.admin-management.roles.index', 'admin.admin-management.admins.index'], ['admin/admin-management*']) ? 'active' : '' }}">
                    <span class="nav-icon uil uil-user-check"></span>
                    <span class="menu-text">{{ trans('menu.admin managment.title') }}</span>
                    <span class="toggle-icon"></span>
                </a>
