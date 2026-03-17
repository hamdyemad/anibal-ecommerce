                    @can('admin-roles.index')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.admin-management.roles.index', 'admin.admin-management.roles.create', 'admin.admin-management.roles.show', 'admin.admin-management.roles.edit'], $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.admin-management.roles.index') }}">
                                {{ trans('menu.admin managment.roles managment') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive(['admin.admin-management.roles.index', 'admin.admin-management.roles.create', 'admin.admin-management.roles.show', 'admin.admin-management.roles.edit'], $currentRoute)) }}">
                                    {{ $admin_roles_count }}
                                </span>
                            </a>
                        </li>
                    @endcan

                    @can('admins.index')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.admin-management.admins.index', 'admin.admin-management.admins.create', 'admin.admin-management.admins.show', 'admin.admin-management.admins.edit'], $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.admin-management.admins.index') }}">
                                {{ trans('menu.admin managment.admin managment') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive(['admin.admin-management.admins.index', 'admin.admin-management.admins.create', 'admin.admin-management.admins.show', 'admin.admin-management.admins.edit'], $currentRoute)) }}">
