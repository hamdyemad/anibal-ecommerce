<?php

namespace Modules\CatalogManagement\app\Interfaces;

interface TaxRepositoryInterface
{
    /**
     * Get all taxes with optional pagination
     */
    public function getAllTaxes(int $perPage = 10, array $filters = []);

    /**
     * Get taxes query for DataTables
     */
    public function getTaxesQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc');

    /**
     * Get tax by ID
     */
    public function getTaxById(int $id);

    /**
     * Create a new tax
     */
    public function createTax(array $data);

    /**
     * Update tax
     */
    public function updateTax(int $id, array $data);

    /**
     * Delete tax
     */
    public function deleteTax(int $id);

    /**
     * Get active taxes
     */
    public function getActiveTaxes();
}
