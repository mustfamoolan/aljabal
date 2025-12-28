<?php

namespace App\Services\Admin;

use App\Models\EmployeeType;

class EmployeeTypeService
{
    /**
     * Get all employee types
     */
    public function getAllEmployeeTypes(array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = EmployeeType::with('roles', 'users');

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Create a new employee type
     */
    public function createEmployeeType(array $data): EmployeeType
    {
        $employeeType = EmployeeType::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);

        // Sync roles if provided
        if (isset($data['roles']) && is_array($data['roles'])) {
            $employeeType->roles()->sync($data['roles']);
        }

        return $employeeType->fresh(['roles']);
    }

    /**
     * Update employee type
     */
    public function updateEmployeeType(EmployeeType $employeeType, array $data): EmployeeType
    {
        $updateData = [];

        if (isset($data['name'])) {
            $updateData['name'] = $data['name'];
        }

        if (isset($data['description'])) {
            $updateData['description'] = $data['description'];
        }

        if (isset($data['is_active'])) {
            $updateData['is_active'] = $data['is_active'];
        }

        $employeeType->update($updateData);

        // Sync roles if provided
        if (isset($data['roles']) && is_array($data['roles'])) {
            $employeeType->roles()->sync($data['roles']);
        }

        return $employeeType->load(['roles']);
    }

    /**
     * Delete employee type
     */
    public function deleteEmployeeType(EmployeeType $employeeType): bool
    {
        return $employeeType->delete();
    }

    /**
     * Assign role to employee type
     */
    public function assignRole(EmployeeType $employeeType, string $roleName): void
    {
        $role = \Spatie\Permission\Models\Role::where('name', $roleName)->firstOrFail();
        $employeeType->roles()->syncWithoutDetaching([$role->id]);
    }

    /**
     * Revoke role from employee type
     */
    public function revokeRole(EmployeeType $employeeType, string $roleName): void
    {
        $role = \Spatie\Permission\Models\Role::where('name', $roleName)->firstOrFail();
        $employeeType->roles()->detach($role->id);
    }

    /**
     * Sync roles for employee type
     */
    public function syncRoles(EmployeeType $employeeType, array $roleIds): void
    {
        $employeeType->roles()->sync($roleIds);
    }
}
