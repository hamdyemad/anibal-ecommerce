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

