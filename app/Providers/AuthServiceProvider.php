<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

use App\Models\UserType;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Define gates for all permissions dynamically
        Gate::before(function ($user, $ability) {
            // Super admin bypass
            if ($user->user_type_id === UserType::SUPER_ADMIN_TYPE) {
                return true;
            }

            // Check if user has roles
            if ($user->roles->isEmpty()) {
                return false;
            }

            // Check if user has a role with the permission
            // We iterate through the roles and check the loaded permissions
            foreach ($user->roles as $role) {
                // If permissions are already loaded, use collection search to limit DB queries
                // Otherwise, query the relation (cached by Laravel if eager loaded)
                if ($role->permessions->contains('key', $ability)) {
                    return true;
                }
            }
            
            // Return false to deny access if explicit permission not found
            return false;
        });
    }
}
