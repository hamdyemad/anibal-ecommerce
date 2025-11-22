<?php

namespace Modules\Customer\app\Services;

use Modules\Customer\app\Interfaces\CustomerRepositoryInterface;

class CustomerService
{
    protected $customerRepository;

    public function __construct(CustomerRepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * Get all customers with filters and pagination
     */
    public function getAllCustomers(array $filters = [])
    {
        return $this->customerRepository->getAllCustomers($filters);
    }

    /**
     * Get customer by ID
     */
    public function find(array $filters = [], $id)
    {
        return $this->customerRepository->find($filters, $id);
    }

    /**
     * Create a new customer
     */
    public function createCustomer(array $data)
    {
        return $this->customerRepository->createCustomer($data);
    }

    /**
     * Update customer
     */
    public function updateCustomer(int $id, array $data)
    {
        return $this->customerRepository->updateCustomer($id, $data);
    }

    /**
     * Delete customer
     */
    public function deleteCustomer(int $id)
    {
        return $this->customerRepository->deleteCustomer($id);
    }
}
