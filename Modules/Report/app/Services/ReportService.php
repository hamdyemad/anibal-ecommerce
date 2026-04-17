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

    /**
     * Get profitability report
     */
    public function getProfitabilityReport(array $filters): array
    {
        $filterDTO = ReportFilterDTO::fromRequest($filters);
        return $this->reportRepository->getProfitabilityReport($filterDTO);
    }

    /**
     * Get sales analysis report
     */
    public function getSalesAnalysisReport(array $filters): array
    {
        $filterDTO = ReportFilterDTO::fromRequest($filters);
        return $this->reportRepository->getSalesAnalysisReport($filterDTO);
    }

    /**
     * Get product performance report
     */
    public function getProductPerformanceReport(array $filters): array
    {
        $filterDTO = ReportFilterDTO::fromRequest($filters);
        return $this->reportRepository->getProductPerformanceReport($filterDTO);
    }

    /**
     * Get customer analysis report
     */
    public function getCustomerAnalysisReport(array $filters): array
    {
        $filterDTO = ReportFilterDTO::fromRequest($filters);
        return $this->reportRepository->getCustomerAnalysisReport($filterDTO);
    }
}
