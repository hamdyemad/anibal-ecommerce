<?php

namespace Modules\AreaSettings\app\Services;

use Modules\AreaSettings\app\Interfaces\CountryRepositoryInterface;
use Illuminate\Support\Facades\Log;

class CountryService
{
    protected $countryRepository;

    public function __construct(CountryRepositoryInterface $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    /**
     * Get all countries with filters and pagination
     */
    public function getAllCountries(array $filters = [], ?int $perPage = 15)
    {
        try {
            return $this->countryRepository->getAllCountries($filters, $perPage);
        } catch (\Exception $e) {
            Log::error('Error fetching countries: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get countries query for DataTables
     */
    public function getCountriesQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc')
    {
        try {
            return $this->countryRepository->getCountriesQuery($filters, $orderBy, $orderDirection);
        } catch (\Exception $e) {
            Log::error('Error fetching countries query: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get country by ID
     */
    public function getCountryById(int $id)
    {
        try {
            return $this->countryRepository->getCountryById($id);
        } catch (\Exception $e) {
            Log::error('Error fetching country: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a new country
     */
    public function createCountry(array $data)
    {
        try {
            return $this->countryRepository->createCountry($data);
        } catch (\Exception $e) {
            Log::error('Error creating country: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update country
     */
    public function updateCountry(int $id, array $data)
    {
        try {
            return $this->countryRepository->updateCountry($id, $data);
        } catch (\Exception $e) {
            Log::error('Error updating country: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete country
     */
    public function deleteCountry(int $id)
    {
        try {
            return $this->countryRepository->deleteCountry($id);
        } catch (\Exception $e) {
            Log::error('Error deleting country: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get active countries
     */
    public function getActiveCountries()
    {
        try {
            return $this->countryRepository->getActiveCountries();
        } catch (\Exception $e) {
            Log::error('Error fetching active countries: ' . $e->getMessage());
            throw $e;
        }
    }
}
