@component('mail::message')
# Welcome, {{ $customer->first_name }}!

Thank you for registering with **e-RAMO Store**. We're excited to have you on board!

Your email has been successfully verified and your account is now active.

@component('mail::button', ['url' => route('home'), 'color' => 'success'])
Start Shopping
@endcomponent

## What's Next?

- **Complete Your Profile**: Add your address information for faster checkout
- **Explore Products**: Browse our wide range of products from trusted vendors
- **Secure Transactions**: All your transactions are protected with our secure payment gateway

## Need Help?

If you have any questions or need assistance, don't hesitate to reach out to our support team at:
- Email: support@eramo.com
- Phone: +20 XXX XXX XXXX

@component('mail::subcopy')
If you didn't create this account, please contact us immediately.
@endcomponent
@endcomponent
