<div class="row">
    <!-- User/Actor Information -->
    <div class="col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0"><i class="uil uil-user me-1"></i>{{ __('systemsetting::activity_log.user') }}</h6>
            </div>
            <div class="card-body">
                @if($activity_log->customer_id && $activity_log->customer)
                    <span class="badge badge-info mb-2">{{ __('dashboard.customer') }}</span>
                    <p class="mb-1 fw-bold">{{ $activity_log->customer->name ?? 'N/A' }}</p>
                    <small class="text-muted d-block">
                        <i class="uil uil-envelope me-1"></i>{{ $activity_log->customer->email ?? 'N/A' }}
                    </small>
                    @if($activity_log->customer->phone)
                        <small class="text-muted d-block">
                            <i class="uil uil-phone me-1"></i>{{ $activity_log->customer->phone }}
                        </small>
                    @endif
                    <small class="text-muted d-block">
                        <i class="uil uil-tag me-1"></i>ID: {{ $activity_log->customer_id }}
                    </small>
                @elseif($activity_log->user)
                    <span class="badge badge-primary mb-2">{{ __('common.admin') }}</span>
                    <p class="mb-1 fw-bold">{{ $activity_log->user->name ?? 'N/A' }}</p>
                    <small class="text-muted d-block">
                        <i class="uil uil-envelope me-1"></i>{{ $activity_log->user->email }}
                    </small>
                @elseif(!empty($activity_log->properties['actor']))
                    @php $actor = $activity_log->properties['actor']; @endphp
                    <span class="badge badge-{{ $actor['type'] === 'customer' ? 'info' : ($actor['type'] === 'vendor' ? 'warning' : 'secondary') }} mb-2">
                        {{ ucfirst($actor['type'] ?? 'Unknown') }}
                    </span>
                    <p class="mb-1 fw-bold">{{ $actor['name'] ?? 'N/A' }}</p>
                    @if(!empty($actor['email']))
                        <small class="text-muted d-block">
                            <i class="uil uil-envelope me-1"></i>{{ $actor['email'] }}
                        </small>
                    @endif
                    @if(!empty($actor['phone']))
                        <small class="text-muted d-block">
                            <i class="uil uil-phone me-1"></i>{{ $actor['phone'] }}
                        </small>
                    @endif
                    @if(!empty($actor['id']))
                        <small class="text-muted d-block">
                            <i class="uil uil-tag me-1"></i>ID: {{ $actor['id'] }}
                        </small>
                    @endif
                @else
                    <span class="badge badge-secondary">{{ __('common.system') }}</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Action & Model Information -->
    <div class="col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0"><i class="uil uil-bolt me-1"></i>{{ __('systemsetting::activity_log.action') }}</h6>
            </div>
            <div class="card-body">
                @php
                    $actionColors = [
                        'created' => 'success',
                        'updated' => 'warning', 
                        'deleted' => 'danger',
                        'restored' => 'info',
                        'login' => 'primary',
                        'logout' => 'secondary',
                        'api_read' => 'info',
                        'api_create' => 'success',
                        'api_update' => 'warning',
                        'api_delete' => 'danger',
                    ];
                    $actionColor = $actionColors[$activity_log->action] ?? 'secondary';
                @endphp
                <span class="badge badge-{{ $actionColor }} badge-lg mb-2">
                    {{ __("activity_log.actions.{$activity_log->action}") }}
                </span>
                <p class="mb-1">
                    <strong>{{ __('systemsetting::activity_log.model') }}:</strong>
                    <code>{{ class_basename($activity_log->model ?? 'Unknown') }}</code>
                </p>
                @if($activity_log->model_id)
                    <p class="mb-1">
                        <strong>ID:</strong> {{ $activity_log->model_id }}
                    </p>
                @endif
                <p class="mb-0 text-muted small">
                    {{ $activity_log->translated_description }}
                </p>
            </div>
        </div>
    </div>

    <!-- Technical Information -->
    <div class="col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0"><i class="uil uil-server me-1"></i>{{ __('systemsetting::activity_log.technical_information') }}</h6>
            </div>
            <div class="card-body">
                <p class="mb-1">
                    <strong>{{ __('systemsetting::activity_log.ip_address') }}:</strong>
                    <code>{{ $activity_log->ip_address ?? '-' }}</code>
                </p>
                <p class="mb-0 small text-muted" style="word-break: break-all;">
                    <strong>{{ __('systemsetting::activity_log.user_agent') }}:</strong><br>
                    {{ Str::limit($activity_log->user_agent, 100) ?? '-' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Timestamps -->
    <div class="col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0"><i class="uil uil-clock me-1"></i>{{ __('common.timestamps') }}</h6>
            </div>
            <div class="card-body">
                <p class="mb-1">
                    <strong>{{ __('common.created_at') }}:</strong>
                    {{ $activity_log->created_at }}
                </p>
                <small class="text-muted">{{ $activity_log->created_at->diffForHumans() }}</small>
            </div>
        </div>
    </div>

    <!-- Properties (if any) -->
    @if($activity_log->properties && count($activity_log->properties) > 0)
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0"><i class="uil uil-brackets-curly me-1"></i>{{ __('systemsetting::activity_log.properties') }}</h6>
                </div>
                <div class="card-body p-0">
                    <pre class="mb-0 p-3 bg-dark text-light" style="max-height: 250px; overflow-y: auto; font-size: 12px; border-radius: 0 0 0.25rem 0.25rem;">{{ json_encode($activity_log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
        </div>
    @endif
</div>
