<?php

namespace App\Services\Admin;

use Spatie\Permission\Models\Permission;

class PermissionService
{
    /**
     * Get all permissions
     */
    public function getAllPermissions(array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Permission::with('roles');

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Create a new permission
     */
    public function createPermission(array $data): Permission
    {
        return Permission::create(['name' => $data['name']]);
    }

    /**
     * Update permission
     */
    public function updatePermission(Permission $permission, array $data): Permission
    {
        $permission->update(['name' => $data['name']]);
        return $permission->fresh();
    }

    /**
     * Delete permission
     */
    public function deletePermission(Permission $permission): bool
    {
        return $permission->delete();
    }
}
