<?php

namespace Modules\AreaSettings\app\Interfaces;

interface SubRegionRepositoryInterface
{
    public function getAllSubRegions(array $filters = [], int $perPage = 15);
    public function getSubRegionsQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc');
    public function getSubRegionById(int $id);
    public function createSubRegion(array $data);
    public function updateSubRegion(int $id, array $data);
    public function deleteSubRegion(int $id);
    public function getActiveSubRegions();
    public function getSubRegionsByRegion(int $regionId);

}
