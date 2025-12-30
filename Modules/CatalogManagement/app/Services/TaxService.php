<?php

namespace Modules\CatalogManagement\app\Services;

use Modules\CatalogManagement\app\Interfaces\TaxRepositoryInterface;
use Illuminate\Support\Facades\Log;

class TaxService
{
    protected $taxRepository;

    public function __construct(TaxRepositoryInterface $taxRepository)
    {
        $this->taxRepository = $taxRepository;
    }

    /**
     * Get all taxes with optional pagination
     */
    public function getAllTaxes(int $perPage = 10, array $filters = [])
    {
        try {
            return $this->taxRepository->getAllTaxes($perPage, $filters);
        } catch (\Exception $e) {
            Log::error('Error fetching taxes: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get taxes query for DataTables
     */
    public function getTaxesQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc')
    {
        try {
            return $this->taxRepository->getTaxesQuery($filters, $orderBy, $orderDirection);
        } catch (\Exception $e) {
            Log::error('Error fetching taxes query: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get tax by ID
     */
    public function getTaxById(int $id)
    {
        try {
            return $this->taxRepository->getTaxById($id);
        } catch (\Exception $e) {
            Log::error('Error fetching tax: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a new tax
     */
    public function createTax(array $data)
    {
        try {
            return $this->taxRepository->createTax($data);
        } catch (\Exception $e) {
            Log::error('Error creating tax: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update tax
     */
    public function updateTax(int $id, array $data)
    {
        try {
            return $this->taxRepository->updateTax($id, $data);
        } catch (\Exception $e) {
            Log::error('Error updating tax: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete tax
     */
    public function deleteTax(int $id)
    {
        try {
            return $this->taxRepository->deleteTax($id);
        } catch (\Exception $e) {
            Log::error('Error deleting tax: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get active taxes
     */
    public function getActiveTaxes()
    {
        try {
            return $this->taxRepository->getActiveTaxes();
        } catch (\Exception $e) {
            Log::error('Error fetching active taxes: ' . $e->getMessage());
            throw $e;
        }
    }
}
