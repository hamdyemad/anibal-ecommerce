<?php

namespace Modules\Customer\app\Services\Api;

use App\Exceptions\InvalidPasswordException;
use Illuminate\Support\Facades\Hash;
use Modules\Customer\app\Interfaces\Api\CustomerApiRepositoryInterface;
use Modules\Customer\app\Models\Customer;

class CustomerApiService
{
    protected $customerRepository;

    public function __construct(CustomerApiRepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function getProfile(Customer $customer): Customer
    {
        return $this->customerRepository->getProfile($customer);
    }

    /**
     * Update customer profile information
     */
    public function updateProfile(Customer $customer, array $data): Customer
    {
        // Verify current password
        if (!Hash::check($data['current_password'], $customer->password)) {
            throw new InvalidPasswordException();
        }

        // Update info if any info fields are provided
        $infoFields = ['first_name', 'last_name', 'phone', 'lang'];
        $infoData = array_filter(
            array_intersect_key($data, array_flip($infoFields)),
            fn($value) => $value !== null
        );

        if (!empty($infoData)) {
            $customer = $this->customerRepository->updateInfo($customer, $infoData);
        }

        // Update password if new password is provided
        if (isset($data['new_password']) && !empty($data['new_password'])) {
            $customer = $this->customerRepository->updatePassword($customer, $data['new_password']);
        }

        return $customer;
    }

    public function changeLanguage(Customer $customer, string $lang): Customer
    { 
        $customer = $this->customerRepository->changeLanguage($customer, $lang);
        return $customer;
    }
}
