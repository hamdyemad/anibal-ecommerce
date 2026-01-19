@php
    // Get vendor request notifications from admin_notifications table
    $vendorRequestNotifications = \App\Models\AdminNotification::notViewedBy(auth()->id())
        ->where('type', 'vendor_request')
        ->whereNull('vendor_id')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    
    $pendingCount = $vendorRequestNotifications->count();
@endphp

<li class="nav-notification">
    <div class="dropdown-custom" style="position: relative;">
        <a href="javascript:;" class="nav-item-toggle icon-active">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="svg">
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="8.5" cy="7" r="4"></circle>
                <line x1="20" y1="8" x2="20" y2="14"></line>
                <line x1="23" y1="11" x2="17" y2="11"></line>
            </svg>
            @if($pendingCount > 0)
                <span class="nav-item__badge" style="position: absolute; top: -8px; background-color: #01b8ff; color: white; border-radius: 50%; width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 600; line-height: 1; z-index: 10;">{{ $pendingCount }}</span>
            @endif
        </a>
        <div class="dropdown-wrapper">
            <h2 class="dropdown-wrapper__title">{{ trans('menu.become a vendor requests.pending') }} <span class="badge-circle badge-info ms-1">{{ $pendingCount }}</span></h2>
            <ul>
                @forelse($vendorRequestNotifications as $notification)
                    <li class="nav-notification__single d-flex flex-wrap">
                        <div class="nav-notification__type nav-notification__type--{{ $notification->color }}">
                            <i class="{{ $notification->icon }}"></i>
                        </div>
                        <div class="nav-notification__details">
                            <p>
                                <a href="{{ route('admin.notifications.show', $notification->id) }}" class="subject stretched-link text-truncate" style="max-width: 180px;">{{ $notification->getTranslatedTitle() }}</a>
                                <span>{{ $notification->getTranslatedDescription() }}</span>
                            </p>
                            <p>
                                <span class="time-posted">{{ $notification->created_at }}</span>
                            </p>
                        </div>
                    </li>
                @empty
                    <li class="nav-notification__single d-flex flex-wrap">
                        <div class="nav-notification__details">
                            <p class="text-muted">{{ trans('menu.become a vendor requests.no_pending') }}</p>
                        </div>
                    </li>
                @endforelse
            </ul>
            <a href="{{ route('admin.vendor-requests.index') }}" class="dropdown-wrapper__more">{{ trans('menu.become a vendor requests.see_all') }}</a>
        </div>
    </div>
</li>
