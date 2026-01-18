<?php

namespace Modules\CategoryManagment\app\Repositories;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\CategoryManagment\app\Interfaces\DepartmentRepositoryInterface;
use Modules\CategoryManagment\app\Models\Department;
use Modules\CategoryManagment\app\Traits\HandlesSortNumber;

class DepartmentRepository implements DepartmentRepositoryInterface
{
    use HandlesSortNumber;
    /**
     * Get all departments with filters and pagination
     */
    public function getAllDepartments(array $filters = [], int $perPage = 15)
    {
        $query = Department::with('translations')->filter($filters);
        $query->orderBy('sort_number', 'asc');
        return ($perPage == 0) ? $query->get() : $query->paginate($perPage);
    }

    /**
     * Get departments query for DataTables
     */
    public function getDepartmentsQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc')
    {
        return Department::with('translations')
            ->filter($filters)
            ->sorted($orderBy, $orderDirection, 'sort_number', 'asc');
    }

    /**
     * Get all active departments
     */
    public function getActiveDepartments()
    {
        return Department::with('translations')->where('active', 1)->orderBy('sort_number', 'asc')->get();
    }

    /**
     * Find department by ID
     */
    public function findById(int $id)
    {
        return Department::with('translations')->findOrFail($id);
    }

    /**
     * Create a new department
     */
    public function createDepartment(array $data)
    {
        return \DB::transaction(function () use ($data) {
            $sortNumber = $data['sort_number'] ?? 0;
            
            // Handle sort number before creating (global scope)
            $this->handleSortNumber(Department::class, null, $sortNumber);
            
            $department = Department::create([
                'active' => $data['active'] ?? 1,
                'commission' => $data['commission'] ?? 0,
                'sort_number' => $sortNumber,
                'view_status' => $data['view_status'] ?? 1,
            ]);

        // Store translations
        if (isset($data['translations'])) {
            foreach ($data['translations'] as $langId => $translation) {
                if (!empty($translation['name'])) {
                    $department->translations()->create([
                    'lang_id' => $langId,
                    'lang_key' => 'name',
                    'lang_value' => $translation['name'],
                    ]);
                }

                if (!empty($translation['description'])) {
                    $department->translations()->create([
                        'lang_id' => $langId,
                        'lang_key' => 'description',
                        'lang_value' => $translation['description'],
                    ]);
                }
            }
        }

        // Handle image upload
        if (isset($data['image'])) {
            $path = $data['image']->store("departments/$department->id", 'public');
            $department->attachments()->create([
                'path' => $path,
                'type' => 'image'
            ]);
        }

        // Handle icon upload
        if (isset($data['icon'])) {
            $path = $data['icon']->store("departments/$department->id", 'public');
            $department->attachments()->create([
                'path' => $path,
                'type' => 'icon'
            ]);
        }

            return $department;
        });
    }

    /**
     * Update department
     */
    public function updateDepartment(int $id, array $data)
    {
        return \DB::transaction(function () use ($id, $data) {
            $department = Department::findOrFail($id);

            $updateData = [
                'active' => $data['active'] ?? 1,
                'commission' => $data['commission'] ?? 0,
                'view_status' => $data['view_status'] ?? 1,
            ];
            
            // Handle sort_number to prevent duplicates globally
            if (isset($data['sort_number'])) {
                $newSortNumber = (int) $data['sort_number'];
                $oldSortNumber = $department->sort_number;
                
                // Use the trait handler function (global scope)
                $this->handleSortNumber(Department::class, $id, $newSortNumber, $oldSortNumber);
                
                $updateData['sort_number'] = $newSortNumber;
            }

            $department->update($updateData);

        // Handle image upload
        if (isset($data['image']) && $data['image']) {
            // Delete old image if exists
            $oldImage = $department->attachments()->where('type', 'image')->first();
            if ($oldImage) {
                if(Storage::disk('public')->exists($oldImage->path)) {
                    Storage::disk('public')->delete($oldImage->path);
                }
                $oldImage->forceDelete();
            }
            // Store new image
            $path = $data['image']->store("departments/{$department->id}", 'public');
            $department->attachments()->create([
                'path' => $path,
                'type' => 'image'
            ]);
        }

        // Handle icon upload
        if (isset($data['icon']) && $data['icon']) {
            // Delete old icon if exists
            $oldIcon = $department->attachments()->where('type', 'icon')->first();
            if ($oldIcon) {
                if(Storage::disk('public')->exists($oldIcon->path)) {
                    Storage::disk('public')->delete($oldIcon->path);
                }
                $oldIcon->forceDelete();
            }
            // Store new icon
            $path = $data['icon']->store("departments/{$department->id}", 'public');
            $department->attachments()->create([
                'path' => $path,
                'type' => 'icon'
            ]);
        }

        // Update translations
        if (isset($data['translations'])) {
            // Delete existing translations
            $department->translations()->forceDelete();

            // Create new translations
            foreach ($data['translations'] as $langId => $translation) {
                if (!empty($translation['name'])) {
                    $department->translations()->create([
                        'lang_id' => $langId,
                        'lang_key' => 'name',
                        'lang_value' => $translation['name'],
                    ]);
                }

                if (!empty($translation['description'])) {
                    $department->translations()->create([
                        'lang_id' => $langId,
                        'lang_key' => 'description',
                        'lang_value' => $translation['description'],
                    ]);
                }
            }
        }

        // Touch the model to trigger GlobalModelObserver for activity logging
        $department->touch();

            return $department;
        });
    }

    /**
     * Delete department
     */
    public function deleteDepartment(int $id)
    {
        return \DB::transaction(function () use ($id) {
            $department = Department::findOrFail($id);
            $deletedSortNumber = $department->sort_number;
            
            $oldImage = $department->attachments()->where('type', 'image')->first();
            $oldIcon = $department->attachments()->where('type', 'icon')->first();
            if ($oldImage) {
                $oldImage->forceDelete();
            }
            if ($oldIcon) {
                $oldIcon->forceDelete();
            }
            $department->translations()->delete();
            $department->delete();
            
            // Shift down all departments with higher sort numbers to fill the gap (global scope)
            $this->handleSortNumberAfterDelete(Department::class, $deletedSortNumber);
            
            return true;
        });
    }
}
