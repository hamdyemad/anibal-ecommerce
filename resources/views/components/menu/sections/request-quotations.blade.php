@can('request-quotations.index')
    <li class="menu-title mt-30">
        <span>{{ trans('menu.sections.vendor_management') }}</span>
    </li>
    <li
        class="has-child {{ isParentMenuOpen(['admin.request-quotations.index', 'admin.request-quotations.archived'], ['admin/request-quotations*']) ? 'open' : '' }}">
        <a href="#"
            class="{{ isParentMenuOpen(['admin.request-quotations.index', 'admin.request-quotations.archived'], ['admin/request-quotations*']) ? 'active' : '' }}">
            <span class="nav-icon uil uil-file-question-alt"></span>
            <span class="menu-text">{{ trans('menu.vendors.request_quotations.title') }}</span>
            <span class="toggle-icon"></span>
        </a>
        <ul class="px-0">
            <li>
                <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.request-quotations.index', $currentRoute) ? 'active' : '' }}"
                    href="{{ route('admin.request-quotations.index') }}">
                    {{ trans('menu.vendors.request_quotations.all_requests') }}
                    <span class="badge badge-round ms-1"
                        style="{{ getBadgeStyle(isMenuActive('admin.request-quotations.index', $currentRoute)) }}">
                        {{ $requestQuotationCounts['not_archived'] ?? 0 }}
                    </span>
                </a>
            </li>
            <li>
                <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.request-quotations.archived', $currentRoute) ? 'active' : '' }}"
                    href="{{ route('admin.request-quotations.archived') }}">
                    {{ trans('menu.vendors.request_quotations.archived_requests') }}
                    <span class="badge badge-round ms-1"
                        style="{{ getBadgeStyle(isMenuActive('admin.request-quotations.archived', $currentRoute)) }}">
                        {{ $requestQuotationCounts['archived'] ?? 0 }}
                    </span>
                </a>
            </li>
        </ul>
    </li>
@endcan
