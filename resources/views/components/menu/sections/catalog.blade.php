        @canany(['departments.index', 'categories.index', 'sub-categories.index'])
            <li class="menu-title mt-30">
                <span>{{ trans('menu.sections.catalog management') }}</span>
            </li>
            <li
                class="has-child {{ isParentMenuOpen(['admin.category-management.departments.index', 'admin.category-management.categories.index', 'admin.category-management.subcategories.index'], ['admin/category-management*']) ? 'open' : '' }}">
                <a href="#"
                    class="{{ isParentMenuOpen(['admin.category-management.departments.index', 'admin.category-management.categories.index', 'admin.category-management.subcategories.index'], ['admin/category-management*']) ? 'active' : '' }}">
                    <span class="nav-icon uil uil-sitemap"></span>
                    <span class="menu-text">{{ trans('menu.category managment.title') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">

                    @can('departments.index')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.category-management.departments.index', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.category-management.departments.index') }}">
                                {{ trans('menu.category managment.department') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive('admin.category-management.departments.index', $currentRoute)) }}">
                                    {{ \Modules\CategoryManagment\app\Models\Department::count() }}
                                </span>
                            </a>
                        </li>
                    @endcan

                    @can('categories.index')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.category-management.categories.index', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.category-management.categories.index') }}">
                                {{ trans('menu.category managment.main category') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive('admin.category-management.categories.index', $currentRoute)) }}">
                                    {{ \Modules\CategoryManagment\app\Models\Category::count() }}
                                </span>
                            </a>
                        </li>
                    @endcan

                    @can('sub-categories.index')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.category-management.subcategories.index', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.category-management.subcategories.index') }}">
                                {{ trans('menu.category managment.sub category') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive('admin.category-management.subcategories.index', $currentRoute)) }}">
                                    {{ \Modules\CategoryManagment\app\Models\SubCategory::count() }}
                                </span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

