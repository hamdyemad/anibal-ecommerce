<?php

namespace Modules\AreaSettings\app\Services;

use Modules\AreaSettings\app\Interfaces\CityRepositoryInterface;
use Illuminate\Support\Facades\Log;

class CityService
{
    protected $cityRepository;

    public function __construct(CityRepositoryInterface $cityRepository)
    {
        $this->cityRepository = $cityRepository;
    }

    /**
     * Get all cities with filters and pagination
     */
    public function getAllCities(array $filters = [], int $perPage = 15)
    {
        try {
            return $this->cityRepository->getAllCities($filters, $perPage);
        } catch (\Exception $e) {
            Log::error('Error fetching cities: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get cities query for DataTables
     */
    public function getCitiesQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc')
    {
        try {
            return $this->cityRepository->getCitiesQuery($filters, $orderBy, $orderDirection);
        } catch (\Exception $e) {
            Log::error('Error fetching cities query: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get city by ID
     */
    public function getCityById(int $id)
    {
        try {
            return $this->cityRepository->getCityById($id);
        } catch (\Exception $e) {
            Log::error('Error fetching city: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a new city
     */
    public function createCity(array $data)
    {
        try {
            return $this->cityRepository->createCity($data);
        } catch (\Exception $e) {
            Log::error('Error creating city: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update city
     */
    public function updateCity(int $id, array $data)
    {
        try {
            return $this->cityRepository->updateCity($id, $data);
        } catch (\Exception $e) {
            Log::error('Error updating city: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete city
     */
    public function deleteCity(int $id)
    {
        try {
            return $this->cityRepository->deleteCity($id);
        } catch (\Exception $e) {
            Log::error('Error deleting city: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get active cities
     */
    public function getActiveCities()
    {
        try {
            return $this->cityRepository->getActiveCities();
        } catch (\Exception $e) {
            Log::error('Error fetching active cities: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get cities by country
     */
    public function getCitiesByCountry(int $countryId)
    {
        try {
            return $this->cityRepository->getCitiesByCountry($countryId);
        } catch (\Exception $e) {
            Log::error('Error fetching cities by country: ' . $e->getMessage());
            throw $e;
        }
    }
}
