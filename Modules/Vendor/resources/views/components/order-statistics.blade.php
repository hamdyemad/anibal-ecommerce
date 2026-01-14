@props(['orderStats'])

<div class="card card-holder mt-3">
    <div class="card-header">
        <h3>
            <i class="uil uil-chart-bar me-1"></i>{{ trans('vendor::vendor.order_statistics') }}
        </h3>
    </div>
    <div class="card-body">
        <div class="row">
            {{-- Total Orders --}}
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #5b69ff15 0%, #5b69ff05 100%);">
                    <div class="card-body text-center py-4">
                        <div class="mb-2">
                            <i class="uil uil-shopping-cart fs-1" style="color: #5b69ff;"></i>
                        </div>
                        <h3 class="mb-1 fw-bold" style="color: #5b69ff;">{{ $orderStats['total_order_products'] ?? 0 }}</h3>
                        <p class="mb-0 text-muted small">{{ trans('vendor::vendor.total_orders') }}</p>
                    </div>
                </div>
            </div>

            {{-- Dynamic Stage Cards --}}
            @if(isset($orderStats['stages']) && count($orderStats['stages']) > 0)
                @foreach($orderStats['stages'] as $stageId => $stage)
                    <div class="col-md-3 mb-3">
                        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, {{ $stage['color'] }}15 0%, {{ $stage['color'] }}05 100%);">
                            <div class="card-body text-center py-4">
                                <div class="mb-2">
                                    <i class="uil {{ $stage['icon'] }} fs-1" style="color: {{ $stage['color'] }};"></i>
                                </div>
                                <h3 class="mb-1 fw-bold" style="color: {{ $stage['color'] }};">{{ $stage['count'] }}</h3>
                                <p class="mb-0 text-muted small">{{ $stage['name'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
