<div class="col-12 mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="mb-0 fw-500">{{ trans('dashboard.orders_overview') }}</h6>
        </div>

        <div class="card-body">
            <div class="revenueSourceChart">
                <div class="parentContainer position-relative mb-4" style="height: 250px;">
                    <canvas id="ordersOverviewChart"></canvas>
                </div>

                <div class="row g-3">
                    @php
                        $icons = [
                            'new' => 'uil-shopping-bag',
                            'in_progress' => 'uil-clock',
                            'deliver' => 'uil-check-circle',
                            'cancel' => 'uil-times-circle',
                            'want_to_return' => 'uil-redo',
                            'in_progress_return' => 'uil-sync',
                            'refund' => 'uil-money-bill',
                        ];
                        $defaultIcon = 'uil-box';
                    @endphp
                    
                    @foreach($ordersOverview as $stage)
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body d-flex align-items-center p-3">
                                    <div class="icon-wrapper d-flex align-items-center justify-content-center rounded-circle me-3" 
                                         style="background-color: {{ $stage['color'] }}25; width: 48px; height: 48px; min-width: 48px;">
                                        <i class="uil {{ $icons[$stage['type']] ?? $defaultIcon }} fs-4" style="color: {{ $stage['color'] }};"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted mb-1 small">{{ $stage['name'] }}</p>
                                        <h5 class="mb-0 fw-bold">{{ $stage['count'] }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
