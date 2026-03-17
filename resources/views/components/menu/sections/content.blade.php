            <li class="menu-title mt-30">
                <span>{{ trans('menu.sections.content and engagement') }}</span>
            </li>
            <li
                class="has-child {{ isParentMenuOpen(['admin.system-settings.blog-categories.index', 'admin.system-settings.blogs.index'], ['admin/system-settings/blog-categories*', 'admin/system-settings/blogs*']) ? 'open' : '' }}">
                <a href="#"
                    class="{{ isParentMenuOpen(['admin.system-settings.blog-categories.index', 'admin.system-settings.blogs.index'], ['admin/system-settings/blog-categories*', 'admin/system-settings/blogs*']) ? 'active' : '' }}">
                    <span class="nav-icon uil uil-edit-alt"></span>
                    <span class="menu-text">{{ trans('menu.blog managment.title') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    @can('blog-categories.index')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.system-settings.blog-categories.index', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.system-settings.blog-categories.index') }}">
                                {{ trans('menu.blog managment.categories') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive('admin.system-settings.blog-categories.index', $currentRoute)) }}">
                                    @php
                                        try {
                                            $blog_categories_count = \Modules\SystemSetting\app\Models\BlogCategory::count();
                                        } catch (\Exception $e) {
                                            $blog_categories_count = 0;
                                        }
                                    @endphp
                                    {{ $blog_categories_count }}
                                </span>
                            </a>
                        </li>
                    @endcan
                    @can('blogs.index')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.system-settings.blogs.index', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.system-settings.blogs.index') }}">
                                {{ trans('menu.blog managment.blogs') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive('admin.system-settings.blogs.index', $currentRoute)) }}">
                                    @php
                                        try {
                                            $blogs_count = \Modules\SystemSetting\app\Models\Blog::count();
                                        } catch (\Exception $e) {
                                            $blogs_count = 0;
                                        }
                                    @endphp
                                    {{ $blogs_count }}
                                </span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @can('messages.index')
            <li class="{{ isMenuActive('admin.messages.index', $currentRoute) ? 'active' : '' }}">
                <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.messages.index', $currentRoute) ? 'active' : '' }}"
                    href="{{ route('admin.messages.index') }}">
                    <span class="d-flex align-items-center">
                        <span class="nav-icon uil uil-envelope"></span>
                        <span class="menu-text">{{ __('systemsetting::messages.messages') }}</span>
                    </span>
                    <span class="badge badge-round ms-1"
                        style="{{ getBadgeStyle(isMenuActive('admin.messages.index', $currentRoute)) }}">
                        {{ \Modules\SystemSetting\app\Models\Message::count() }}
                    </span>
                </a>
            </li>
        @endcan

