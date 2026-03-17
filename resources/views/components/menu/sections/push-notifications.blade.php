@canany(['push-notifications.index', 'push-notifications.create'])
    <li class="menu-title mt-30">
        <span>{{ trans('menu.sections.push_notifications') }}</span>
    </li>
    <li
        class="has-child {{ isParentMenuOpen(['admin.system-settings.push-notifications.index', 'admin.system-settings.push-notifications.create'], ['admin/system-settings/push-notifications*']) ? 'open' : '' }}">
        <a href="#"
            class="{{ isParentMenuOpen(['admin.system-settings.push-notifications.index', 'admin.system-settings.push-notifications.create'], ['admin/system-settings/push-notifications*']) ? 'active' : '' }}">
            <span class="nav-icon uil uil-bell"></span>
            <span class="menu-text">{{ trans('menu.push_notifications.title') }}</span>
            <span class="toggle-icon"></span>
        </a>
        <ul class="px-0">
            @can('push-notifications.create')
            <li class="l_sidebar">
                <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.system-settings.push-notifications.create', $currentRoute) ? 'active' : '' }}"
                    href="{{ route('admin.system-settings.push-notifications.create') }}">
                    {{ trans('menu.push_notifications.send_notification') }}
                </a>
            </li>
            @endcan
            @can('push-notifications.index')
            <li class="l_sidebar">
                <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.system-settings.push-notifications.index', $currentRoute) ? 'active' : '' }}"
                    href="{{ route('admin.system-settings.push-notifications.index') }}">
                    {{ trans('menu.push_notifications.all_notifications') }}
                    <span class="badge badge-round ms-1"
                        style="{{ getBadgeStyle(isMenuActive('admin.system-settings.push-notifications.index', $currentRoute)) }}">
                        {{ \Modules\SystemSetting\app\Models\PushNotification::count() }}
                    </span>
                </a>
            </li>
            @endcan
        </ul>
    </li>
@endcanany
