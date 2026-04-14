<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ trans('order::order.new_order_email_subject', ['order_number' => $order->order_number]) }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: {{ $locale === 'ar' ? "'Segoe UI', Tahoma, Arial, sans-serif" : "'Segoe UI', Arial, sans-serif" }};
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
            padding: 20px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #2d7a3e 0%, #5cb85c 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        .header-logo {
            max-width: 150px;
            height: auto;
            margin-bottom: 20px;
        }
        .header-icon {
            width: 50px;
            height: 50px;
            background-color: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            font-size: 24px;
        }
        .header h1 {
            font-size: 24px;
            font-weight: 600;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header p {
            font-size: 14px;
            margin-top: 8px;
            opacity: 0.95;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #2d7a3e;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .message {
            font-size: 15px;
            color: #555;
            margin-bottom: 30px;
            line-height: 1.8;
        }
        .info-box {
            background: linear-gradient(135deg, #f8fff9 0%, #f0f9f1 100%);
            border-{{ $locale === 'ar' ? 'right' : 'left' }}: 4px solid #5cb85c;
            border-radius: 8px;
            padding: 25px;
            margin: 25px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e0f0e3;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-size: 14px;
            color: #666;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .info-label::before {
            content: '';
            width: 6px;
            height: 6px;
            background-color: #5cb85c;
            border-radius: 50%;
            display: inline-block;
        }
        .info-value {
            font-size: 15px;
            color: #2d7a3e;
            font-weight: 600;
        }
        .button-container {
            text-align: center;
            margin: 35px 0;
        }
        .button {
            display: inline-block;
            padding: 14px 40px;
            background: linear-gradient(135deg, #2d7a3e 0%, #5cb85c 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(45, 122, 62, 0.3);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(45, 122, 62, 0.4);
        }
        .divider {
            height: 1px;
            background: linear-gradient(to {{ $locale === 'ar' ? 'left' : 'right' }}, transparent, #e0e0e0, transparent);
            margin: 30px 0;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        .footer-message {
            font-size: 15px;
            color: #2d7a3e;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .footer-text {
            font-size: 13px;
            color: #666;
            margin-top: 10px;
        }
        .footer-logo {
            max-width: 120px;
            height: auto;
            margin-bottom: 15px;
        }
        .footer-text-logo {
            font-size: 20px;
            font-weight: 700;
            color: #2d7a3e;
            margin-bottom: 10px;
        }
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }
            .header {
                padding: 30px 20px;
            }
            .header h1 {
                font-size: 20px;
            }
            .content {
                padding: 30px 20px;
            }
            .info-box {
                padding: 20px;
            }
            .button {
                padding: 12px 30px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header with gradient -->
        <div class="header">
            @php
                // Use locale-specific logo (logo_ar.png or logo_en.png)
                $logoFileName = 'logo_' . $locale . '.png';
                $logoUrl = url('assets/img/' . $logoFileName);
            @endphp
            <img src="{{ $logoUrl }}" alt="{{ config('app.name') }}" class="header-logo">
            <h1>{{ trans('order::order.order_confirmation') }}</h1>
            <p>{{ trans('order::order.new_order_notification') }}</p>
        </div>
        
        <!-- Content -->
        <div class="content">
            <div class="greeting">
                {{ trans('order::order.dear_customer') }} {{ $order->customer_name }}
            </div>
            
            <div class="message">
                {{ trans('order::order.order_received_message') }}
            </div>
            
            <!-- Order Information Box -->
            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">{{ trans('order::order.order_number') }}</span>
                    <span class="info-value">{{ $order->order_number }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">{{ trans('order::order.order_date') }}</span>
                    <span class="info-value">
                        @if($order->created_at instanceof \Carbon\Carbon)
                            {{ $order->created_at->format('Y-m-d H:i') }}
                        @else
                            {{ $order->created_at }}
                        @endif
                    </span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">{{ trans('order::order.total_amount') }}</span>
                    <span class="info-value">{{ number_format($order->total_price, 2) }} {{ currency() }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">{{ trans('order::order.payment_method') }}</span>
                    <span class="info-value">
                        @if(trans()->has('order::order.' . $order->payment_type))
                            {{ trans('order::order.' . $order->payment_type) }}
                        @else
                            {{ ucfirst(str_replace('_', ' ', $order->payment_type)) }}
                        @endif
                    </span>
                </div>
            </div>
            
            <div class="message">
                {{ trans('order::order.order_processing_message') }}
            </div>
            
            <!-- Track Order Button -->
            <div class="button-container">
                <a href="{{ url('/api/v1/orders/track/' . $order->order_number) }}" class="button">
                    {{ trans('order::order.track_order') }}
                </a>
            </div>
            
            <div class="divider"></div>
            
            <div class="message" style="font-size: 13px; color: #888; text-align: center;">
                {{ trans('order::order.email_help_text') }}
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            @php
                // Use locale-specific logo (logo_ar.png or logo_en.png)
                $footerLogoFileName = 'logo_' . $locale . '.png';
                $footerLogoUrl = url('assets/img/' . $footerLogoFileName);
            @endphp
            <img src="{{ $footerLogoUrl }}" alt="{{ config('app.name') }}" class="footer-logo">
            <div class="footer-message">{{ trans('order::order.thank_you_message') }}</div>
            <div class="footer-text">
                &copy; {{ date('Y') }} {{ config('app.name') }}. {{ trans('order::order.all_rights_reserved') }}
            </div>
        </div>
    </div>
</body>
</html>
