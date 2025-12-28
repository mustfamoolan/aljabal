<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreSupplierRequest;
use App\Http\Requests\Inventory\UpdateSupplierRequest;
use App\Http\Resources\Inventory\SupplierResource;
use App\Models\Supplier;
use App\Services\Inventory\SupplierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function __construct(
        protected SupplierService $supplierService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'is_active', 'per_page']);
        $suppliers = $this->supplierService->getAllSuppliers($filters);

        return response()->json([
            'data' => SupplierResource::collection($suppliers->items()),
            'meta' => [
                'current_page' => $suppliers->currentPage(),
                'last_page' => $suppliers->lastPage(),
                'per_page' => $suppliers->perPage(),
                'total' => $suppliers->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSupplierRequest $request): JsonResponse
    {
        $supplier = $this->supplierService->createSupplier($request->validated());

        return response()->json([
            'message' => 'تم إنشاء المورد بنجاح',
            'data' => new SupplierResource($supplier),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier): JsonResponse
    {
        $supplier->load(['products', 'purchases']);

        return response()->json([
            'data' => new SupplierResource($supplier),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSupplierRequest $request, Supplier $supplier): JsonResponse
    {
        $supplier = $this->supplierService->updateSupplier($supplier, $request->validated());

        return response()->json([
            'message' => 'تم تحديث المورد بنجاح',
            'data' => new SupplierResource($supplier),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier): JsonResponse
    {
        try {
            $this->supplierService->deleteSupplier($supplier);

            return response()->json([
                'message' => 'تم حذف المورد بنجاح',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
