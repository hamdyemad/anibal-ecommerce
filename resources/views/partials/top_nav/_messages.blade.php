@if(isAdmin())
<li class="nav-message">
    <div class="dropdown-custom" style="position: relative;">
        <a href="javascript:;" class="nav-item-toggle icon-active">
            <img class="svg" src="{{ asset('assets/img/svg/message.svg') }}" alt="img">
            @php
                $unreadMessagesCount = \Modules\SystemSetting\app\Models\Message::where('status', 'pending')->count();
                $latestMessages = \Modules\SystemSetting\app\Models\Message::where('status', 'pending')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
            @endphp
            @if($unreadMessagesCount > 0)
                <span class="nav-item__badge" style="position: absolute; top: -8px; background-color: #20c997; color: white; border-radius: 50%; width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 600; line-height: 1; z-index: 10;">{{ $unreadMessagesCount }}</span>
            @endif
        </a>
        <div class="dropdown-wrapper">
            <h2 class="dropdown-wrapper__title">{{ trans('menu.messages') }} <span class="badge-circle badge-success ms-1">{{ $unreadMessagesCount }}</span></h2>
            @if($unreadMessagesCount > 0)
                <ul>
                    @foreach($latestMessages as $message)
                        <li class="author-online has-new-message">
                            <div class="user-avater">
                                <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #5f63f2; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 16px;">
                                    {{ strtoupper(substr($message->name, 0, 1)) }}
                                </div>
                            </div>
                            <div class="user-message">
                                <p>
                                    <a href="{{ route('admin.messages.show', $message->id) }}" class="subject stretched-link text-truncate" style="max-width: 180px;">{{ $message->name }}</a>
                                </p>
                                <p>
                                    <span class="desc text-truncate" style="max-width: 215px;">{{ $message->title }}</span>
                                </p>
                                <p>
                                    <span class="time-posted">{{ \Carbon\Carbon::parse($message->getRawOriginal('created_at'))->diffForHumans() }}</span>
                                </p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-center py-4">
                    <p class="text-muted">{{ trans('menu.no_messages') }}</p>
                </div>
            @endif
            <a href="{{ route('admin.messages.index') }}" class="dropdown-wrapper__more">{{ trans('menu.see_all_messages') }}</a>
        </div>
    </div>
</li>
@endif
