@extends('layout.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">{{ trans('menu.notification_details') }}</h4>
                    <div class="breadcrumb-action justify-content-center flex-wrap">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">
                                        <i class="uil uil-estate"></i>{{ trans('dashboard.title') }}
                                    </a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">{{ trans('menu.notification_details') }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card card-default card-md mb-4">
                    <div class="card-header">
                        <h6>{{ trans('menu.notification_details') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="notification-detail-wrapper">
                            <!-- Notification Icon and Type -->
                            <div class="d-flex align-items-center mb-4">
                                <div class="notification-icon-large me-3" style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background-color: {{ 
                                    $notification->color === 'primary' ? '#5f63f2' : 
                                    ($notification->color === 'success' ? '#20c997' : 
                                    ($notification->color === 'warning' ? '#fa8b0c' : 
                                    ($notification->color === 'danger' ? '#ff4d4f' : 
                                    ($notification->color === 'info' ? '#01b8ff' : '#8231d3'))))
                                }};">
                                    <i class="{{ $notification->icon }}" style="font-size: 28px; color: white;"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1">{{ $notification->getTranslatedTitle() }}</h5>
                                    @php
                                        $typeLabel = match($notification->type) {
                                            'vendor_request' => trans('menu.become a vendor requests.title'),
                                            'new_order' => trans('menu.order'),
                                            'new_message' => trans('menu.messages'),
                                            'withdraw_request' => trans('menu.withdraw module.title'),
                                            'withdraw_status' => trans('menu.withdraw module.title'),
                                            'new_refund_request' => trans('refund::refund.refund_requests'),
                                            'refund_status_changed' => trans('refund::refund.refund_requests'),
                                            default => ucfirst(str_replace('_', ' ', $notification->type))
                                        };
                                        
                                        $badgeColor = match($notification->color) {
                                            'primary' => '#5f63f2',
                                            'success' => '#20c997',
                                            'warning' => '#fa8b0c',
                                            'danger' => '#ff4d4f',
                                            'info' => '#01b8ff',
                                            default => '#8231d3'
                                        };
                                    @endphp
                                    <x-protected-badge 
                                        :text="$typeLabel" 
                                        :color="$badgeColor" 
                                        size="lg" 
                                    />
                                </div>
                            </div>

                            <!-- Notification Description -->
                            <div class="mb-4">
                                <h6 class="mb-2">{{ trans('common.description') }}</h6>
                                <p class="text-muted">{{ $notification->getTranslatedDescription() }}</p>
                            </div>

                            <!-- Notification Time -->
                            <div class="mb-4">
                                <h6 class="mb-2">{{ trans('common.created_at') }}</h6>
                                <p class="text-muted">
                                    <i class="uil uil-clock"></i> {{ $notification->created_at }}
                                </p>
                            </div>

                            <!-- Notification Data -->
                            @if($notification->data && count($notification->data) > 0)
                                <div class="mb-4">
                                    <h6 class="mb-3">{{ trans('common.details') }}</h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <tbody>
                                                @foreach($notification->data as $key => $value)
                                                    @php
                                                        // Check if key is a translation key (contains dots)
                                                        if (str_contains($key, '.') && trans($key) !== $key) {
                                                            $label = trans($key);
                                                        } else {
                                                            // Fallback for non-translation keys
                                                            $label = ucfirst(str_replace('_', ' ', $key));
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td class="fw-bold" style="width: 30%;">{{ $label }}</td>
                                                        <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="d-flex gap-2 mt-4">
                                @if($notification->url && $notification->url !== '#')
                                    <a href="{{ $notification->url }}" class="btn btn-primary">
                                        <i class="uil uil-arrow-right"></i> {{ trans('common.go_to_details') }}
                                    </a>
                                @endif
                                <a href="{{ url()->previous() }}" class="btn btn-light">
                                    <i class="uil uil-arrow-left"></i> {{ trans('common.back') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
