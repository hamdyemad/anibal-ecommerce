<?php

namespace Modules\Customer\app\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Customer\app\Models\Customer;

class WelcomeNotification extends Notification
{
    use Queueable;

    public function __construct(protected Customer $customer = null)
    {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting('Welcome, ' . $notifiable->first_name . '!')
            ->line('Thank you for registering with us.')
            ->line('Your account has been successfully created and verified.')
            ->action('Explore Now', route('home'))
            ->line('If you have any questions, please contact our support team.')
            ->salutation('Best regards,')
            ->markdown('customer::emails.welcome', [
                'customer' => $notifiable,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'customer_id' => $notifiable->id,
            'message' => 'Welcome to our platform!',
        ];
    }
}
