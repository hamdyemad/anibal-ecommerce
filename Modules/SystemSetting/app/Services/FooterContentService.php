<?php

namespace Modules\SystemSetting\app\Services;

use Modules\SystemSetting\app\Repositories\FooterContentRepository;

class FooterContentService
{
    protected $footerContentRepository;

    public function __construct(FooterContentRepository $footerContentRepository)
    {
        $this->footerContentRepository = $footerContentRepository;
    }

    public function getFooterContent()
    {
        return $this->footerContentRepository->get();
    }

    public function saveFooterContent(array $data)
    {
        return $this->footerContentRepository->createOrUpdate($data);
    }
}
