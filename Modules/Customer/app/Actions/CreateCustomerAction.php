<?php

namespace Modules\Customer\app\Actions;

use Modules\Customer\app\Models\Customer;
use Illuminate\Support\Facades\Hash;

class CreateCustomerAction
{
    /**
     * Create a new customer with provided data
     * Email verification happens after OTP confirmation
     */
    public function execute(array $data): Customer
    {
        return Customer::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'phone' => $data['phone'] ?? null,
            'lang' => $data['lang'] ?? 'en',
            'status' => $data['status'] ?? true,
            'country_id' => $data['country_id'] ?? null,
            'city_id' => $data['city_id'] ?? null,
            'region_id' => $data['region_id'] ?? null,
            'gender' => $data['gender'] ?? null,
        ]);
    }
}
