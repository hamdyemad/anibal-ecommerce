<?php

namespace Modules\AreaSettings\app\Actions;

use Modules\AreaSettings\app\Models\City;

class CityQueryAction
{
    public function handle(array $filters = [])
    {
        $query = City::query()
            ->active()
            ->with([
                'translations',
                'shippings' => function($query) {
                    $query->where('active', 1);
                }
            ])
            ->filter($filters);
        return $query;
    }
}
