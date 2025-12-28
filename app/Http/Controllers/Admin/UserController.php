<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Services\Admin\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse
    {
        $filters = $request->only(['type', 'employee_type', 'is_active', 'search', 'per_page']);
        $users = $this->userService->getAllUsers($filters);

        // Calculate statistics
        $totalUsers = \App\Models\User::whereIn('type', ['admin', 'employee'])->count();
        $totalAdmins = \App\Models\User::where('type', 'admin')->count();
        $totalEmployees = \App\Models\User::where('type', 'employee')->count();
        $totalActive = \App\Models\User::where('is_active', true)->whereIn('type', ['admin', 'employee'])->count();

        if ($request->expectsJson()) {
            return response()->json($users);
        }

        return view('users.users.list', compact('users', 'totalUsers', 'totalAdmins', 'totalEmployees', 'totalActive'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $employeeTypes = \App\Models\EmployeeType::where('is_active', true)->get() ?? collect([]);
        return view('admin.users.create', compact('employeeTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse|JsonResponse
    {
        $user = $this->userService->createUser($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'تم إنشاء المستخدم بنجاح',
                'user' => $user,
            ], 201);
        }

        return redirect()->route('users.users.list')
            ->with('success', 'تم إنشاء المستخدم بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): View|JsonResponse
    {
        $user->load(['roles', 'permissions']);

        if (request()->expectsJson()) {
            return response()->json($user);
        }

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        $user->load(['roles', 'permissions', 'employeeType']);
        $employeeTypes = \App\Models\EmployeeType::where('is_active', true)->get() ?? collect([]);
        return view('admin.users.edit', compact('user', 'employeeTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse|JsonResponse
    {
        $user = $this->userService->updateUser($user, $request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'تم تحديث المستخدم بنجاح',
                'user' => $user,
            ]);
        }

        return redirect()->route('users.users.list')
            ->with('success', 'تم تحديث المستخدم بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse|JsonResponse
    {
        $this->userService->deleteUser($user);

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'تم حذف المستخدم بنجاح',
            ]);
        }

        return redirect()->route('users.users.list')
            ->with('success', 'تم حذف المستخدم بنجاح');
    }

    /**
     * Assign role to user
     */
    public function assignRole(Request $request, User $user): RedirectResponse|JsonResponse
    {
        $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $this->userService->assignRole($user, $request->role);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'تم إسناد الدور بنجاح',
            ]);
        }

        return back()->with('success', 'تم إسناد الدور بنجاح');
    }

    /**
     * Revoke role from user
     */
    public function revokeRole(Request $request, User $user): RedirectResponse|JsonResponse
    {
        $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $this->userService->revokeRole($user, $request->role);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'تم إزالة الدور بنجاح',
            ]);
        }

        return back()->with('success', 'تم إزالة الدور بنجاح');
    }

    /**
     * Assign permission to user
     */
    public function assignPermission(Request $request, User $user): RedirectResponse|JsonResponse
    {
        $request->validate([
            'permission' => ['required', 'string', 'exists:permissions,name'],
        ]);

        $this->userService->assignPermission($user, $request->permission);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'تم إسناد الصلاحية بنجاح',
            ]);
        }

        return back()->with('success', 'تم إسناد الصلاحية بنجاح');
    }

    /**
     * Revoke permission from user
     */
    public function revokePermission(Request $request, User $user): RedirectResponse|JsonResponse
    {
        $request->validate([
            'permission' => ['required', 'string', 'exists:permissions,name'],
        ]);

        $this->userService->revokePermission($user, $request->permission);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'تم إزالة الصلاحية بنجاح',
            ]);
        }

        return back()->with('success', 'تم إزالة الصلاحية بنجاح');
    }
}
