<?php

namespace Modules\AreaSettings\app\Services;

use Modules\AreaSettings\app\Interfaces\SubRegionRepositoryInterface;
use Illuminate\Support\Facades\Log;

class SubRegionService
{
    protected $subregionRepository;

    public function __construct(SubRegionRepositoryInterface $subregionRepository)
    {
        $this->subregionRepository = $subregionRepository;
    }

    /**
     * Get all subregions with filters and pagination
     */
    public function getAllSubRegions(array $filters = [], int $perPage = 15)
    {
        try {
            return $this->subregionRepository->getAllSubRegions($filters, $perPage);
        } catch (\Exception $e) {
            Log::error('Error fetching subregions: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get subregions query for DataTables
     */
    public function getSubRegionsQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc')
    {
        try {
            return $this->subregionRepository->getSubRegionsQuery($filters, $orderBy, $orderDirection);
        } catch (\Exception $e) {
            Log::error('Error fetching subregions query: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get subregion by ID
     */
    public function getSubRegionById(int $id)
    {
        try {
            return $this->subregionRepository->getSubRegionById($id);
        } catch (\Exception $e) {
            Log::error('Error fetching subregion: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a new subregion
     */
    public function createSubRegion(array $data)
    {
        try {
            return $this->subregionRepository->createSubRegion($data);
        } catch (\Exception $e) {
            Log::error('Error creating subregion: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update subregion
     */
    public function updateSubRegion(int $id, array $data)
    {
        try {
            return $this->subregionRepository->updateSubRegion($id, $data);
        } catch (\Exception $e) {
            Log::error('Error updating subregion: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete subregion
     */
    public function deleteSubRegion(int $id)
    {
        try {
            return $this->subregionRepository->deleteSubRegion($id);
        } catch (\Exception $e) {
            Log::error('Error deleting subregion: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get active subregions
     */
    public function getActiveSubRegions()
    {
        try {
            return $this->subregionRepository->getActiveSubRegions();
        } catch (\Exception $e) {
            Log::error('Error fetching active subregions: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get subregions by region
     */
    public function getSubRegionsByRegion(int $regionId)
    {
        try {
            return $this->subregionRepository->getSubRegionsByRegion($regionId);
        } catch (\Exception $e) {
            Log::error('Error fetching subregions by region: ' . $e->getMessage());
            throw $e;
        }
    }
}
