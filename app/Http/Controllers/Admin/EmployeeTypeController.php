<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEmployeeTypeRequest;
use App\Http\Requests\Admin\UpdateEmployeeTypeRequest;
use App\Models\EmployeeType;
use App\Services\Admin\EmployeeTypeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeTypeController extends Controller
{
    public function __construct(
        protected EmployeeTypeService $employeeTypeService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse
    {
        $filters = $request->only(['search', 'per_page', 'is_active']);
        $employeeTypes = $this->employeeTypeService->getAllEmployeeTypes($filters);

        if ($request->expectsJson()) {
            return response()->json($employeeTypes);
        }

        return view('admin.employee-types.list', compact('employeeTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $roles = \Spatie\Permission\Models\Role::all();
        return view('admin.employee-types.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeTypeRequest $request): RedirectResponse|JsonResponse
    {
        $employeeType = $this->employeeTypeService->createEmployeeType($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'تم إنشاء نوع الموظف بنجاح',
                'employee_type' => $employeeType,
            ], 201);
        }

        return redirect()->route('admin.employee-types.index')
            ->with('success', 'تم إنشاء نوع الموظف بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(EmployeeType $employeeType): View|JsonResponse
    {
        $employeeType->load(['roles']);

        if (request()->expectsJson()) {
            return response()->json($employeeType);
        }

        return view('admin.employee-types.show', compact('employeeType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmployeeType $employeeType): View
    {
        $employeeType->load(['roles']);
        $roles = \Spatie\Permission\Models\Role::all();
        return view('admin.employee-types.edit', compact('employeeType', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeTypeRequest $request, EmployeeType $employeeType): RedirectResponse|JsonResponse
    {
        $employeeType = $this->employeeTypeService->updateEmployeeType($employeeType, $request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'تم تحديث نوع الموظف بنجاح',
                'employee_type' => $employeeType,
            ]);
        }

        return redirect()->route('admin.employee-types.index')
            ->with('success', 'تم تحديث نوع الموظف بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeType $employeeType): RedirectResponse|JsonResponse
    {
        $this->employeeTypeService->deleteEmployeeType($employeeType);

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'تم حذف نوع الموظف بنجاح',
            ]);
        }

        return redirect()->route('admin.employee-types.index')
            ->with('success', 'تم حذف نوع الموظف بنجاح');
    }

    /**
     * Assign role to employee type
     */
    public function assignRole(Request $request, EmployeeType $employeeType): RedirectResponse|JsonResponse
    {
        $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $this->employeeTypeService->assignRole($employeeType, $request->role);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'تم إسناد الدور لنوع الموظف بنجاح',
            ]);
        }

        return back()->with('success', 'تم إسناد الدور لنوع الموظف بنجاح');
    }

    /**
     * Revoke role from employee type
     */
    public function revokeRole(Request $request, EmployeeType $employeeType): RedirectResponse|JsonResponse
    {
        $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $this->employeeTypeService->revokeRole($employeeType, $request->role);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'تم إزالة الدور من نوع الموظف بنجاح',
            ]);
        }

        return back()->with('success', 'تم إزالة الدور من نوع الموظف بنجاح');
    }
}
