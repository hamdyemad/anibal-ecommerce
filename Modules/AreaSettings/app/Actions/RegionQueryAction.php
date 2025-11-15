<?php

namespace Modules\AreaSettings\app\Actions;

use Modules\AreaSettings\app\Models\Region;

class RegionQueryAction
{
    public function handle(array $filters = [])
    {
        $query = Region::query()->with('translations')->filter($filters);
        return $query;
    }
}
