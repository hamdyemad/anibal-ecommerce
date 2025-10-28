<?php

namespace App\Repositories;

use App\Interfaces\RoleRepositoryInterface;
use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

class RoleRepository implements RoleRepositoryInterface
{
    /**
     * Get all roles with their permissions
     */
    public function getAllWithPermissions(): Collection
    {
        return Role::with('permessions')->latest()->get();
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
    public function create(array $data): Role
    {
        return Role::create($data);
    }

    /**
     * Update a role
     */
    public function update(Role $role, array $data): Role
    {
        $role->update($data);
        return $role->fresh();
    }

    /**
     * Delete a role
     */
    public function delete(Role $role): bool
    {
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
