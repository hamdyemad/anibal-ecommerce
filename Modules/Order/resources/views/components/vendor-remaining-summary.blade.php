@props([
    'vendorName' => 'Vendor',
    'subtotalBeforeTax' => 0,
    'taxAmount' => 0,
    'subtotalWithTax' => 0,
    'shipping' => 0,
    'total' => 0,
    'commissionPercentage' => 0,
    'commissionAmount' => 0,
    'remaining' => 0,
    'promoDiscount' => 0,
    'colors' => ['#28a745', '#5dd879']
])

<div class="card border-0 shadow-sm h-100"
    style="background: linear-gradient(135deg, {{ $colors[0] }} 0%, {{ $colors[1] }} 100%); color: white;">
    <div class="card-body">
        <h6 class="card-title fw-bold mb-20 d-flex align-items-center text-white">
            <i class="uil uil-store me-2" style="font-size: 20px;"></i>
            {{ $vendorName }} {{ trans('order::order.remaining_summary') }}
        </h6>
        <div class="summary-details">
            <div class="summary-row mb-12">
                <span class="fw-bold">{{ trans('order::order.subtotal') }}</span>
                <span class="fw-bold">{{ number_format($subtotalBeforeTax, 2) }} {{ currency() }}</span>
            </div>
            <div class="summary-row mb-12">
                <span class="fw-bold">{{ trans('order::order.taxes_price') }}</span>
                <span class="fw-bold">+{{ number_format($taxAmount, 2) }} {{ currency() }}</span>
            </div>
            <div class="summary-row mb-12">
                <span class="fw-bold">{{ trans('order::order.subtotal_including_tax') }}</span>
                <span class="fw-bold">{{ number_format($subtotalWithTax, 2) }} {{ currency() }}</span>
            </div>
            @if ($promoDiscount > 0)
                <div class="summary-row mb-12">
                    <span class="fw-bold">{{ trans('order::order.promo_discount') }}</span>
                    <span class="fw-bold" style="color: #ffcccc;">-{{ number_format($promoDiscount, 2) }} {{ currency() }}</span>
                </div>
            @endif
            @if ($shipping > 0)
                <div class="summary-row mb-12">
                    <span class="fw-bold">{{ trans('order::order.shipping') }}</span>
                    <span class="fw-bold">+{{ number_format($shipping, 2) }} {{ currency() }}</span>
                </div>
            @endif
            <div class="summary-row mb-12">
                <span class="fw-bold">{{ trans('order::order.total') }}</span>
                <span class="fw-bold">{{ number_format($total, 2) }} {{ currency() }}</span>
            </div>
            <div class="summary-row mb-12">
                <span class="fw-bold">{{ trans('order::order.bnaia_commission') }}</span>
                <span class="fw-bold" style="color: #ffcccc;">({{ $commissionPercentage }}%) -{{ number_format($commissionAmount, 2) }} {{ currency() }}</span>
            </div>
            <hr style="border-color: rgba(255,255,255,0.3); margin: 15px 0;">
            <div class="summary-row" style="font-size: 18px;">
                <span class="fw-bold">{{ trans('order::order.remaining') }}</span>
                <span class="fw-bold" style="color: #ffffcc;">{{ number_format($remaining, 2) }} {{ currency() }}</span>
            </div>
        </div>
    </div>
</div>

<style>
    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        font-size: 14px;
    }
</style>
