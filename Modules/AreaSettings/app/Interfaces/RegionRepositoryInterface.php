<?php

namespace Modules\AreaSettings\app\Interfaces;

interface RegionRepositoryInterface
{
    public function getAllRegions(array $filters = [], int $perPage = 15);
    public function getRegionsQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc');
    public function getRegionById(int $id);
    public function createRegion(array $data);
    public function updateRegion(int $id, array $data);
    public function deleteRegion(int $id);
    public function getActiveRegions();
    public function getRegionsByCity(int $cityId);
}
