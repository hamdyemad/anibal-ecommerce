                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany


        @canany(['vendors.index', 'vendors.create'])
            <li
                class="has-child {{ Request::is(LaravelLocalization::getCurrentLocale() . '/admin/vendors*') ? 'open' : '' }}">
                <a href="#"
                    class="{{ Request::is(LaravelLocalization::getCurrentLocale() . '/admin/vendors*') ? 'active' : '' }}">
                    <span class="nav-icon uil uil-users-alt"></span>
                    <span class="menu-text">{{ trans('menu.vendors.title') }}</span>
