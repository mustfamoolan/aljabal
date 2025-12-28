<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreSupplierRequest;
use App\Http\Requests\Inventory\UpdateSupplierRequest;
use App\Models\Supplier;
use App\Services\Inventory\SupplierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function __construct(
        protected SupplierService $supplierService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse
    {
        $filters = $request->only(['search', 'is_active', 'per_page']);
        $suppliers = $this->supplierService->getAllSuppliers($filters);

        if ($request->expectsJson()) {
            return response()->json($suppliers);
        }

        return view('inventory.suppliers.list', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('inventory.suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSupplierRequest $request): RedirectResponse|JsonResponse
    {
        $supplier = $this->supplierService->createSupplier($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'تم إنشاء المورد بنجاح',
                'supplier' => $supplier,
            ], 201);
        }

        return redirect()->route('inventory.suppliers.index')
            ->with('success', 'تم إنشاء المورد بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier): View|JsonResponse
    {
        $supplier->load(['products', 'purchases']);

        if (request()->expectsJson()) {
            return response()->json($supplier);
        }

        return view('inventory.suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier): View
    {
        return view('inventory.suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSupplierRequest $request, Supplier $supplier): RedirectResponse|JsonResponse
    {
        $supplier = $this->supplierService->updateSupplier($supplier, $request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'تم تحديث المورد بنجاح',
                'supplier' => $supplier,
            ]);
        }

        return redirect()->route('inventory.suppliers.index')
            ->with('success', 'تم تحديث المورد بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier): RedirectResponse|JsonResponse
    {
        try {
            $this->supplierService->deleteSupplier($supplier);

            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'تم حذف المورد بنجاح',
                ]);
            }

            return redirect()->route('inventory.suppliers.index')
                ->with('success', 'تم حذف المورد بنجاح');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
