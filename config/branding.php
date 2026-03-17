<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Branding Configuration
    |--------------------------------------------------------------------------
    |
    | Define your application's branding colors and styling preferences
    | used across emails, notifications, and UI components.
    |
    | Colors are pulled from CSS variables defined in resources/scss/variables.scss
    | These match the project's existing design system.
    |
    */
    'logo' => env('APP_URL') . "/assets/img/logo.jpg",

    'colors' => [
        'primary' => '#0056B7',
        'secondary' => '#cb1037',
        'text' => '#272b41',
        'light_gray' => '#f3f3f3',
        'border' => '#e3e6ef',
    ],

    'email' => [
        'logo_url_ar' => env('APP_URL') . "/assets/img/logo.jpg",
        'support_email' => 'info@anibal.com',
        'support_phone' => '+1090563070',
    ],

    'social' => [
        'facebook' => '#',
        'twitter' => '#',
        'instagram' => '#',
        'linkedin' => '#',
    ],
];
