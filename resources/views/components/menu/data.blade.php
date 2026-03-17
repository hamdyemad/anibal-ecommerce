@php
    // Withdraw transactions counts
    $new_transactions = 0;
    $accepted_transactions = 0;
    $rejected_transactions = 0;
    $all_transactions = 0;

    try {
        if ($vendor) {
            $new_transactions = Modules\Withdraw\app\Models\Withdraw::where('reciever_id', $vendor->id)
                ->where('status', 'new')
                ->count();
            $accepted_transactions = Modules\Withdraw\app\Models\Withdraw::where('reciever_id', $vendor->id)
                ->where('status', 'accepted')
                ->count();
            $rejected_transactions = Modules\Withdraw\app\Models\Withdraw::where('reciever_id', $vendor->id)
                ->where('status', 'rejected')
                ->count();
        } else {
            $new_transactions = Modules\Withdraw\app\Models\Withdraw::where('status', 'new')->count();
            $accepted_transactions = Modules\Withdraw\app\Models\Withdraw::where('status', 'accepted')->count();
            $rejected_transactions = Modules\Withdraw\app\Models\Withdraw::where('status', 'rejected')->count();
        }

        $all_transactions = Modules\Withdraw\app\Models\Withdraw::count();
    } catch (\Exception $e) {
        $new_transactions = 0;
        $accepted_transactions = 0;
        $rejected_transactions = 0;
        $all_transactions = 0;
    }

    // Get admin roles count (filtered by country + system roles)
    $admin_roles_count = 0;
    $admins_count = 0;
    $vendor_user_roles_count = 0;
    $vendor_users_count = 0;
    
    try {
        $countryCode = request()->route('countryCode') ?? session('country_code');
        $countryCode = strtoupper($countryCode);
        $currentCountryId = \Modules\AreaSettings\app\Models\Country::where('code', $countryCode)->value('id');

        $userTypeId = auth()->user()->user_type_id ?? null;
        
        // Admin roles count
        $rolesQuery = \App\Models\Role::where('type', 'admin');
        switch ($userTypeId) {
            case \App\Models\UserType::SUPER_ADMIN_TYPE:
                $rolesQuery->superAdminShowRoles();
                break;
            case \App\Models\UserType::ADMIN_TYPE:
                $rolesQuery->adminShowRoles();
                break;
            case \App\Models\UserType::VENDOR_TYPE:
            case \App\Models\UserType::VENDOR_USER_TYPE:
                $rolesQuery->vendorShowRoles();
                break;
        }
        if ($currentCountryId) {
            $rolesQuery->where(function($q) use ($currentCountryId) {
                $q->where('country_id', $currentCountryId)->orWhereNull('country_id');
            });
        }
        $admin_roles_count = $rolesQuery->count();

        // Admins count
        $adminsQuery = \App\Models\User::where('id', '!=', auth()->id());
        switch ($userTypeId) {
            case \App\Models\UserType::SUPER_ADMIN_TYPE:
                $adminsQuery->superAdminShow();
                break;
            case \App\Models\UserType::ADMIN_TYPE:
                $adminsQuery->adminShow();
                break;
            case \App\Models\UserType::VENDOR_TYPE:
                $adminsQuery->vendorShow();
                break;
            case \App\Models\UserType::VENDOR_USER_TYPE:
                $adminsQuery->otherShow();
                break;
        }
        if ($currentCountryId) {
            $adminsQuery->where(function($q) use ($currentCountryId) {
                $q->where('country_id', $currentCountryId)->orWhereNull('country_id');
            });
        }
        $admins_count = $adminsQuery->count();

        // Vendor user roles count
        $vendorUserRolesQuery = \App\Models\Role::where('type', 'vendor_user');
        switch ($userTypeId) {
            case \App\Models\UserType::SUPER_ADMIN_TYPE:
                $vendorUserRolesQuery->superAdminShowRoles();
                break;
            case \App\Models\UserType::ADMIN_TYPE:
                $vendorUserRolesQuery->adminShowRoles();
                break;
            case \App\Models\UserType::VENDOR_TYPE:
            case \App\Models\UserType::VENDOR_USER_TYPE:
                $vendorUserRolesQuery->vendorShowRoles();
                if (auth()->user()->isVendor()) {
                    $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
                    if ($vendor) {
                        $vendorUserRolesQuery->where(function($q) use ($vendor) {
                            $q->where('vendor_id', $vendor->id)->orWhereNull('vendor_id');
                        });
                    }
                }
                break;
        }
        if ($currentCountryId) {
            $vendorUserRolesQuery->where(function($q) use ($currentCountryId) {
                $q->where('country_id', $currentCountryId)->orWhereNull('country_id');
            });
        }
        $vendor_user_roles_count = $vendorUserRolesQuery->count();

        // Vendor users count
        $vendorUsersQuery = \App\Models\User::where('user_type_id', \App\Models\UserType::VENDOR_USER_TYPE)
            ->where('id', '!=', auth()->id());
        if (auth()->user()->isVendor()) {
            $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
            if ($vendor) {
                $vendorUsersQuery->where('vendor_id', $vendor->id);
            }
        }
        if ($currentCountryId) {
            $vendorUsersQuery->where(function($q) use ($currentCountryId) {
                $q->where('country_id', $currentCountryId)->orWhereNull('country_id');
            });
        }
        $vendor_users_count = $vendorUsersQuery->count();
    } catch (\Exception $e) {
        $admin_roles_count = 0;
        $admins_count = 0;
        $vendor_user_roles_count = 0;
        $vendor_users_count = 0;
    }
@endphp
