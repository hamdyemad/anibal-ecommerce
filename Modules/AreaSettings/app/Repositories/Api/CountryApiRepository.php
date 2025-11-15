<?php

namespace Modules\AreaSettings\app\Repositories\Api;

use App\Actions\IsPaginatedAction;
use Modules\AreaSettings\app\Actions\CountryQueryAction;
use Modules\AreaSettings\app\Interfaces\Api\CountryApiRepositoryInterface;

class CountryApiRepository implements CountryApiRepositoryInterface
{
    public function __construct(private CountryQueryAction $query, private IsPaginatedAction $paginated){}

    public function getAllCountries(array $filters = [])
    {
        $paginated = isset($filters["paginated"]) ? true : false;
        $query = $this->query->handle($filters);
        $result = $this->paginated->handle($query, $paginated, $filters["per_page"] ?? null);
        return $result;
    }

    public function getCountryById(array $filters = [], int $id)
    {
        return $this->query->handle($filters)->with('currency.translations')->find($id);
    }
}
