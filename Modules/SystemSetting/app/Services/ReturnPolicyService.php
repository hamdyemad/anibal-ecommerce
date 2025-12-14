<?php

namespace Modules\SystemSetting\app\Services;

use Modules\SystemSetting\app\Repositories\ReturnPolicyRepository;

class ReturnPolicyService
{
    protected $repository;

    public function __construct(ReturnPolicyRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getReturnPolicy()
    {
        return $this->repository->getReturnPolicy();
    }

    public function updateReturnPolicy($data)
    {
        return $this->repository->update($data);
    }
}
