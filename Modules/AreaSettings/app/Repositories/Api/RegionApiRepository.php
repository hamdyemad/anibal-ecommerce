<?php

namespace Modules\AreaSettings\app\Repositories\Api;

use App\Actions\IsPaginatedAction;
use Modules\AreaSettings\app\Actions\RegionQueryAction;
use Modules\AreaSettings\app\Interfaces\Api\RegionApiRepositoryInterface;

class RegionApiRepository implements RegionApiRepositoryInterface
{
    public function __construct(private RegionQueryAction $query, private IsPaginatedAction $paginated){}

    public function getAllRegions(array $filters = [])
    {
        $paginated = isset($filters["paginated"]) ? true : false;
        $query = $this->query->handle($filters);
        $result = $this->paginated->handle($query, $paginated, $filters["per_page"] ?? null);
        return $result;
    }

    public function getRegionsByCity(array $filters = [], int $id)
    {
        $paginated = isset($filters["paginated"]) ? true : false;

        $query = $this->query->handle($filters)->whereHas('city', function($q) use ($id) {
            $q->where('id', $id);
        });

        $result = $this->paginated->handle($query, $paginated, $filters["per_page"] ?? null);
        return $result;
    }
}
