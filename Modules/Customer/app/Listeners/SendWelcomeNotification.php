<?php

namespace Modules\Customer\app\Listeners;

use Modules\Customer\app\Events\CustomerEmailVerified;
use Modules\Customer\app\Notifications\WelcomeNotification;

class SendWelcomeNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {}

    /**
     * Handle the event.
     */
    public function handle(CustomerEmailVerified $event): void
    {
        $event->customer->notify(new WelcomeNotification($event->customer));
    }
}
