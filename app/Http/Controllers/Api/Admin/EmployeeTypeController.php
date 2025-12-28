<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEmployeeTypeRequest;
use App\Http\Requests\Admin\UpdateEmployeeTypeRequest;
use App\Http\Resources\Admin\EmployeeTypeResource;
use App\Models\EmployeeType;
use App\Services\Admin\EmployeeTypeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeTypeController extends Controller
{
    public function __construct(
        protected EmployeeTypeService $employeeTypeService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'per_page', 'is_active']);
        $employeeTypes = $this->employeeTypeService->getAllEmployeeTypes($filters);

        return response()->json([
            'data' => EmployeeTypeResource::collection($employeeTypes->items()),
            'meta' => [
                'current_page' => $employeeTypes->currentPage(),
                'last_page' => $employeeTypes->lastPage(),
                'per_page' => $employeeTypes->perPage(),
                'total' => $employeeTypes->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeTypeRequest $request): JsonResponse
    {
        $employeeType = $this->employeeTypeService->createEmployeeType($request->validated());

        return response()->json([
            'message' => 'تم إنشاء نوع الموظف بنجاح',
            'data' => new EmployeeTypeResource($employeeType),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(EmployeeType $employeeType): JsonResponse
    {
        $employeeType->load(['roles']);

        return response()->json([
            'data' => new EmployeeTypeResource($employeeType),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeTypeRequest $request, EmployeeType $employeeType): JsonResponse
    {
        $employeeType = $this->employeeTypeService->updateEmployeeType($employeeType, $request->validated());

        return response()->json([
            'message' => 'تم تحديث نوع الموظف بنجاح',
            'data' => new EmployeeTypeResource($employeeType),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeType $employeeType): JsonResponse
    {
        $this->employeeTypeService->deleteEmployeeType($employeeType);

        return response()->json([
            'message' => 'تم حذف نوع الموظف بنجاح',
        ]);
    }

    /**
     * Assign role to employee type
     */
    public function assignRole(Request $request, EmployeeType $employeeType): JsonResponse
    {
        $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $this->employeeTypeService->assignRole($employeeType, $request->role);
        $employeeType->load(['roles']);

        return response()->json([
            'message' => 'تم إسناد الدور لنوع الموظف بنجاح',
            'data' => new EmployeeTypeResource($employeeType),
        ]);
    }

    /**
     * Revoke role from employee type
     */
    public function revokeRole(Request $request, EmployeeType $employeeType): JsonResponse
    {
        $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $this->employeeTypeService->revokeRole($employeeType, $request->role);
        $employeeType->load(['roles']);

        return response()->json([
            'message' => 'تم إزالة الدور من نوع الموظف بنجاح',
            'data' => new EmployeeTypeResource($employeeType),
        ]);
    }
}
