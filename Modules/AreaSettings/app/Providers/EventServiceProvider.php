<?php

namespace Modules\AreaSettings\app\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\AreaSettings\app\Models\Country;
use Modules\AreaSettings\app\Models\City;
use Modules\AreaSettings\app\Models\Region;
use Modules\AreaSettings\app\Observers\CountryObserver;
use Modules\AreaSettings\app\Observers\CityObserver;
use Modules\AreaSettings\app\Observers\RegionObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [];

    /**
     * The model observers for the application.
     *
     * @var array<string, string>
     */
    protected $observers = [
        Country::class => [CountryObserver::class],
        City::class => [CityObserver::class],
        Region::class => [RegionObserver::class],
    ];

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = true;

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void {}
}

