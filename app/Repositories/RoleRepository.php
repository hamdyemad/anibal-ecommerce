<?php

namespace App\Repositories;

use App\Interfaces\RoleRepositoryInterface;
use App\Models\Permession;
use App\Models\Role;
use App\Services\LanguageService;
use Illuminate\Database\Eloquent\Collection;

class RoleRepository implements RoleRepositoryInterface
{

    public function __construct(protected LanguageService $languageService)
    {
        
    }
    /**
     * Get all roles with their permissions
     */
    public function getAll($filter = [], $per_page = 10)
    {
        $query = Role::latest();
        
        if(isset($filter['with'])) {
            $query->with($filter['with']);
        }
        
        if(isset($filter['search']) && !empty($filter['search'])) {
            $query->whereHas('translations', function($query) use ($filter) {
                $query->where('lang_value', 'like', '%' . $filter['search'] . '%');
            });
        }

        // Apply date range filter
        if (isset($filter['created_date_from']) && !empty($filter['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filter['created_date_from']);
        }
        if (isset($filter['created_date_to']) && !empty($filter['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filter['created_date_to']);
        }
        
        return $query->paginate($per_page);
    }

    public function getRolesQuery($filter = []) {
        $query = Role::latest();
        if(isset($filter['with'])) {
            $query->with($filter['with']);
        }
        if(isset($filter['search']) && !empty($filter['search'])) {
            $query->whereHas('translations', function($query) use ($filter) {
                $query->where('lang_value', 'like', '%' . $filter['search'] . '%');
            });
        }

        // Apply date range filter
        if (isset($filter['created_date_from']) && !empty($filter['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filter['created_date_from']);
        }
        if (isset($filter['created_date_to']) && !empty($filter['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filter['created_date_to']);
        }

        return $query;
    }

    /**
     * Get grouped permissions
     */
    public function getGroupedPermissions(): Collection
    {
        // Load permissions with their translations
        $permissions = Permession::with('translations')->get();
        // Group by translated group_by field
        return $permissions->groupBy(function($permission) {
            return $permission->getTranslation('group_by', app()->getLocale()) ?? 'Other';
        });
    }


    /**
     * Find a role by ID
     */
    public function findById(int $id): ?Role
    {
        return Role::with('permessions')->find($id);
    }

    /**
     * Create a new role
     */
    public function create(array $data)
    {
        // Get all languages
        $languages = $this->languageService->getAll();
        $role = Role::create($data);
        // Add translations for all languages
        foreach ($languages as $language) {
            if (isset($data['name_' . $language->code]) && !empty($data['name_' . $language->code])) {
                $role->setTranslation(
                    'name',
                    $language->code,
                    $data['name_' . $language->code]
                );
            }
        }

        // Sync permissions if provided
        if (isset($data['permissions']) && is_array($data['permissions'])) {
            $this->syncPermissions($role, $data['permissions']);
        }
        return $role;

    }

    /**
     * Update a role
     */
    public function update(Role $role, array $data)
    {
        // Get all languages
        $languages = $this->languageService->getAll();
        // Update translations for all languages (no name column to update)
        foreach ($languages as $language) {
            if (isset($data['name_' . $language->code]) && !empty($data['name_' . $language->code])) {
                $role->setTranslation(
                    'name',
                    $language->code,
                    $data['name_' . $language->code]
                );
            }
        }
        // Sync permissions if provided
        if (isset($data['permissions']) && is_array($data['permissions'])) {
            $this->syncPermissions($role, $data['permissions']);
        } else {
            // Clear all permissions if none provided
            $this->syncPermissions($role, []);
        }
        return $role;
    }

    /**
     * Delete a role
     */
    public function delete(Role $role)
    {
        // Detach all permissions before deleting
        $role->permessions()->detach();
        // Delete all translations
        $role->translations()->delete();
        return $role->delete();
    }

    /**
     * Sync permissions for a role
     */
    public function syncPermissions(Role $role, array $permissionIds): void
    {
        $role->permessions()->sync($permissionIds);
    }

    /**
     * Set translation for a role
     */
    public function setTranslation(Role $role, string $key, string $value, string $locale): void
    {
        $role->setTranslation($key, $value, $locale);
    }
}
