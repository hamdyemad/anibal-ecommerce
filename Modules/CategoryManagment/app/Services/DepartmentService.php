<?php

namespace Modules\CategoryManagment\app\Services;

use Modules\CategoryManagment\app\Interfaces\DepartmentRepositoryInterface;
use Illuminate\Support\Facades\Log;

class DepartmentService
{
    protected $departmentRepository;

    public function __construct(DepartmentRepositoryInterface $departmentRepository)
    {
        $this->departmentRepository = $departmentRepository;
    }

    /**
     * Get all departments with filters and pagination
     */
    public function getAllDepartments(array $filters = [], int $perPage = 15)
    {
        try {
            return $this->departmentRepository->getAllDepartments($filters, $perPage);
        } catch (\Exception $e) {
            Log::error('Error fetching departments: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get departments query for DataTables
     */
    public function getDepartmentsQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc')
    {
        try {
            return $this->departmentRepository->getDepartmentsQuery($filters, $orderBy, $orderDirection);
        } catch (\Exception $e) {
            Log::error('Error fetching departments query: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get active departments
     */
    public function getActiveDepartments()
    {
        try {
            return $this->departmentRepository->getActiveDepartments();
        } catch (\Exception $e) {
            Log::error('Error fetching active departments: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get department by ID
     */
    public function getDepartmentById(int $id)
    {
        try {
            return $this->departmentRepository->findById($id);
        } catch (\Exception $e) {
            Log::error('Error fetching department: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a new department
     */
    public function createDepartment(array $data)
    {
        try {
            return $this->departmentRepository->createDepartment($data);
        } catch (\Exception $e) {
            Log::error('Error creating department: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update department
     */
    public function updateDepartment(int $id, array $data)
    {
        try {
            return $this->departmentRepository->updateDepartment($id, $data);
        } catch (\Exception $e) {
            Log::error('Error updating department: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete department
     */
    public function deleteDepartment(int $id)
    {
        try {
            return $this->departmentRepository->deleteDepartment($id);
        } catch (\Exception $e) {
            Log::error('Error deleting department: ' . $e->getMessage());
            throw $e;
        }
    }
}
