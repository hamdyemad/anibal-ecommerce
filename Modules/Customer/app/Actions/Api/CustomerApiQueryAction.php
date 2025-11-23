<?php

namespace Modules\Customer\app\Actions\Api;

use Modules\Customer\app\Models\Customer;

class CustomerApiQueryAction
{
    /**
     * Build the query with filters
     */
    public function handle(array $filters = [])
    {
        $query = Customer::query();

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Status filter
        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        // Language filter
        if (!empty($filters['lang'])) {
            $query->where('lang', $filters['lang']);
        }

        // Load relationships
        $query->with(['addresses', 'fcmTokens']);

        // Order by latest first
        $query->latest();

        return $query;
    }
}
