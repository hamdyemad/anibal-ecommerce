<?php

namespace Modules\CategoryManagment\app\Interfaces;

interface DepartmentRepositoryInterface
{
    /**
     * Get all departments with filters and pagination
     */
    public function getAllDepartments(array $filters = [], int $perPage = 15);

    /**
     * Get departments query for DataTables
     */
    public function getDepartmentsQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc');

    /**
     * Get all active departments
     */
    public function getActiveDepartments();

    /**
     * Find department by ID
     */
    public function findById(int $id);

    /**
     * Create a new department
     */
    public function createDepartment(array $data);

    /**
     * Update department
     */
    public function updateDepartment(int $id, array $data);

    /**
     * Delete department
     */
    public function deleteDepartment(int $id);
}
