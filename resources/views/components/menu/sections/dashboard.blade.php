@can('dashboard.view')
<li>
    <a href="{{ route('admin.dashboard') }}"
        class="{{ isMenuActive('admin.dashboard', $currentRoute) ? 'active' : '' }}">
        <span class="nav-icon uil uil-create-dashboard"></span>
        <span class="menu-text">{{ trans('menu.dashboard.title') }}</span>
    </a>
</li>
@endcan
