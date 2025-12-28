<?php

namespace App\Services\Admin;

use Spatie\Permission\Models\Role;

class RoleService
{
    /**
     * Get all roles
     */
    public function getAllRoles(array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Role::with('permissions', 'users');

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Create a new role
     */
    public function createRole(array $data): Role
    {
        return Role::create(['name' => $data['name']]);
    }

    /**
     * Update role
     */
    public function updateRole(Role $role, array $data): Role
    {
        $role->update(['name' => $data['name']]);
        return $role->fresh();
    }

    /**
     * Delete role
     */
    public function deleteRole(Role $role): bool
    {
        return $role->delete();
    }

    /**
     * Assign permission to role
     */
    public function assignPermission(Role $role, string $permissionName): void
    {
        $role->givePermissionTo($permissionName);
    }

    /**
     * Revoke permission from role
     */
    public function revokePermission(Role $role, string $permissionName): void
    {
        $role->revokePermissionTo($permissionName);
    }
}
