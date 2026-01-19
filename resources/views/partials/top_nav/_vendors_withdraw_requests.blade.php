@php
    $vendor = auth()->user()->vendor ?? null;
    $withdrawNotifications = [];

    try {
        if ($vendor) {
            // For vendors: show accepted/rejected withdraw notifications
            $withdrawNotifications = \App\Models\AdminNotification::notViewedBy(auth()->id())
                ->where('type', 'withdraw_status')
                ->where('vendor_id', $vendor->id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        } else {
            // For admin: show new withdraw requests
            $withdrawNotifications = \App\Models\AdminNotification::notViewedBy(auth()->id())
                ->where('type', 'withdraw_request')
                ->whereNull('vendor_id')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }
    } catch (\Exception $e) {
        // Silently fail
        $withdrawNotifications = collect([]);
    }
@endphp
<li class="nav-notification">
    <div class="dropdown-custom" style="position: relative;">
        <a href="javascript:;" class="nav-item-toggle icon-active">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="svg">
                <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                <line x1="2" y1="10" x2="22" y2="10"></line>
            </svg>
            <span class="nav-item__badge"
                style="position: absolute; top: -8px; background-color: #fa8b0c; color: white; border-radius: 50%; width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 600; line-height: 1; z-index: 10;"
                dir="ltr">
                <style>[dir="rtl"] .nav-item__badge { left: -8px !important; right: auto !important; } [dir="ltr"]
                .nav-item__badge { right: -8px !important; left: auto !important; }</style>
                {{ $withdrawNotifications->count() }}
            </span>
        </a>
        <div class="dropdown-wrapper">
            <h2 class="dropdown-wrapper__title">{{ trans('menu.withdraw module.vendors_withdraw_requests') }} <span
                    class="badge-circle badge-warning ms-1">{{ $withdrawNotifications->count() }}</span></h2>
            <ul>
                @forelse ($withdrawNotifications as $notification)
                    <li class="nav-notification__single d-flex flex-wrap">
                        <div class="nav-notification__type nav-notification__type--{{ $notification->color }}">
                            <i class="{{ $notification->icon }}"></i>
                        </div>
                        <div class="nav-notification__details">
                            <p>
                                <a href="{{ route('admin.notifications.show', $notification->id) }}" class="subject stretched-link text-truncate"
                                    style="max-width: 180px;">{{ $notification->getTranslatedTitle() }}</a>
                            </p>
                            <p>
                                <span class="time-posted">{{ $notification->getTranslatedDescription() }}</span>
                            </p>
                            <p>
                                <span class="time-posted text-muted">{{ $notification->created_at }}</span>
                            </p>
                        </div>
                    </li>
                @empty
                    <li class="nav-notification__single d-flex flex-wrap">
                        <div class="nav-notification__details">
                            <p class="text-muted">{{ trans('menu.withdraw module.no_requests') }}</p>
                        </div>
                    </li>
                @endforelse
            </ul>
            <a href="{{ route('admin.transactionsRequests', 'new') }}" class="dropdown-wrapper__more">{{ trans('menu.withdraw module.see_all_requests') }}</a>
        </div>
    </div>
</li>
