<?php

namespace Modules\AreaSettings\app\Services;

use Modules\AreaSettings\app\Interfaces\RegionRepositoryInterface;
use Illuminate\Support\Facades\Log;

class RegionService
{
    protected $regionRepository;

    public function __construct(RegionRepositoryInterface $regionRepository)
    {
        $this->regionRepository = $regionRepository;
    }

    /**
     * Get all regions with filters and pagination
     */
    public function getAllRegions(array $filters = [], int $perPage = 15)
    {
        try {
            return $this->regionRepository->getAllRegions($filters, $perPage);
        } catch (\Exception $e) {
            Log::error('Error fetching regions: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get regions query for DataTables
     */
    public function getRegionsQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc')
    {
        try {
            return $this->regionRepository->getRegionsQuery($filters, $orderBy, $orderDirection);
        } catch (\Exception $e) {
            Log::error('Error fetching regions query: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get region by ID
     */
    public function getRegionById(int $id)
    {
        try {
            return $this->regionRepository->getRegionById($id);
        } catch (\Exception $e) {
            Log::error('Error fetching region: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a new region
     */
    public function createRegion(array $data)
    {
        try {
            return $this->regionRepository->createRegion($data);
        } catch (\Exception $e) {
            Log::error('Error creating region: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update region
     */
    public function updateRegion(int $id, array $data)
    {
        try {
            return $this->regionRepository->updateRegion($id, $data);
        } catch (\Exception $e) {
            Log::error('Error updating region: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete region
     */
    public function deleteRegion(int $id)
    {
        try {
            return $this->regionRepository->deleteRegion($id);
        } catch (\Exception $e) {
            Log::error('Error deleting region: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get active regions
     */
    public function getActiveRegions()
    {
        try {
            return $this->regionRepository->getActiveRegions();
        } catch (\Exception $e) {
            Log::error('Error fetching active regions: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get regions by city
     */
    public function getRegionsByCity(int $cityId)
    {
        try {
            return $this->regionRepository->getRegionsByCity($cityId);
        } catch (\Exception $e) {
            Log::error('Error fetching regions by city: ' . $e->getMessage());
            throw $e;
        }
    }
}
