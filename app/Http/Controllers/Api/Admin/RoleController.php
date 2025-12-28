<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Http\Resources\Admin\RoleResource;
use App\Services\Admin\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct(
        protected RoleService $roleService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'per_page']);
        $roles = $this->roleService->getAllRoles($filters);

        return response()->json([
            'data' => RoleResource::collection($roles->items()),
            'meta' => [
                'current_page' => $roles->currentPage(),
                'last_page' => $roles->lastPage(),
                'per_page' => $roles->perPage(),
                'total' => $roles->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = $this->roleService->createRole($request->validated());
        $role->load(['permissions']);

        return response()->json([
            'message' => 'تم إنشاء الدور بنجاح',
            'data' => new RoleResource($role),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role): JsonResponse
    {
        $role->load(['permissions']);

        return response()->json([
            'data' => new RoleResource($role),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $role = $this->roleService->updateRole($role, $request->validated());
        $role->load(['permissions']);

        return response()->json([
            'message' => 'تم تحديث الدور بنجاح',
            'data' => new RoleResource($role),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): JsonResponse
    {
        $this->roleService->deleteRole($role);

        return response()->json([
            'message' => 'تم حذف الدور بنجاح',
        ]);
    }

    /**
     * Assign permission to role
     */
    public function assignPermission(Request $request, Role $role): JsonResponse
    {
        $request->validate([
            'permission' => ['required', 'string', 'exists:permissions,name'],
        ]);

        $this->roleService->assignPermission($role, $request->permission);
        $role->load(['permissions']);

        return response()->json([
            'message' => 'تم إسناد الصلاحية للدور بنجاح',
            'data' => new RoleResource($role),
        ]);
    }

    /**
     * Revoke permission from role
     */
    public function revokePermission(Request $request, Role $role): JsonResponse
    {
        $request->validate([
            'permission' => ['required', 'string', 'exists:permissions,name'],
        ]);

        $this->roleService->revokePermission($role, $request->permission);
        $role->load(['permissions']);

        return response()->json([
            'message' => 'تم إزالة الصلاحية من الدور بنجاح',
            'data' => new RoleResource($role),
        ]);
    }
}
