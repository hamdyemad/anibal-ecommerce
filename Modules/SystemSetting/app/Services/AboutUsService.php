<?php

namespace Modules\SystemSetting\app\Services;

use Modules\SystemSetting\app\Repositories\AboutUsRepository;

class AboutUsService
{
    protected $repository;

    public function __construct(AboutUsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getByPlatform(string $platform = 'website')
    {
        return $this->repository->getByPlatform($platform);
    }

    public function update(array $data, string $platform = 'website')
    {
        return $this->repository->update($data, $platform);
    }
}
