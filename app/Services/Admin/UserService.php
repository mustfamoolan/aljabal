<?php

namespace App\Services\Admin;

use App\Enums\UserType;
use App\Models\EmployeeType;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Get all users with optional filtering
     */
    public function getAllUsers(array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = User::with('roles', 'employeeType', 'employeeType.roles');

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['employee_type_id'])) {
            $query->where('employee_type_id', $filters['employee_type_id']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Create a new user
     */
    public function createUser(array $data): User
    {
        $userData = [
            'name' => $data['name'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'type' => UserType::from($data['type']),
            'is_active' => $data['is_active'] ?? true,
        ];

        if (isset($data['email'])) {
            $userData['email'] = $data['email'];
        }

        if (isset($data['employee_type_id']) && UserType::from($data['type']) === UserType::EMPLOYEE) {
            $userData['employee_type_id'] = $data['employee_type_id'];
        }

        $user = User::create($userData);

        // Assign roles from employee type if applicable
        if (isset($userData['employee_type_id'])) {
            $employeeType = EmployeeType::with('roles')->find($userData['employee_type_id']);
            if ($employeeType && $employeeType->roles->isNotEmpty()) {
                $roleIds = $employeeType->roles->pluck('id')->toArray();
                $user->syncRoles($roleIds);
            }
        }

        return $user->fresh(['roles', 'employeeType']);
    }

    /**
     * Update user
     */
    public function updateUser(User $user, array $data): User
    {
        $updateData = [];

        if (isset($data['name'])) {
            $updateData['name'] = $data['name'];
        }

        if (isset($data['phone'])) {
            $updateData['phone'] = $data['phone'];
        }

        if (array_key_exists('email', $data)) {
            $updateData['email'] = $data['email'] ?: null;
        }

        if (isset($data['password']) && !empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        if (isset($data['type'])) {
            $updateData['type'] = UserType::from($data['type']);
        }

        $userType = $updateData['type'] ?? $user->type;

        if (isset($data['employee_type_id'])) {
            if ($userType === UserType::EMPLOYEE) {
                $updateData['employee_type_id'] = $data['employee_type_id'];
            } else {
                $updateData['employee_type_id'] = null;
            }
        } elseif (isset($data['type']) && $userType === UserType::ADMIN) {
            // If changing to admin, remove employee_type_id
            $updateData['employee_type_id'] = null;
        }

        if (isset($data['is_active'])) {
            $updateData['is_active'] = $data['is_active'];
        }

        $oldEmployeeTypeId = $user->employee_type_id;
        $user->update($updateData);

        // Sync roles if employee_type_id changed
        $newEmployeeTypeId = $updateData['employee_type_id'] ?? $user->fresh()->employee_type_id;
        if ($newEmployeeTypeId !== $oldEmployeeTypeId && $newEmployeeTypeId) {
            $employeeType = EmployeeType::with('roles')->find($newEmployeeTypeId);
            if ($employeeType && $employeeType->roles->isNotEmpty()) {
                $roleIds = $employeeType->roles->pluck('id')->toArray();
                $user->syncRoles($roleIds);
            }
        } elseif ($newEmployeeTypeId === null && $oldEmployeeTypeId) {
            // If employee_type_id removed, keep existing roles (don't remove)
        }

        return $user->fresh(['roles', 'employeeType']);
    }

    /**
     * Delete user
     */
    public function deleteUser(User $user): bool
    {
        return $user->delete();
    }

    /**
     * Assign role to user
     */
    public function assignRole(User $user, string $roleName): void
    {
        $user->assignRole($roleName);
    }

    /**
     * Revoke role from user
     */
    public function revokeRole(User $user, string $roleName): void
    {
        $user->removeRole($roleName);
    }

    /**
     * Assign permission to user
     */
    public function assignPermission(User $user, string $permissionName): void
    {
        $user->givePermissionTo($permissionName);
    }

    /**
     * Revoke permission from user
     */
    public function revokePermission(User $user, string $permissionName): void
    {
        $user->revokePermissionTo($permissionName);
    }
}
