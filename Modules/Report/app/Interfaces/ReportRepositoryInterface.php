<?php

namespace Modules\Report\app\Interfaces;

use Modules\Report\app\DTOs\ReportFilterDTO;

interface ReportRepositoryInterface
{
    /**
     * Get registered users report
     */
    public function getRegisteredUsersReport(ReportFilterDTO $filter): array;

    /**
     * Get area users report
     */
    public function getAreaUsersReport(ReportFilterDTO $filter): array;

    /**
     * Get orders report
     */
    public function getOrdersReport(ReportFilterDTO $filter): array;

    /**
     * Get products report
     */
    public function getProductsReport(ReportFilterDTO $filter): array;

    /**
     * Get points report
     */
    public function getPointsReport(ReportFilterDTO $filter): array;

    /**
     * Get profitability report
     */
    public function getProfitabilityReport(ReportFilterDTO $filter): array;

    /**
     * Get sales analysis report
     */
    public function getSalesAnalysisReport(ReportFilterDTO $filter): array;

    /**
     * Get product performance report
     */
    public function getProductPerformanceReport(ReportFilterDTO $filter): array;

    /**
     * Get customer analysis report
     */
    public function getCustomerAnalysisReport(ReportFilterDTO $filter): array;
}
