<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Services\Admin\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
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
    public function index(Request $request): View|JsonResponse
    {
        $filters = $request->only(['search', 'per_page']);
        $roles = $this->roleService->getAllRoles($filters);

        if ($request->expectsJson()) {
            return response()->json($roles);
        }

        return view('users.role.list', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $permissions = \Spatie\Permission\Models\Permission::all();
        return view('users.role.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request): RedirectResponse|JsonResponse
    {
        $role = $this->roleService->createRole($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'تم إنشاء الدور بنجاح',
                'role' => $role,
            ], 201);
        }

        // Assign permissions if provided
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('users.role.list')
            ->with('success', 'تم إنشاء الدور بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role): View|JsonResponse
    {
        $role->load(['permissions']);

        if (request()->expectsJson()) {
            return response()->json($role);
        }

        return view('admin.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role): View
    {
        $role->load(['permissions']);
        $permissions = \Spatie\Permission\Models\Permission::all();
        return view('users.role.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse|JsonResponse
    {
        $role = $this->roleService->updateRole($role, $request->validated());

        // Sync permissions if provided
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'تم تحديث الدور بنجاح',
                'role' => $role,
            ]);
        }

        return redirect()->route('users.role.list')
            ->with('success', 'تم تحديث الدور بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): RedirectResponse|JsonResponse
    {
        $this->roleService->deleteRole($role);

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'تم حذف الدور بنجاح',
            ]);
        }

        return redirect()->route('users.role.list')
            ->with('success', 'تم حذف الدور بنجاح');
    }

    /**
     * Assign permission to role
     */
    public function assignPermission(Request $request, Role $role): RedirectResponse|JsonResponse
    {
        $request->validate([
            'permission' => ['required', 'string', 'exists:permissions,name'],
        ]);

        $this->roleService->assignPermission($role, $request->permission);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'تم إسناد الصلاحية للدور بنجاح',
            ]);
        }

        return back()->with('success', 'تم إسناد الصلاحية للدور بنجاح');
    }

    /**
     * Revoke permission from role
     */
    public function revokePermission(Request $request, Role $role): RedirectResponse|JsonResponse
    {
        $request->validate([
            'permission' => ['required', 'string', 'exists:permissions,name'],
        ]);

        $this->roleService->revokePermission($role, $request->permission);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'تم إزالة الصلاحية من الدور بنجاح',
            ]);
        }

        return back()->with('success', 'تم إزالة الصلاحية من الدور بنجاح');
    }
}
