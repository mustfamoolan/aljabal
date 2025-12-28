<?php

namespace App\Services\Inventory;

use App\Models\Supplier;

class SupplierService
{
    /**
     * Get all suppliers
     */
    public function getAllSuppliers(array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Supplier::query();

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Create a new supplier
     */
    public function createSupplier(array $data): Supplier
    {
        return Supplier::create($data);
    }

    /**
     * Update supplier
     */
    public function updateSupplier(Supplier $supplier, array $data): Supplier
    {
        $supplier->update($data);
        return $supplier->fresh();
    }

    /**
     * Delete supplier
     */
    public function deleteSupplier(Supplier $supplier): bool
    {
        // Check if supplier has products
        if ($supplier->products()->count() > 0) {
            throw new \Exception('لا يمكن حذف المورد لأنه مرتبط بمنتجات');
        }

        return $supplier->delete();
    }
}
