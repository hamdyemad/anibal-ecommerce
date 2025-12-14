<?php

namespace Modules\SystemSetting\app\Services;

use Modules\SystemSetting\app\Repositories\SiteInformationRepository;

class SiteInformationService
{
    protected $repository;

    public function __construct(SiteInformationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getSiteInformation()
    {
        return $this->repository->getSiteInformation();
    }

    public function updateSiteInformation($data)
    {
        return $this->repository->update($data);
    }
}
