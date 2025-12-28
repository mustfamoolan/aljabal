<?php

namespace App\Services\Representatives;

use App\Models\Representative;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class RepresentativeService
{
    /**
     * Get all representatives with optional filtering
     */
    public function getAllRepresentatives(array $filters = []): LengthAwarePaginator
    {
        $query = Representative::query();

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Create a new representative
     */
    public function createRepresentative(array $data): Representative
    {
        $representativeData = [
            'name' => $data['name'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'is_active' => $data['is_active'] ?? true,
        ];

        if (isset($data['email'])) {
            $representativeData['email'] = $data['email'];
        }

        if (isset($data['address'])) {
            $representativeData['address'] = $data['address'];
        }

        $representative = Representative::create($representativeData);

        // Upload image if provided
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $imagePath = $this->uploadImage($representative, $data['image']);
            $representative->update(['image' => $imagePath]);
        }

        return $representative->fresh();
    }

    /**
     * Update representative
     */
    public function updateRepresentative(Representative $representative, array $data): Representative
    {
        $updateData = [];

        if (isset($data['name'])) {
            $updateData['name'] = $data['name'];
        }

        if (isset($data['phone'])) {
            $updateData['phone'] = $data['phone'];
        }

        if (array_key_exists('email', $data)) {
            $updateData['email'] = $data['email'] ?: null;
        }

        if (isset($data['password']) && !empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        if (isset($data['address'])) {
            $updateData['address'] = $data['address'];
        }

        if (isset($data['is_active'])) {
            $updateData['is_active'] = $data['is_active'];
        }

        // Handle image upload
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            // Delete old image if exists
            if ($representative->image) {
                Storage::disk('public')->delete($representative->image);
            }
            $updateData['image'] = $this->uploadImage($representative, $data['image']);
        }

        $representative->update($updateData);

        return $representative->fresh();
    }

    /**
     * Delete representative
     */
    public function deleteRepresentative(Representative $representative): bool
    {
        // Delete image if exists
        if ($representative->image) {
            Storage::disk('public')->delete($representative->image);
        }

        return $representative->delete();
    }

    /**
     * Upload image for representative
     */
    private function uploadImage(Representative $representative, UploadedFile $image): string
    {
        $uploadPath = "representatives/{$representative->id}";
        return $image->store($uploadPath, 'public');
    }
}

