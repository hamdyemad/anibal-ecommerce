# Order Notifications Setup Guide

This guide explains how to configure email and WhatsApp notifications for new orders created through the `/api/v1/orders/checkout` endpoint.

## Features

When a customer completes checkout, the system automatically:
1. Sends a confirmation email to the customer's email address
2. Sends a WhatsApp message to the customer's phone number

## Email Configuration

Email notifications are sent using Laravel's built-in mail system. Make sure your `.env` file has the correct mail configuration:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Email Template

The email template is located at:
- `Modules/Order/resources/views/emails/order-created.blade.php`

You can customize this template to match your branding.

## WhatsApp Configuration

### Step 1: Choose a WhatsApp API Provider

You need to integrate with a WhatsApp Business API provider. Popular options include:
- **Twilio** (https://www.twilio.com/whatsapp)
- **MessageBird** (https://messagebird.com/whatsapp)
- **WhatsApp Business API** (Official)
- **WATI** (https://www.wati.io/)
- **Interakt** (https://www.interakt.shop/)

### Step 2: Add Configuration to .env

Add these variables to your `.env` file:

```env
WHATSAPP_API_URL=https://your-whatsapp-provider.com/api/send
WHATSAPP_API_TOKEN=your_api_token_here
```

### Step 3: Customize WhatsApp Service (if needed)

The WhatsApp service is located at:
- `Modules/Order/app/Services/WhatsAppService.php`

The default implementation expects a simple POST request with:
```json
{
    "phone": "+201234567890",
    "message": "Your order message here"
}
```

If your provider uses a different API format, modify the `sendOrderConfirmation()` method in `WhatsAppService.php`.

### Example: Twilio Integration

For Twilio, update the `sendOrderConfirmation()` method:

```php
$response = Http::withHeaders([
    'Authorization' => 'Basic ' . base64_encode(config('services.whatsapp.account_sid') . ':' . config('services.whatsapp.auth_token')),
    'Content-Type' => 'application/x-www-form-urlencoded',
])->asForm()->post($apiUrl, [
    'From' => 'whatsapp:+14155238886', // Your Twilio WhatsApp number
    'To' => 'whatsapp:' . $phone,
    'Body' => $message,
]);
```

And update `.env`:
```env
WHATSAPP_API_URL=https://api.twilio.com/2010-04-01/Accounts/YOUR_ACCOUNT_SID/Messages.json
WHATSAPP_ACCOUNT_SID=your_account_sid
WHATSAPP_AUTH_TOKEN=your_auth_token
```

## Message Content

### Email Content
The email includes:
- Order number
- Order date
- Total amount
- Payment method
- Link to track the order

### WhatsApp Message Content
The WhatsApp message includes:
- Order number
- Total amount
- Link to track the order
- Bilingual support (Arabic/English based on app locale)

## Testing

### Test Email Notifications

1. Use a service like Mailtrap for testing emails without sending real emails
2. Create a test order through the checkout endpoint
3. Check your Mailtrap inbox for the confirmation email

### Test WhatsApp Notifications

1. If WhatsApp API is not configured, the system will log a warning but won't fail
2. Check `storage/logs/laravel.log` for WhatsApp notification logs
3. Once configured, test with a real phone number

## Troubleshooting

### Email not sending
- Check `storage/logs/laravel.log` for errors
- Verify MAIL_* configuration in `.env`
- Test mail configuration: `php artisan tinker` then `Mail::raw('Test', function($msg) { $msg->to('test@example.com')->subject('Test'); });`

### WhatsApp not sending
- Check `storage/logs/laravel.log` for errors
- Verify WHATSAPP_* configuration in `.env`
- Ensure phone numbers are in international format (e.g., +201234567890)
- Check your WhatsApp API provider's dashboard for delivery status

## Disabling Notifications

If you want to temporarily disable notifications:

### Disable Email
Comment out the email sending code in `Modules/Order/app/Pipelines/CreateOrder.php` (lines with `Mail::to()`)

### Disable WhatsApp
Simply don't set the `WHATSAPP_API_URL` and `WHATSAPP_API_TOKEN` in `.env`. The system will log a warning but continue processing orders.

## Files Modified/Created

1. **Created:**
   - `Modules/Order/app/Mail/OrderCreated.php` - Email mailable class
   - `Modules/Order/app/Services/WhatsAppService.php` - WhatsApp service
   - `Modules/Order/resources/views/emails/order-created.blade.php` - Email template

2. **Modified:**
   - `Modules/Order/app/Pipelines/CreateOrder.php` - Added notification sending
   - `config/services.php` - Added WhatsApp configuration
   - `lang/en/order.php` - Added email translation keys
   - `lang/ar/order.php` - Added email translation keys (Arabic)

## Support

For issues or questions, check the Laravel logs at `storage/logs/laravel.log`.
