<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\AreaSettings\app\Models\Country;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/admin/dashboard';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        // Global handler for all routes
        Route::matched(function ($event) {
            $route = $event->route;
            if (!$route) return;

            $params = $route->parameters();

            // Only if there are at least 2 parameters
            if (count($params) >= 2) {
                $keys = array_keys($params);

                $firstKey = $keys[0];   // 'countryCode'
                $secondKey = $keys[1];  // 'activity'

                // Replace first parameter value with the second
                $route->setParameter($firstKey, $params[$secondKey]);
            }
        });


        $this->configureRateLimiting();
        // Set URL defaults to automatically inject country code
        $this->app->booted(function () {
            $this->app['router']->matched(function () {
                $this->setUrlDefaults();
            });
        });

        $this->routes(function () {

            Route::middleware('api')
                ->namespace($this->namespace)
                ->prefix('api')
                ->group(function() {
                    require base_path('routes/api.php');
                });

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(function() {
                    require base_path('routes/web.php');
                });

            Route::middleware(['web', 'auth'])
                ->namespace($this->namespace)
                ->group(function() {
                    require base_path('routes/admin.php');
                });
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }

    /**
     * Set URL defaults for locale and country code
     * This ensures route() helper automatically includes these parameters
     *
     * @return void
     */
    protected function setUrlDefaults()
    {
        try {
            // Get country code from URL segment or session
            $countryCode = request()->segment(2) ?? session('country_code', 'sa');

            // Validate if it's a real country code
            if ($countryCode) {
                $country = Country::where('code', strtoupper($countryCode))->first();
                if (!$country) {
                    $countryCode = session('country_code', 'sa');
                }
            }

            // Set URL defaults so route() automatically includes country code
            // This makes route('name', $id) work as route('name', ['countryCode' => $country, 'activity' => $id])
            URL::defaults([
                'countryCode' => strtolower($countryCode),
            ]);

        } catch (\Exception $e) {
            // Fallback
            URL::defaults([
                'countryCode' => 'sa',
            ]);
        }
    }
}
