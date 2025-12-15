<?php

namespace Modules\SystemSetting\app\Services\Api;

use Modules\SystemSetting\app\Interfaces\Api\AdApiRepositoryInterface;

class AdApiService
{
    protected $adRepository;

    public function __construct(AdApiRepositoryInterface $adRepository)
    {
        $this->adRepository = $adRepository;
    }

    public function getAll()
    {
        return $this->adRepository->all();
    }

    public function find($id)
    {
        return $this->adRepository->find($id);
    }

}
