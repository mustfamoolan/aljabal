<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use App\Services\Admin\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['type', 'employee_type_id', 'is_active', 'search', 'per_page']);
        $users = $this->userService->getAllUsers($filters);

        return response()->json([
            'data' => UserResource::collection($users->items()),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->createUser($request->validated());
        $user->load(['roles', 'permissions', 'employeeType']);

        return response()->json([
            'message' => 'تم إنشاء المستخدم بنجاح',
            'data' => new UserResource($user),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): JsonResponse
    {
        $user->load(['roles', 'permissions', 'employeeType']);

        return response()->json([
            'data' => new UserResource($user),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $user = $this->userService->updateUser($user, $request->validated());
        $user->load(['roles', 'permissions', 'employeeType']);

        return response()->json([
            'message' => 'تم تحديث المستخدم بنجاح',
            'data' => new UserResource($user),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): JsonResponse
    {
        $this->userService->deleteUser($user);

        return response()->json([
            'message' => 'تم حذف المستخدم بنجاح',
        ]);
    }

    /**
     * Assign role to user
     */
    public function assignRole(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $this->userService->assignRole($user, $request->role);
        $user->load(['roles', 'permissions', 'employeeType']);

        return response()->json([
            'message' => 'تم إسناد الدور بنجاح',
            'data' => new UserResource($user),
        ]);
    }

    /**
     * Revoke role from user
     */
    public function revokeRole(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $this->userService->revokeRole($user, $request->role);
        $user->load(['roles', 'permissions', 'employeeType']);

        return response()->json([
            'message' => 'تم إزالة الدور بنجاح',
            'data' => new UserResource($user),
        ]);
    }

    /**
     * Assign permission to user
     */
    public function assignPermission(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'permission' => ['required', 'string', 'exists:permissions,name'],
        ]);

        $this->userService->assignPermission($user, $request->permission);
        $user->load(['roles', 'permissions', 'employeeType']);

        return response()->json([
            'message' => 'تم إسناد الصلاحية بنجاح',
            'data' => new UserResource($user),
        ]);
    }

    /**
     * Revoke permission from user
     */
    public function revokePermission(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'permission' => ['required', 'string', 'exists:permissions,name'],
        ]);

        $this->userService->revokePermission($user, $request->permission);
        $user->load(['roles', 'permissions', 'employeeType']);

        return response()->json([
            'message' => 'تم إزالة الصلاحية بنجاح',
            'data' => new UserResource($user),
        ]);
    }
}
