<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePermissionRequest;
use App\Http\Requests\Admin\UpdatePermissionRequest;
use App\Services\Admin\PermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct(
        protected PermissionService $permissionService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse
    {
        // Get all permissions with roles for GridJS (no pagination needed)
        $permissions = Permission::with('roles')->get();

        // Calculate statistics
        $totalPermissions = \Spatie\Permission\Models\Permission::count();
        $totalRoles = \Spatie\Permission\Models\Role::count();
        $totalUsers = \App\Models\User::whereIn('type', ['admin', 'employee'])->count();

        if ($request->expectsJson()) {
            return response()->json($permissions);
        }

        return view('users.pages-permission', compact('permissions', 'totalPermissions', 'totalRoles', 'totalUsers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('users.permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePermissionRequest $request): RedirectResponse|JsonResponse
    {
        $permission = $this->permissionService->createPermission($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'تم إنشاء الصلاحية بنجاح',
                'permission' => $permission,
            ], 201);
        }

        return redirect()->route('users.pages-permission')
            ->with('success', 'تم إنشاء الصلاحية بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission): View|JsonResponse
    {
        if (request()->expectsJson()) {
            return response()->json($permission);
        }

        return view('admin.permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission): View
    {
        return view('users.permissions.edit', compact('permission'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePermissionRequest $request, Permission $permission): RedirectResponse|JsonResponse
    {
        $permission = $this->permissionService->updatePermission($permission, $request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'تم تحديث الصلاحية بنجاح',
                'permission' => $permission,
            ]);
        }

        return redirect()->route('users.pages-permission')
            ->with('success', 'تم تحديث الصلاحية بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission): RedirectResponse|JsonResponse
    {
        $this->permissionService->deletePermission($permission);

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'تم حذف الصلاحية بنجاح',
            ]);
        }

        return redirect()->route('users.pages-permission')
            ->with('success', 'تم حذف الصلاحية بنجاح');
    }
}
