@extends('layout.app')
@section('title')
    {{ trans('order::order.order_details') }} | Bnaia
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    ['title' => trans('order::order.order_management'), 'url' => route('admin.orders.index')],
                    ['title' => trans('order::order.order_details')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div id="printableArea" class="bg-white p-40 radius-xl">
                    <!-- Order Info & Customer Details with QR Code -->
                    <div class="row mb-40">
                        <div class="col-md-6">
                            <div class="mb-20">
                                <p class="mb-5"><span class="text-primary fw-bold">{{ trans('order::order.order_id') }}:</span> <span class="text-primary fw-bold">{{ $order->order_number }}</span></p>
                                <p class="mb-5"><span class="text-primary fw-bold">{{ trans('order::order.created_at') }}:</span> {{ $order->created_at->format('d M, Y, H:i A') }}</p>
                                <p class="mb-5"><span class="text-primary fw-bold">{{ trans('order::order.stage') }}:</span> <span class="badge" style="background: {{ $order->stage?->color ?? '#6c757d' }}; color: white">{{ $order->stage?->name ?? 'N/A' }}</span></p>
                                <p class="mb-0"><span class="text-primary fw-bold">{{ trans('order::order.order_from') }}:</span> 
                                    @if($order->order_from === 'web')
                                        {{ trans('order::order.web') }}
                                    @elseif($order->order_from === 'ios')
                                        {{ trans('order::order.ios') }}
                                    @elseif($order->order_from === 'android')
                                        {{ trans('order::order.android') }}
                                    @else
                                        {{ $order->order_from }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="{{ app()->getLocale() === 'en' ? 'col-md-3' : 'col-md-6' }} {{ app()->getLocale() === 'en' ? '' : 'text-end' }}">
                            <p class="mb-5"><span class="fw-bold">{{ trans('order::order.customer_name') }}:</span> {{ $order->customer_name }}</p>
                            <p class="mb-5"><span class="fw-bold">{{ trans('order::order.customer_phone') }}:</span> {{ $order->customer_phone }}</p>
                            <p class="mb-5"><span class="fw-bold">{{ trans('order::order.customer_email') }}:</span> {{ $order->customer_email }}</p>
                            <p class="mb-0"><span class="fw-bold">{{ trans('order::order.customer_address') }}:</span> {{ $order->customer_address }}</p>
                        </div>
                        <div class="col-md-3 text-end">
                            <div class="d-flex justify-content-end">
                                <div style="width: 120px; height: 120px; background: #f5f5f5; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode('https://bnaia.com/ar/profile/info') }}" alt="QR Code" style="width: 100%; height: 100%; border-radius: 8px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Products Table -->
                    <div class="mb-40">
                        <div class="table-responsive">
                            <table class="table mb-0 table-hover" style="border-color: #dee2e6;">
                                <thead class="userDatatable-header" style="background-color: #003d82; color: white;">
                                    <tr>
                                        <th class="text-white fw-bold">#</th>
                                        <th class="text-white fw-bold">{{ trans('order::order.product') }}</th>
                                        <th class="text-white fw-bold text-end">{{ trans('order::order.price_per_unit') }}</th>
                                        <th class="text-white fw-bold text-center">{{ trans('order::order.quantity') }}</th>
                                        <th class="text-white fw-bold text-end">{{ trans('order::order.total_price') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($order->products as $key => $product)
                                        <tr>
                                            <td class="fw-bold">{{ $key + 1 }}</td>
                                            <td>
                                                <p class="mb-5 fw-bold">{{ $product->getTranslation('name', app()->getLocale()) ?? 'N/A' }}</p>
                                                <small class="text-muted">SKU: {{ $product->vendorProduct?->sku ?? 'N/A' }}</small>
                                                @if($product->taxes->count() > 0)
                                                    <br>
                                                    @foreach($product->taxes as $tax)
                                                        <small class="text-muted">{{ $tax->getTranslation('tax_title', app()->getLocale()) ?? 'N/A' }}: {{ $tax->percentage }}%</small><br>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td class="text-end">{{ number_format($product->price, 2) }} {{ __('common.currency') }}</td>
                                            <td class="text-center">{{ $product->quantity }}</td>
                                            <td class="text-end fw-bold">{{ number_format($product->price * $product->quantity, 2) }} {{ __('common.currency') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-20">
                                                {{ trans('common.no_data') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Fees & Discounts Details -->
                    @if($order->extraFeesDiscounts->count() > 0)
                        <div class="mb-40">
                            <div class="table-responsive">
                                <table class="table mb-0 table-hover" style="border-color: #dee2e6;">
                                    <thead class="userDatatable-header" style="background-color: #003d82; color: white;">
                                        <tr>
                                            <th class="text-white fw-bold">{{ trans('order::order.type') }}</th>
                                            <th class="text-white fw-bold">{{ trans('order::order.reason') }}</th>
                                            <th class="text-white fw-bold text-end">{{ trans('order::order.amount') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->extraFeesDiscounts as $extra)
                                            <tr>
                                                <td>
                                                    @if($extra->type === 'fee')
                                                        <span class="badge bg-danger">{{ trans('order::order.fee') }}</span>
                                                    @else
                                                        <span class="badge bg-success">{{ trans('order::order.discount') }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $extra->reason }}</td>
                                                <td class="text-end fw-bold">
                                                    {{ $extra->type === 'fee' ? '+' : '-' }}{{ number_format($extra->cost, 2) }} {{ __('common.currency') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Summary Section -->
                    <div class="row mb-40">
                        <div class="col-md-6">
                            @if($order->country || $order->city || $order->region)
                                <div class="p-20 radius-md">
                                    <h6 class="fw-bold mb-15">{{ trans('order::order.location') }}</h6>
                                    <p class="mb-5"><span class="fw-bold">{{ trans('order::order.country') }}:</span> {{ $order->country?->name ?? 'N/A' }}</p>
                                    <p class="mb-5"><span class="fw-bold">{{ trans('order::order.city') }}:</span> {{ $order->city?->name ?? 'N/A' }}</p>
                                    <p class="mb-0"><span class="fw-bold">{{ trans('order::order.region') }}:</span> {{ $order->region?->name ?? 'N/A' }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="bg-primary text-white p-20 radius-md">
                                <div class="d-flex justify-content-between mb-10">
                                    <span>{{ trans('order::order.subtotal') }} :</span>
                                    <span class="fw-bold">{{ number_format($order->total_product_price, 2) }} {{ __('common.currency') }}</span>
                                </div>
                                @if($order->total_discounts > 0)
                                    <div class="d-flex justify-content-between mb-10">
                                        <span>{{ trans('order::order.discounts') }} :</span>
                                        <span class="fw-bold">{{ number_format($order->total_discounts, 2) }} {{ __('common.currency') }}</span>
                                    </div>
                                @endif
                                @if($order->total_fees > 0)
                                    <div class="d-flex justify-content-between mb-10">
                                        <span>{{ trans('order::order.fees') }} :</span>
                                        <span class="fw-bold">+{{ number_format($order->total_fees, 2) }} {{ __('common.currency') }}</span>
                                    </div>
                                @endif
                                <div class="d-flex justify-content-between mb-10">
                                    <span>{{ trans('order::order.shipping') }} :</span>
                                    <span class="fw-bold">+{{ number_format($order->shipping, 2) }} {{ __('common.currency') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-10">
                                    <span>{{ trans('order::order.tax') }} :</span>
                                    <span class="fw-bold">+{{ number_format($order->total_tax, 2) }} {{ __('common.currency') }}</span>
                                </div>
                                <hr class="bg-white my-10">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold fs-16">{{ trans('order::order.total') }} :</span>
                                    <span class="fw-bold fs-16">{{ number_format($order->total_price, 2) }} {{ __('common.currency') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons Section (Not Printable) -->
                <div class="mt-30 mb-40 no-print">
                    <div class="bg-white p-20 radius-xl global-shadow border-light-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-500">{{ trans('order::order.actions') }}</h6>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="uil uil-arrow-left me-2"></i>{{ trans('common.back') }}
                                </a>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#changeStageModal">
                                    <i class="uil uil-check-circle me-2"></i>{{ trans('order::order.change_order_stage') }}
                                </button>
                                <button class="btn btn-info btn-sm" onclick="printInvoice()">
                                    <i class="uil uil-print me-2"></i>{{ trans('order::order.print_invoice') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Change Stage Modal Component -->
    <x-order::change-stage-modal :order-id="$order->id" :current-stage-id="$order->stage_id" />

    {{-- <style>
        .no-print {
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }

        @media print {
            /* Hide sidebar and header */
            .sidebar,
            .header,
            .navbar,
            .no-print,
            .breadcrumb {
                display: none !important;
            }

            /* Show only printable area */
            body {
                margin: 0;
                padding: 0;
                background: white;
                font-size: 12px;
            }

            .container-fluid {
                margin: 0;
                padding: 20px;
                max-width: 100%;
            }

            #printableArea {
                padding: 20px !important;
                background: white !important;
                box-shadow: none !important;
                border: none !important;
            }

            /* Header styling */
            .row.mb-40 {
                page-break-inside: avoid;
                margin-bottom: 20px;
                border-bottom: 2px solid #003d82;
                padding-bottom: 15px;
            }

            /* Table styling for print */
            .table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
                page-break-inside: avoid;
            }

            .table thead {
                background-color: #003d82 !important;
                color: white !important;
                page-break-inside: avoid;
            }

            .table th {
                padding: 10px !important;
                border: 1px solid #ddd !important;
                font-weight: bold !important;
                color: white !important;
            }

            .table td {
                padding: 8px !important;
                border: 1px solid #ddd !important;
            }

            .table tbody tr {
                page-break-inside: avoid;
            }

            .table tbody tr:nth-child(even) {
                background-color: #f9f9f9 !important;
            }

            /* Summary section */
            .bg-primary {
                background-color: #003d82 !important;
                color: white !important;
            }

            /* Badge styling */
            .badge {
                padding: 4px 8px !important;
                font-size: 11px !important;
            }

            /* Text utilities */
            .text-end {
                text-align: right;
            }

            .text-center {
                text-align: center;
            }

            .fw-bold {
                font-weight: bold;
            }

            /* Prevent page breaks in important sections */
            .userDatatable {
                box-shadow: none !important;
                border: none !important;
                page-break-inside: avoid;
            }

            .row {
                page-break-inside: avoid;
            }

            hr {
                border: 1px solid #ddd;
                page-break-inside: avoid;
            }

            /* QR Code styling */
            .col-md-3 {
                page-break-inside: avoid;
            }

            /* Ensure proper margins */
            @page {
                margin: 10mm;
            }
        }
    </style> --}}

    <style>
    .no-print {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }

    @media print {
        /* Hide non-printable elements */
        .sidebar,
        .header,
        .navbar,
        .no-print,
        .breadcrumb,
        nav,
        .btn,
        button,
        footer {
            display: none !important;
        }

        /* Reset body and container */
        body * {
            visibility: hidden;
        }

        #printableArea,
        #printableArea * {
            visibility: visible;
        }

        body {
            margin: 0 !important;
            padding: 0 !important;
            background: white !important;
            font-size: 12pt;
        }

        .container-fluid {
            margin: 0 !important;
            padding: 15mm !important;
            max-width: 100% !important;
            width: 100% !important;
        }

        #printableArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 20px !important;
            background: white !important;
            box-shadow: none !important;
            border: none !important;
            margin: 0 !important;
        }

        /* Header section - Order Info & Customer Details */
        .row.mb-40 {
            page-break-inside: avoid;
            margin-bottom: 15pt !important;
            border-bottom: 2pt solid #003d82 !important;
            padding-bottom: 10pt !important;
            display: block !important;
        }

        /* Fix column layout for print */
        .row {
            display: block !important;
            page-break-inside: avoid;
        }

        .col-md-6,
        .col-md-3 {
            width: 100% !important;
            float: none !important;
            display: inline-block !important;
            vertical-align: top;
        }

        .col-md-6 {
            width: 45% !important;
        }

        .col-md-3 {
            width: 20% !important;
        }

        /* Text styling */
        p {
            margin-bottom: 5pt !important;
            line-height: 1.4 !important;
        }

        .text-primary {
            color: #003d82 !important;
        }

        .fw-bold {
            font-weight: bold !important;
        }

        /* QR Code container */
        .text-end {
            text-align: right !important;
        }

        /* Table styling */
        .table-responsive {
            width: 100% !important;
            overflow: visible !important;
        }

        .table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin-bottom: 15pt !important;
            page-break-inside: auto;
        }

        .table thead {
            background-color: #003d82 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            color: white !important;
            page-break-inside: avoid;
            page-break-after: avoid;
        }

        .table th {
            padding: 8pt 6pt !important;
            border: 1pt solid #003d82 !important;
            font-weight: bold !important;
            color: white !important;
            background-color: #003d82 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .table td {
            padding: 6pt !important;
            border: 1pt solid #ddd !important;
            line-height: 1.3 !important;
        }

        .table tbody tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        .table tbody tr:nth-child(even) {
            background-color: #f9f9f9 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Badge styling */
        .badge {
            padding: 3pt 6pt !important;
            font-size: 9pt !important;
            border-radius: 3pt !important;
            display: inline-block !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .bg-primary {
            background-color: #003d82 !important;
            color: white !important;
        }

        .bg-danger {
            background-color: #dc3545 !important;
            color: white !important;
        }

        .bg-success {
            background-color: #28a745 !important;
            color: white !important;
        }

        /* Summary section */
        .bg-primary.p-20 {
            background-color: #003d82 !important;
            color: white !important;
            padding: 15pt !important;
            border-radius: 5pt !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .bg-primary * {
            color: white !important;
        }

        /* Location box */
        .p-20.radius-md {
            padding: 15pt !important;
            border: 1pt solid #ddd !important;
            border-radius: 5pt !important;
        }

        /* Summary layout fix */
        .mb-40 .col-md-6:first-child {
            width: 48% !important;
            display: inline-block !important;
            vertical-align: top;
        }

        .mb-40 .col-md-6:last-child {
            width: 48% !important;
            display: inline-block !important;
            vertical-align: top;
            float: right;
        }

        /* Horizontal rule */
        hr {
            border: 0 !important;
            border-top: 1pt solid white !important;
            margin: 8pt 0 !important;
            page-break-inside: avoid;
        }

        hr.bg-white {
            opacity: 0.3 !important;
        }

        /* Flex utilities for print */
        .d-flex {
            display: flex !important;
        }

        .justify-content-between {
            justify-content: space-between !important;
        }

        .justify-content-end {
            justify-content: flex-end !important;
        }

        .align-items-center {
            align-items: center !important;
        }

        .mb-10 {
            margin-bottom: 8pt !important;
        }

        .mb-15 {
            margin-bottom: 10pt !important;
        }

        .mb-5 {
            margin-bottom: 4pt !important;
        }

        .mb-0 {
            margin-bottom: 0 !important;
        }

        /* Font sizes */
        .fs-16 {
            font-size: 14pt !important;
        }

        h6 {
            font-size: 12pt !important;
            margin-bottom: 10pt !important;
        }

        small {
            font-size: 9pt !important;
        }

        /* Text utilities */
        .text-center {
            text-align: center !important;
        }

        .text-muted {
            color: #6c757d !important;
        }

        /* Page settings */
        @page {
            size: A4;
            margin: 15mm 10mm;
        }

        /* Prevent orphans and widows */
        p, h1, h2, h3, h4, h5, h6 {
            orphans: 3;
            widows: 3;
        }

        /* Color adjustment for all colored elements */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>

    <script>
        function printInvoice() {
            // Get order number for filename
            const orderNumber = '{{ $order->order_number }}';
            
            // Set document title for print dialog
            const originalTitle = document.title;
            document.title = `Invoice_${orderNumber}`;
            
            // Trigger print dialog
            window.print();
            
            // Restore original title after print dialog closes
            setTimeout(() => {
                document.title = originalTitle;
            }, 100);
        }
    </script>
@endsection
