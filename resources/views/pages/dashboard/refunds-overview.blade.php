@if(isset($stats['refunds']))
<div class="col-xl-6 col-lg-12 mb-30">
    <div class="card chart-card">
        <div class="card-body fw-bold">
            <h5 class="header-title pb-2 mt-0" style="font-weight: bold; font-size: 1.1rem; color: #495057; margin-bottom: 1rem;">
                {{ trans('dashboard.refunds_this_month') }}
                ( {{ $stats['refunds']['month']['period'] ?? date('m-Y') }} )
            </h5>

            <div class="text-center mb-3" style="font-size: 0.75rem; color: #555;">
                {{ trans('dashboard.total_refunds') }}: <span style="color: #dc3545; font-weight: 600;">{{ $stats['refunds']['month']['count'] ?? 0 }}</span> |
                {{ trans('dashboard.refunded_amount') }}: <span style="color: #dc3545; font-weight: 600;">{{ number_format($stats['refunds']['month']['amount'] ?? 0, 1) }}</span>
                {{ currency() }} |
                {{ trans('dashboard.products_refunded') }}: <span style="color: #6c757d; font-weight: 600;">{{ $stats['refunds']['month']['products_count'] ?? 0 }}</span>
            </div>

            <canvas id="monthlyRefundsChart"
                style="max-height: 300px; display: block; box-sizing: border-box; height: 300px;"></canvas>

            <div class="text-center mt-3" style="font-size: 0.9rem;">
                <span style="margin-right: 20px;">
                    <span
                        style="display: inline-block; width: 12px; height: 12px; background-color: #dc3545; border-radius: 50%; margin-right: 5px;"></span>
                    {{ trans('dashboard.refunded_amount') }}
                </span>
                <span>
                    <span
                        style="display: inline-block; width: 12px; height: 12px; background-color: #6c757d; border-radius: 50%; margin-right: 5px;"></span>
                    {{ trans('dashboard.refunds_count') }}
                </span>
            </div>
        </div>
    </div>
</div>

<div class="col-xl-6 col-lg-12 mb-30">
    <div class="card chart-card">
        <div class="card-body">
            <h5 class="header-title pb-2 mt-0" style="font-weight: bold; font-size: 1.1rem; color: #495057; margin-bottom: 1rem;">
                {{ trans('dashboard.refunds_this_year') }} ( {{ $stats['refunds']['year']['period'] ?? date('Y') }} )
            </h5>

            <div class="text-center mb-3" style="font-size: 0.75rem; color: #555;">
                {{ trans('dashboard.total_refunds') }}: <span style="color: #dc3545; font-weight: 600;">{{ $stats['refunds']['year']['count'] ?? 0 }}</span> |
                {{ trans('dashboard.refunded_amount') }}: <span style="color: #dc3545; font-weight: 600;">{{ number_format($stats['refunds']['year']['amount'] ?? 0, 1) }}</span>
                {{ currency() }} |
                {{ trans('dashboard.products_refunded') }}: <span style="color: #6c757d; font-weight: 600;">{{ $stats['refunds']['year']['products_count'] ?? 0 }}</span>
            </div>

            <canvas id="yearlyRefundsChart"
                style="max-height: 300px; display: block; box-sizing: border-box; height: 300px;"></canvas>

            <div class="text-center mt-3" style="font-size: 0.9rem;">
                <span style="margin-right: 20px;">
                    <span
                        style="display: inline-block; width: 12px; height: 12px; background-color: #dc3545; border-radius: 50%; margin-right: 5px;"></span>
                    {{ trans('dashboard.refunded_amount') }}
                </span>
                <span>
                    <span
                        style="display: inline-block; width: 12px; height: 12px; background-color: #6c757d; border-radius: 50%; margin-right: 5px;"></span>
                    {{ trans('dashboard.refunds_count') }}
                </span>
            </div>
        </div>
    </div>
</div>
@endif
