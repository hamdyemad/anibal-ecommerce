@php
    $latestOrders = isAdmin() 
        ? \Modules\Order\app\Models\Order::orderBy('id', 'desc')->take(5)->get()
        : \Modules\Order\app\Models\Order::whereHas('products', function($q) {
            $q->where('vendor_id', auth()->user()->vendor_id ?? auth()->id());
        })->orderBy('id', 'desc')->take(5)->get();
    
    $ordersCount = $latestOrders->count();
@endphp

<li class="nav-order">
    <div class="dropdown-custom" style="position: relative;">
        <a href="javascript:;" class="nav-item-toggle icon-active">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="svg">
                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <path d="M16 10a4 4 0 0 1-8 0"></path>
            </svg>
            @if($ordersCount > 0)
                <span class="nav-item__badge" style="position: absolute; top: -8px; background-color: #5f63f2; color: white; border-radius: 50%; width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 600; line-height: 1; z-index: 10;">{{ $ordersCount }}</span>
            @endif
        </a>
        <div class="dropdown-wrapper">
            <h2 class="dropdown-wrapper__title">{{ trans('menu.latest_orders') }} <span class="badge-circle badge-primary ms-1">{{ $ordersCount }}</span></h2>
            @if($ordersCount > 0)
                <ul>
                    @foreach($latestOrders as $order)
                        <li class="nav-notification__single d-flex flex-wrap">
                            <div class="nav-notification__type nav-notification__type--primary">
                                <i class="uil uil-shopping-bag"></i>
                            </div>
                            <div class="nav-notification__details">
                                <p>
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="subject stretched-link text-truncate" style="max-width: 180px;">{{ trans('menu.order') }} #{{ $order->order_number }}</a>
                                    <span>{{ $order->customer_name }}</span>
                                </p>
                                <p>
                                    <span class="time-posted">{{ number_format($order->total_price, 2) }} {{ currency() }}</span>
                                </p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-center py-4">
                    <p class="text-muted">{{ trans('menu.no_orders') }}</p>
                </div>
            @endif
            <a href="{{ route('admin.orders.index') }}" class="dropdown-wrapper__more">{{ trans('menu.see_all_orders') }}</a>
        </div>
    </div>
</li>
