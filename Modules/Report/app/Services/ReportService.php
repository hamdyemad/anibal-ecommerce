<?php

namespace Modules\Report\app\Services;

use Modules\Report\app\DTOs\ReportFilterDTO;
use Modules\Report\app\Interfaces\ReportRepositoryInterface;

class ReportService
{
    public function __construct(
        protected ReportRepositoryInterface $reportRepository
    ) {}

    /**
     * Get registered users report
     */
    public function getRegisteredUsersReport(array $filters): array
    {
        $filterDTO = ReportFilterDTO::fromRequest($filters);
        return $this->reportRepository->getRegisteredUsersReport($filterDTO);
    }

    /**
     * Get area users report
     */
    public function getAreaUsersReport(array $filters): array
    {
        $filterDTO = ReportFilterDTO::fromRequest($filters);
        return $this->reportRepository->getAreaUsersReport($filterDTO);
    }

    /**
     * Get orders report
     */
    public function getOrdersReport(array $filters): array
    {
        $filterDTO = ReportFilterDTO::fromRequest($filters);
        return $this->reportRepository->getOrdersReport($filterDTO);
    }

    /**
     * Get products report
     */
    public function getProductsReport(array $filters): array
    {
        $filterDTO = ReportFilterDTO::fromRequest($filters);
        return $this->reportRepository->getProductsReport($filterDTO);
    }

    /**
     * Get points report
     */
    public function getPointsReport(array $filters): array
    {
        $filterDTO = ReportFilterDTO::fromRequest($filters);
        return $this->reportRepository->getPointsReport($filterDTO);
    }
}
