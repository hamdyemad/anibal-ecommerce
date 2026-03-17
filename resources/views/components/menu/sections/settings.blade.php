            <li class="menu-title mt-30">
                <span>{{ trans('menu.sections.settings') }}</span>
            </li>
        @endcanany

        @can('ads.index')
            <li
                class="has-child {{ isParentMenuOpen(['admin.system-settings.ads.index'], ['*/system-settings/ads*']) ? 'open' : '' }}">
                <a href="#"
                    class="{{ isParentMenuOpen(['admin.system-settings.ads.index'], ['*/system-settings/ads*']) ? 'active' : '' }}">
                    <span class="nav-icon uil uil-trophy"></span>
                    <span class="menu-text">{{ trans('menu.advertisements.title') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.system-settings.ads.index', $currentRoute) ? 'active' : '' }}"
                            href="{{ route('admin.system-settings.ads.index') }}">
                            {{ trans('menu.advertisements.title') }}
                            <span class="badge badge-round ms-1"
                                style="{{ getBadgeStyle(isMenuActive('admin.system-settings.ads.index', $currentRoute)) }}">
                                {{ \Modules\SystemSetting\app\Models\Ad::count() }}
                            </span>
                        </a>
                    </li>
                </ul>
            </li>
        @endcan

        {{-- Frontend Settings --}}
        @canany(['features.index', 'footer-content.index', 'faqs.index', 'sliders.index'])
            <li
                class="has-child {{ isParentMenuOpen(['admin.system-settings.features.index', 'admin.system-settings.footer-content.index', 'admin.system-settings.faqs.index', 'admin.system-settings.sliders.index', 'admin.system-settings.site-information.index'], ['*/system-settings/features*', '*/system-settings/footer-content*', '*/system-settings/faqs*', '*/system-settings/sliders*', '*/system-settings/site-information*']) ? 'open' : '' }}">
                <a href="#"
                    class="{{ isParentMenuOpen(['admin.system-settings.features.index', 'admin.system-settings.footer-content.index', 'admin.system-settings.faqs.index', 'admin.system-settings.sliders.index', 'admin.system-settings.site-information.index'], ['*/system-settings/features*', '*/system-settings/footer-content*', '*/system-settings/faqs*', '*/system-settings/sliders*', '*/system-settings/site-information*']) ? 'active' : '' }}">
                    <span class="nav-icon uil uil-window-section"></span>
                    <span class="menu-text fw-bold">{{ trans('menu.frontend settings.title') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    @can('features.index')
                        <li>
                            <a href="{{ route('admin.system-settings.features.index') }}"
                                class="{{ isMenuActive('admin.system-settings.features.index', $currentRoute) ? 'active' : '' }}">
                                <span class="nav-icon uil uil-star"></span>
                                <span class=" fw-bold">{{ trans('menu.frontend settings.our features') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('footer-content.index')
                        <li>
                            <a href="{{ route('admin.system-settings.footer-content.index') }}"
                                class="{{ isMenuActive('admin.system-settings.footer-content.index', $currentRoute) ? 'active' : '' }}">
                                <span class="nav-icon uil uil-align-center-alt"></span>
                                <span class=" fw-bold">{{ trans('menu.frontend settings.footer content') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('faqs.index')
                        <li>
                            <a href="{{ route('admin.system-settings.faqs.index') }}"
                                class="{{ isMenuActive(['admin.system-settings.faqs.index', 'admin.system-settings.faqs.create', 'admin.system-settings.faqs.edit', 'admin.system-settings.faqs.show'], $currentRoute) ? 'active' : '' }}">
                                <span class="nav-icon uil uil-question-circle"></span>
                                <span class=" fw-bold">{{ trans('menu.frontend settings.faq management') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('sliders.index')
                        <li>
                            <a href="{{ route('admin.system-settings.sliders.index') }}"
                                class="{{ isMenuActive(['admin.system-settings.sliders.index', 'admin.system-settings.sliders.create', 'admin.system-settings.sliders.edit', 'admin.system-settings.sliders.show'], $currentRoute) ? 'active' : '' }}">
                                <span class="nav-icon uil uil-image-v"></span>
                                <span class=" fw-bold">{{ trans('menu.frontend settings.sliders') }}</span>
                            </a>
                        </li>
                    @endcan

                </ul>
            </li>
        @endcanany

        {{-- Site Information --}}
        @canany(['site-information.index', 'about-us.index', 'return-policy.index', 'service-terms.index',
            'privacy-policy.index', 'terms-conditions.index'])
            <li class="has-child">
                <a href="#"
                    class="{{ isMenuActive(['admin.system-settings.site-information.index', 'admin.system-settings.about-us.website', 'admin.system-settings.about-us.mobile'], $currentRoute) ? 'active' : '' }}">
                    <span class="nav-icon uil uil-info-circle"></span>
                    <span class="fw-bold">{{ trans('menu.frontend settings.site information') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    @can('site-information.index')
                        <li>
                            <a href="{{ route('admin.system-settings.site-information.index') }}"
                                class="{{ isMenuActive('admin.system-settings.site-information.index', $currentRoute) ? 'active' : '' }}">
                                <span class="fw-bold">{{ trans('menu.frontend settings.contact us') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('about-us.index')
                        <li>
                            <a href="{{ route('admin.system-settings.about-us.website') }}"
                                class="{{ isMenuActive('admin.system-settings.about-us.website', $currentRoute) ? 'active' : '' }}">
                                <span class="fw-bold">{{ trans('menu.frontend settings.about us website') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.system-settings.about-us.mobile') }}"
                                class="{{ isMenuActive('admin.system-settings.about-us.mobile', $currentRoute) ? 'active' : '' }}">
                                <span class="fw-bold">{{ trans('menu.frontend settings.about us mobile') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('return-policy.index')
                        <li>
                            <a href="{{ route('admin.system-settings.return-policy.index') }}"
                                class="{{ isMenuActive('admin.system-settings.return-policy.index', $currentRoute) ? 'active' : '' }}">
                                <span class="fw-bold">{{ trans('menu.frontend settings.return policy') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('service-terms.index')
                        <li>
                            <a href="{{ route('admin.system-settings.service-terms.index') }}"
                                class="{{ isMenuActive('admin.system-settings.service-terms.index', $currentRoute) ? 'active' : '' }}">
                                <span class="fw-bold">{{ trans('menu.frontend settings.service terms') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('privacy-policy.index')
                        <li>
                            <a href="{{ route('admin.system-settings.privacy-policy.index') }}"
                                class="{{ isMenuActive('admin.system-settings.privacy-policy.index', $currentRoute) ? 'active' : '' }}">
                                <span class="fw-bold">{{ trans('menu.frontend settings.privacy policy') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('terms-conditions.index')
                        <li>
                            <a href="{{ route('admin.system-settings.terms-conditions.index') }}"
                                class="{{ isMenuActive('admin.system-settings.terms-conditions.index', $currentRoute) ? 'active' : '' }}">
                                <span class="fw-bold">{{ trans('menu.frontend settings.terms conditions') }}</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @canany(['area.country.index', 'area.city.index', 'area.region.index', 'area.subregion.index'])
            <li
                class="has-child {{ isParentMenuOpen(['admin.area-settings.countries.index', 'admin.area-settings.cities.index', 'admin.area-settings.regions.index', 'admin.area-settings.subregions.index'], ['admin/area-settings*']) ? 'open' : '' }}">
                <a href="#"
                    class="{{ isParentMenuOpen(['admin.area-settings.countries.index', 'admin.area-settings.cities.index', 'admin.area-settings.regions.index', 'admin.area-settings.subregions.index'], ['admin/area-settings*']) ? 'active' : '' }}">
                    <span class="nav-icon uil uil-map-marker"></span>
                    <span class="menu-text">{{ trans('menu.area settings.title') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    @can('area.country.index')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.area-settings.countries.index', 'admin.area-settings.countries.create', 'admin.area-settings.countries.show', 'admin.area-settings.countries.edit'], $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.area-settings.countries.index') }}">
                                {{ trans('menu.area settings.country') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive(['admin.area-settings.countries.index', 'admin.area-settings.countries.create', 'admin.area-settings.countries.show', 'admin.area-settings.countries.edit'], $currentRoute)) }}">{{ \Modules\AreaSettings\app\Models\Country::count() }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('area.city.index')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.area-settings.cities.index', 'admin.area-settings.cities.create', 'admin.area-settings.cities.show', 'admin.area-settings.cities.edit'], $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.area-settings.cities.index') }}">
                                {{ trans('menu.area settings.city') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive(['admin.area-settings.cities.index', 'admin.area-settings.cities.create', 'admin.area-settings.cities.show', 'admin.area-settings.cities.edit'], $currentRoute)) }}">{{ \Modules\AreaSettings\app\Models\City::count() }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('area.region.index')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.area-settings.regions.index', 'admin.area-settings.regions.create', 'admin.area-settings.regions.show', 'admin.area-settings.regions.edit'], $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.area-settings.regions.index') }}">
                                {{ trans('menu.area settings.region') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive(['admin.area-settings.regions.index', 'admin.area-settings.regions.create', 'admin.area-settings.regions.show', 'admin.area-settings.regions.edit'], $currentRoute)) }}">{{ \Modules\AreaSettings\app\Models\Region::count() }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('area.subregion.index')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.area-settings.subregions.index', 'admin.area-settings.subregions.create', 'admin.area-settings.subregions.show', 'admin.area-settings.subregions.edit'], $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.area-settings.subregions.index') }}">
                                {{ trans('menu.area settings.subregion') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive(['admin.area-settings.subregions.index', 'admin.area-settings.subregions.create', 'admin.area-settings.subregions.show', 'admin.area-settings.subregions.edit'], $currentRoute)) }}">{{ \Modules\AreaSettings\app\Models\SubRegion::count() }}</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @can('system.currency.index')
            <li>
                <a href="{{ route('admin.system-settings.currencies.index') }}"
                    class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.system-settings.currencies.index', 'admin.system-settings.currencies.create', 'admin.system-settings.currencies.show', 'admin.system-settings.currencies.edit'], $currentRoute) ? 'active' : '' }}">
                    <span class="d-flex align-items-center">
                        <span class="nav-icon uil uil-dollar-alt"></span>
                        <span class="menu-text">{{ trans('menu.currencies.title') }}</span>
                    </span>
                    <span class="badge badge-round ms-1"
                        style="{{ getBadgeStyle(isMenuActive(['admin.system-settings.currencies.index', 'admin.system-settings.currencies.create', 'admin.system-settings.currencies.show', 'admin.system-settings.currencies.edit'], $currentRoute)) }}">
                        {{ \Modules\SystemSetting\app\Models\Currency::count() }}
                    </span>
                </a>
            </li>
        @endcan

        @can('system_log.view')
            <li>
                <a href="{{ route('admin.system-settings.activity-logs.index') }}"
                    class="{{ isMenuActive('admin.system-settings.activity-logs.index', $currentRoute) ? 'active' : '' }}">
                    <span class="nav-icon uil uil-history"></span>
                    <span class="menu-text">{{ trans('menu.system log.title') }}</span>
                </a>
            </li>
        @endcan

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to open parent menus
        function openParentMenus() {
            // Find all active menu items (both parent and child)
