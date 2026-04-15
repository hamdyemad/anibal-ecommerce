@php
    $locale = app()->getLocale();
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ trans('order::order.stage_updated_subject', ['order_number' => $order->order_number]) }}</title>
</head>
<body style="margin: 0; padding: 20px; font-family: {{ $locale === 'ar' ? "'Segoe UI', Tahoma, Arial, sans-serif" : "'Segoe UI', Arial, sans-serif" }}; line-height: 1.6; color: #333; background-color: #f5f5f5;">
    <table role="presentation" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1);" width="100%" cellspacing="0" cellpadding="0" border="0">
        <!-- Header with gradient -->
        <tr>
            <td style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); background-color: #007bff; padding: 40px 30px; text-align: center; color: white;">
                @php
                    // Use locale-specific logo (logo_ar.png or logo_en.png)
                    $logoFileName = 'logo_' . $locale . '.png';
                    $logoUrl = url('assets/img/' . $logoFileName);
                @endphp
                <img src="{{ $logoUrl }}" alt="{{ config('app.name') }}" style="max-width: 150px; height: auto; margin-bottom: 20px; display: block; margin-left: auto; margin-right: auto;">
                <h1 style="font-size: 24px; font-weight: 600; margin: 0; color: white; text-shadow: 0 2px 4px rgba(0,0,0,0.1);">{{ trans('order::order.order_status_updated') }}</h1>
                <p style="font-size: 14px; margin-top: 8px; opacity: 0.95; color: white;">{{ trans('order::order.order_status_change_notification') }}</p>
            </td>
        </tr>
        
        <!-- Content -->
        <tr>
            <td style="padding: 40px 30px;">
                <div style="font-size: 18px; color: #007bff; margin-bottom: 20px; font-weight: 600;">
                    {{ trans('order::order.dear_customer') }} {{ $order->customer_name }}
                </div>
                
                <div style="font-size: 15px; color: #555; margin-bottom: 30px; line-height: 1.8;">
                    {{ trans('order::order.order_status_changed_message', ['order_number' => $order->order_number]) }}
                </div>
                
                <!-- New Status Badge -->
                <div style="text-align: center; margin: 30px 0;">
                    <div style="display: inline-block; padding: 15px 30px; background: {{ $newStage->color ?? '#28a745' }}; color: white; border-radius: 50px; font-size: 18px; font-weight: 600; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);">
                        {{ $newStage->getTranslation('name', $locale) }}
                    </div>
                </div>
                
                <!-- Order Information Box -->
                <table role="presentation" style="background: linear-gradient(135deg, #f8f9ff 0%, #f0f4ff 100%); background-color: #f8f9ff; border-{{ $locale === 'ar' ? 'right' : 'left' }}: 4px solid #007bff; border-radius: 8px; padding: 25px; margin: 25px 0; width: 100%;" width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td style="padding: 12px 0; border-bottom: 1px solid #e0e8f0;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="font-size: 14px; color: #666;">
                                        <span style="display: inline-block; width: 6px; height: 6px; background-color: #007bff; border-radius: 50%; margin-{{ $locale === 'ar' ? 'left' : 'right' }}: 8px;"></span>
                                        {{ trans('order::order.order_number') }}
                                    </td>
                                    <td style="font-size: 15px; color: #007bff; font-weight: 600; text-align: {{ $locale === 'ar' ? 'left' : 'right' }};">
                                        {{ $order->order_number }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <tr>
                        <td style="padding: 12px 0; border-bottom: 1px solid #e0e8f0;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="font-size: 14px; color: #666;">
                                        <span style="display: inline-block; width: 6px; height: 6px; background-color: {{ $newStage->color ?? '#007bff' }}; border-radius: 50%; margin-{{ $locale === 'ar' ? 'left' : 'right' }}: 8px;"></span>
                                        {{ trans('order::order.current_stage') }}
                                    </td>
                                    <td style="font-size: 15px; color: {{ $newStage->color ?? '#28a745' }}; font-weight: 600; text-align: {{ $locale === 'ar' ? 'left' : 'right' }};">
                                        {{ $newStage->getTranslation('name', $locale) }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <tr>
                        <td style="padding: 12px 0; border-bottom: 1px solid #e0e8f0;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="font-size: 14px; color: #666;">
                                        <span style="display: inline-block; width: 6px; height: 6px; background-color: #007bff; border-radius: 50%; margin-{{ $locale === 'ar' ? 'left' : 'right' }}: 8px;"></span>
                                        {{ trans('order::order.order_date') }}
                                    </td>
                                    <td style="font-size: 15px; color: #007bff; font-weight: 600; text-align: {{ $locale === 'ar' ? 'left' : 'right' }};">
                                        @if($order->created_at instanceof \Carbon\Carbon)
                                            {{ $order->created_at->format('d M Y, H:i') }}
                                        @else
                                            {{ $order->created_at }}
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <tr>
                        <td style="padding: 12px 0;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="font-size: 14px; color: #666;">
                                        <span style="display: inline-block; width: 6px; height: 6px; background-color: #007bff; border-radius: 50%; margin-{{ $locale === 'ar' ? 'left' : 'right' }}: 8px;"></span>
                                        {{ trans('order::order.total_amount') }}
                                    </td>
                                    <td style="font-size: 15px; color: #007bff; font-weight: 600; text-align: {{ $locale === 'ar' ? 'left' : 'right' }};">
                                        {{ number_format($order->total_price, 2) }} {{ currency() }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                
                <div style="font-size: 15px; color: #555; margin-bottom: 30px; line-height: 1.8;">
                    {{ trans('order::order.order_status_update_info') }}
                </div>
                
                <!-- Track Order Button -->
                <div style="text-align: center; margin: 35px 0;">
                    <a href="{{ url('/api/v1/orders/track/' . $order->order_number) }}" style="display: inline-block; padding: 14px 40px; background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); background-color: #007bff; color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);">
                        {{ trans('order::order.track_order') }}
                    </a>
                </div>
                
                <div style="height: 1px; background: linear-gradient(to {{ $locale === 'ar' ? 'left' : 'right' }}, transparent, #e0e0e0, transparent); background-color: #e0e0e0; margin: 30px 0;"></div>
                
                <div style="font-size: 13px; color: #888; text-align: center;">
                    {{ trans('order::order.email_help_text') }}
                </div>
            </td>
        </tr>
        
        <!-- Footer -->
        <tr>
            <td style="background-color: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #e9ecef;">
                @php
                    // Use locale-specific logo (logo_ar.png or logo_en.png)
                    $footerLogoFileName = 'logo_' . $locale . '.png';
                    $footerLogoUrl = url('assets/img/' . $footerLogoFileName);
                @endphp
                <img src="{{ $footerLogoUrl }}" alt="{{ config('app.name') }}" style="max-width: 120px; height: auto; margin-bottom: 15px; display: block; margin-left: auto; margin-right: auto;">
                <div style="font-size: 15px; color: #007bff; font-weight: 600; margin-bottom: 15px;">{{ trans('order::order.thank_you_message') }}</div>
                <div style="font-size: 13px; color: #666; margin-top: 10px;">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. {{ trans('order::order.all_rights_reserved') }}
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
