<?php

namespace App\Repositories;

use App\Interfaces\DepartmentRepositoryInterface;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

class DepartmentRepository implements DepartmentRepositoryInterface
{
    /**
     * Get all departments with filters and pagination
     */
    public function getAllDepartments(array $filters = [], ?int $perPage)
    {
        $query = Department::with('translations');

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->whereHas('translations', function($query) use ($search) {
                    $query->where('lang_value', 'like', "%{$search}%");
                })->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Active filter
        if (isset($filters['active']) && $filters['active'] !== '') {
            $query->where('active', $filters['active']);
        }

        // Date from filter
        if (!empty($filters['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }

        // Date to filter
        if (!empty($filters['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }

        // Order by latest
        $query->orderBy('created_at', 'desc');

        // Return paginated or all records
        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    /**
     * Get departments query for DataTables
     */
    public function getDepartmentsQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc')
    {
        $query = Department::with('translations');
        
        // Debug: Log filters in repository
        \Log::info('DepartmentRepository filters:', $filters);

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            \Log::info('Applying search filter:', ['search' => $search]);
            $query->where(function($q) use ($search) {
                $q->whereHas('translations', function($query) use ($search) {
                    $query->where('lang_value', 'like', "%{$search}%");
                })
                ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Active filter
        if (isset($filters['active']) && $filters['active'] !== '') {
            \Log::info('Applying active filter:', ['active' => $filters['active']]);
            $query->where('active', $filters['active']);
        }

        // Date from filter
        if (!empty($filters['created_date_from'])) {
            \Log::info('Applying date from filter:', ['date_from' => $filters['created_date_from']]);
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }

        // Date to filter
        if (!empty($filters['created_date_to'])) {
            \Log::info('Applying date to filter:', ['date_to' => $filters['created_date_to']]);
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }

        // Apply sorting
        if ($orderBy !== null) {
            if (is_array($orderBy)) {
                // Sorting by translated name
                $langId = $orderBy['lang_id'];
                $query->leftJoin('translations as t_sort', function($join) use ($langId) {
                    $join->on('departments.id', '=', 't_sort.translatable_id')
                         ->where('t_sort.translatable_type', '=', 'App\\Models\\Department')
                         ->where('t_sort.lang_id', '=', $langId)
                         ->where('t_sort.lang_key', '=', 'name');
                })
                ->orderBy('t_sort.lang_value', $orderDirection)
                ->select('departments.*');
            } else {
                // Sorting by regular column
                $query->orderBy($orderBy, $orderDirection);
            }
        }

        return $query;
    }

    /**
     * Get department by ID
     */
    public function getDepartmentById(int $id)
    {
        return Department::with('translations')->findOrFail($id);
    }

    /**
     * Create a new department
     */
    public function createDepartment(array $data)
    {
        return DB::transaction(function () use ($data) {
            $department = Department::create([
                'code' => $data['code'],
                'active' => $data['active'] ?? 0,
            ]);

            // Set translations from nested array
            if (isset($data['translations']) && is_array($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    if (isset($translation['name'])) {
                        $department->translations()->create([
                            'lang_id' => $langId,
                            'lang_key' => 'name',
                            'lang_value' => $translation['name'],
                        ]);
                    }
                }
            }
            
            return $department;
        });
    }

    /**
     * Update department
     */
    public function updateDepartment(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $department = Department::findOrFail($id);

            $department->update([
                'code' => $data['code'],
                'active' => $data['active'] ?? 0,
            ]);

            // Update translations from nested array
            if (isset($data['translations']) && is_array($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    if (isset($translation['name'])) {
                        $department->translations()->updateOrCreate(
                            [
                                'lang_id' => $langId,
                                'lang_key' => 'name',
                            ],
                            [
                                'lang_value' => $translation['name'],
                            ]
                        );
                    }
                }
            }

            $department->refresh();
            $department->load('translations');

            return $department;
        });
    }

    /**
     * Delete department
     */
    public function deleteDepartment(int $id)
    {
        $department = Department::findOrFail($id);
        $department->translations()->delete();
        return $department->delete();
    }

    /**
     * Get active departments
     */
    public function getActiveDepartments()
    {
        return Department::with('translations')->where('active', 1)
            ->get();
    }
}
