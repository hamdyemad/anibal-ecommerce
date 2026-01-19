@php
    // Get admin notifications from database (not viewed by current user)
    $adminNotificationsQuery = \App\Models\AdminNotification::notViewedBy(auth()->id())->orderBy('created_at', 'desc');
    
    // Filter by vendor if not admin
    if (isAdmin()) {
        // Admin sees all notifications without vendor_id
        $adminNotificationsQuery->whereNull('vendor_id');
    } else {
        // Vendors see their own notifications, but exclude admin-only types
        $vendorId = auth()->user()->vendor->id;
        $adminNotificationsQuery->where(function($q) use ($vendorId) {
            $q->where('vendor_id', $vendorId)
              ->orWhereNull('vendor_id');
        })->whereNotIn('type', ['vendor_request', 'new_message']);
    }
    
    $notifications = $adminNotificationsQuery->limit(20)
        ->get()
        ->map(function($notification) {
            return [
                'id' => $notification->id,
                'type' => $notification->type,
                'icon' => $notification->icon,
                'color' => $notification->color,
                'title' => $notification->getTranslatedTitle(),
                'description' => $notification->getTranslatedDescription(),
                'url' => $notification->url ?? '#',
                'created_at' => $notification->getRawOriginal('created_at'),
                'source' => 'admin_notifications',
            ];
        });
    
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
                                    <a href="{{ route('admin.notifications.show', $notification['id']) }}" class="subject stretched-link text-truncate" style="max-width: 180px;">{{ $notification['title'] }}</a>
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
