<?php

namespace Modules\AreaSettings\app\Actions;

use Modules\AreaSettings\app\Models\City;

class CityQueryAction
{
    public function handle(array $filters = [])
    {
        $query = City::query()->with('translations')->filter($filters);
        return $query;
    }
}
