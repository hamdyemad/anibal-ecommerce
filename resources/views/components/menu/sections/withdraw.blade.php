@canany(['withdraw.send_money.create', 'withdraw.transactions.view', 'withdraw.my_transactions.view', 'withdraw.request.create', 'withdraw.vendor_requests.new.view'])
    <li class="menu-title mt-30">
        <span>{{ trans('menu.sections.withdraw module') }}</span>
    </li>
    <li
        class="has-child {{ isParentMenuOpen(['admin.sendMoney', 'admin.allTransactions', 'admin.allVendorsTransactions', 'admin.sendMoneyRequest', 'admin.transactionsRequests'], ['admin/send-money*', 'admin/transactions*', 'admin/withdraw*', 'admin/vendors-transactions*', 'admin/trasnactions-requests*']) ? 'open' : '' }}">
        <a href="#"
            class="{{ isParentMenuOpen(['admin.sendMoney', 'admin.allTransactions', 'admin.allVendorsTransactions', 'admin.sendMoneyRequest', 'admin.transactionsRequests'], ['admin/send-money*', 'admin/transactions*', 'admin/withdraw*', 'admin/vendors-transactions*', 'admin/trasnactions-requests*']) ? 'active' : '' }}">
            <span class="nav-icon uil uil-sitemap"></span>
            <span class="menu-text">{{ trans('menu.withdraw module.title') }}</span>
            <span class="toggle-icon"></span>
        </a>
        <ul class="px-0">
            @can('withdraw.send_money.create')
                <li>
                    <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.sendMoney', $currentRoute) ? 'active' : '' }}"
                        href="{{ route('admin.sendMoney') }}">
                        {{ trans('menu.withdraw module.send money') }}
                    </a>
                </li>
            @endcan

            @can('withdraw.transactions.view')
                <li>
                    <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.allVendorsTransactions', $currentRoute) ? 'active' : '' }}"
                        href="{{ route('admin.allVendorsTransactions') }}">
                        {{ trans('dashboard.vendors_transactions_overview') }}
                    </a>
                </li>

                <li>
                    <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.allTransactions', $currentRoute) ? 'active' : '' }}"
                        href="{{ route('admin.allTransactions') }}">
                        {{ trans('menu.withdraw module.all transactions') }}
                        <span class="badge badge-round ms-1"
                            style="{{ getBadgeStyle(isMenuActive('admin.allTransactions', $currentRoute)) }}">{{ $all_transactions }}</span>
                    </a>
                </li>
            @endcan

            @if(isVendor())
                @can('withdraw.request.create')
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.sendMoneyRequest', $currentRoute) ? 'active' : '' }}"
                            href="{{ route('admin.sendMoneyRequest') }}">
                            {{ trans('menu.withdraw module.send money request') }}
                        </a>
                    </li>
                @endcan
            @endif

            {{-- Vendor view: my transactions (only for vendor users) --}}
            @if(isVendor())
                @can('withdraw.my_transactions.view')
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'new' ? 'active' : '' }}"
                            href="{{ route('admin.transactionsRequests', ['status' => 'new']) }}">
                            {{ trans('menu.withdraw module.my_new_requests') }}
                            <span class="badge badge-round ms-1"
                                style="{{ getBadgeStyle(isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'new') }}">{{ $new_transactions }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'accepted' ? 'active' : '' }}"
                            href="{{ route('admin.transactionsRequests', ['status' => 'accepted']) }}">
                            {{ trans('menu.withdraw module.my_accepted_requests') }}
                            <span class="badge badge-round ms-1"
                                style="{{ getBadgeStyle(isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'accepted') }}">{{ $accepted_transactions }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'rejected' ? 'active' : '' }}"
                            href="{{ route('admin.transactionsRequests', ['status' => 'rejected']) }}">
                            {{ trans('menu.withdraw module.my_rejected_requests') }}
                            <span class="badge badge-round ms-1"
                                style="{{ getBadgeStyle(isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'rejected', 'secondary') }}">{{ $rejected_transactions }}</span>
                        </a>
                    </li>
                @endcan
            @endif

            {{-- Admin view: vendor requests management --}}
            @can('withdraw.vendor_requests.new.view')
                <li>
                    <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'new' ? 'active' : '' }}"
                        href="{{ route('admin.transactionsRequests', ['status' => 'new']) }}">
                        {{ trans('menu.withdraw module.new transaction requests') }}
                        <span class="badge badge-round ms-1"
                            style="{{ getBadgeStyle(isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'new') }}">{{ $new_transactions }}</span>
                    </a>
                </li>
            @endcan

            @can('withdraw.vendor_requests.accepted.view')
                <li>
                    <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'accepted' ? 'active' : '' }}"
                        href="{{ route('admin.transactionsRequests', ['status' => 'accepted']) }}">
                        {{ trans('menu.withdraw module.accepted transaction requests') }}
                        <span class="badge badge-round ms-1"
                            style="{{ getBadgeStyle(isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'accepted') }}">{{ $accepted_transactions }}</span>
                    </a>
                </li>
            @endcan

            @can('withdraw.vendor_requests.rejected.view')
                <li>
                    <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'rejected' ? 'active' : '' }}"
                        href="{{ route('admin.transactionsRequests', ['status' => 'rejected']) }}">
                        {{ trans('menu.withdraw module.rejected transaction requests') }}
                        <span class="badge badge-round ms-1"
                            style="{{ getBadgeStyle(isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'rejected', 'secondary') }}">{{ $rejected_transactions }}</span>
                    </a>
                </li>
            @endcan

        </ul>
    </li>
@endcanany
