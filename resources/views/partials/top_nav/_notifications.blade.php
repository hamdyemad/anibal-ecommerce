@php
    // Collect all notifications from different sources
    $notifications = collect();
    
    // 1. Vendor Requests (pending vendors where active = 0)
    if (isAdmin()) {
        $vendorRequests = \Modules\Vendor\app\Models\Vendor::where('active', 0)
            ->with('user')
            ->get()
            ->map(function($vendor) {
                return [
                    'type' => 'vendor_request',
                    'icon' => 'uil-user-plus',
                    'color' => 'warning',
                    'title' => $vendor->name,
                    'description' => trans('menu.become a vendor requests.wants_to_become'),
                    'url' => route('admin.vendors.show', $vendor->id),
                    'created_at' => $vendor->created_at,
                ];
            });
        $notifications = $notifications->merge($vendorRequests);
    }
    
    // 2. Recent Orders
    $ordersQuery = \Modules\Order\app\Models\Order::with('customer');
    if (!isAdmin() && auth()->user()->vendor) {
        $ordersQuery->whereHas('products', function($q) {
            $q->where('vendor_id', auth()->user()->vendor->id);
        });
    }
    $recentOrders = $ordersQuery->orderBy('created_at', 'desc')
        ->limit(10)
        ->get()
        ->map(function($order) {
            return [
                'type' => 'order',
                'icon' => 'uil-shopping-bag',
                'color' => 'primary',
                'title' => trans('menu.order') . ' #' . $order->order_number,
                'description' => $order->customer_name ?? 'N/A',
                'url' => route('admin.orders.show', $order->id),
                'created_at' => $order->created_at,
            ];
        });
    $notifications = $notifications->merge($recentOrders);
    
    // 3. User Messages (pending messages)
    if (isAdmin()) {
        $messages = \Modules\SystemSetting\app\Models\Message::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($message) {
                return [
                    'type' => 'message',
                    'icon' => 'uil-envelope',
                    'color' => 'success',
                    'title' => $message->name,
                    'description' => $message->title,
                    'url' => route('admin.messages.show', $message->id),
                    'created_at' => $message->created_at,
                ];
            });
        $notifications = $notifications->merge($messages);
    }
    
    // Sort by created_at and take latest 5
    $notifications = $notifications->sortByDesc('created_at')->take(5);
    $notificationsCount = $notifications->count();
@endphp

<li class="nav-notification">
    <div class="dropdown-custom" style="position: relative;">
        <a href="javascript:;" class="nav-item-toggle icon-active">
            <img class="svg" src="{{ asset('assets/img/svg/alarm.svg') }}" alt="img">
            @if($notificationsCount > 0)
                <span class="nav-item__badge" style="position: absolute; top: -8px; background-color: #fa8b0c; color: white; border-radius: 50%; width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 600; line-height: 1; z-index: 10;">{{ $notificationsCount }}</span>
            @endif
        </a>
        <div class="dropdown-wrapper">
            <h2 class="dropdown-wrapper__title">{{ trans('menu.notifications.title') }} <span class="badge-circle badge-warning ms-1">{{ $notificationsCount }}</span></h2>
            @if($notificationsCount > 0)
                <ul>
                    @foreach($notifications as $notification)
                        <li class="nav-notification__single nav-notification__single--unread d-flex flex-wrap">
                            <div class="nav-notification__type nav-notification__type--{{ $notification['color'] }}">
                                <i class="{{ $notification['icon'] }}"></i>
                            </div>
                            <div class="nav-notification__details">
                                <p>
                                    <a href="{{ $notification['url'] }}" class="subject stretched-link text-truncate" style="max-width: 180px;">{{ $notification['title'] }}</a>
                                    <span>{{ $notification['description'] }}</span>
                                </p>
                                <p>
                                    <span class="time-posted">{{ $notification['created_at'] }}</span>
                                </p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-center py-4">
                    <p class="text-muted">{{ trans('menu.no_notifications') }}</p>
                </div>
            @endif
            <a href="{{ route('admin.dashboard') }}" class="dropdown-wrapper__more">{{ trans('menu.see_all_notifications') }}</a>
        </div>
    </div>
</li>
