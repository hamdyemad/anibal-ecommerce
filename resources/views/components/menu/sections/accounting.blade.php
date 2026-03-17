        @canany(['accounting.summary.index', 'accounting.balances.index', 'accounting.expense-items.index', 'accounting.expenses.index', 'accounting.income.index'])
            <li class="menu-title mt-30">
                <span>{{ trans('menu.sections.financials') }}</span>
            </li>
            <li
                class="has-child {{ isParentMenuOpen(['admin.accounting.summary', 'admin.accounting.income', 'admin.accounting.balances', 'admin.accounting.expenses', 'admin.accounting.expense-items'], ['admin/accounting*']) ? 'open' : '' }}">
                <a href="#"
                    class="{{ isParentMenuOpen(['admin.accounting.summary', 'admin.accounting.income', 'admin.accounting.balances', 'admin.accounting.expenses', 'admin.accounting.expense-items'], ['admin/accounting*']) ? 'active' : '' }}">
                    <span class="nav-icon uil uil-invoice"></span>
                    <span class="menu-text">{{ trans('menu.accounting module.title') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    @can('accounting.summary.index')
                        <li><a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.accounting.summary', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.accounting.summary') }}">{{ trans('menu.accounting module.overview') }}</a>
                        </li>
                    @endcan
                    @can('accounting.balances.index')
                        <li><a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.accounting.balances', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.accounting.balances') }}">{{ trans('menu.accounting module.vendor_balances') }}</a>
                        </li>
                    @endcan
                    @can('accounting.expense-items.index')
                        <li><a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.accounting.expense-items', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.accounting.expense-items') }}">{{ trans('menu.accounting module.expense_categories') }}</a>
                        </li>
                    @endcan
                    @can('accounting.expenses.index')
                        <li><a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.accounting.expenses', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.accounting.expenses') }}">{{ trans('menu.accounting module.expense_records') }}</a>
                        </li>
                    @endcan
                    @can('accounting.income.index')
                        <li><a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.accounting.income', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.accounting.income') }}">{{ trans('menu.accounting module.income_entries') }}</a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

