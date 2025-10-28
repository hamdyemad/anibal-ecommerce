<?php

namespace App\Interfaces;

interface DepartmentRepositoryInterface
{
    /**
     * Get all departments with filters and pagination
     */
    public function getAllDepartments(array $filters = [], ?int $perPage);

    /**
     * Get departments query for DataTables
     */
    public function getDepartmentsQuery(array $filters = []);

    /**
     * Get department by ID
     */
    public function getDepartmentById(int $id);

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

    /**
     * Get active departments
     */
    public function getActiveDepartments();
}
