<?php

namespace App\Services\Inventory;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\PurchaseHistory;
use App\Services\Notifications\NotificationService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function __construct(
        protected NotificationService $notificationService
    ) {
    }
    /**
     * Get all products with optional filtering
     */
    public function getAllProducts(array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Product::with(['category', 'subcategory', 'supplier', 'images', 'tags']);

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('publisher', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        if (isset($filters['category_id'])) {
            $categoryId = $filters['category_id'];
            $query->where(function ($q) use ($categoryId) {
                // Return products where this ID is the main category,
                // OR it's a subcategory of the product,
                // OR the product's subcategory belongs to this parent category
                $q->where('category_id', $categoryId)
                    ->orWhere('subcategory_id', $categoryId)
                    ->orWhereIn('subcategory_id', function ($subQuery) use ($categoryId) {
                        $subQuery->select('id')
                            ->from('categories')
                            ->where('parent_id', $categoryId);
                    });
            });
        }

        if (isset($filters['subcategory_id'])) {
            $query->where('subcategory_id', $filters['subcategory_id']);
        }

        if (isset($filters['supplier_id'])) {
            $query->where('supplier_id', $filters['supplier_id']);
        }

        if (isset($filters['tag_id'])) {
            $query->whereHas('tags', function ($q) use ($filters) {
                $q->where('tags.id', $filters['tag_id']);
            });
        }

        if (isset($filters['author'])) {
            $query->where('author', 'like', "%{$filters['author']}%");
        }

        if (isset($filters['publisher'])) {
            $query->where('publisher', 'like', "%{$filters['publisher']}%");
        }

        if (isset($filters['product_type'])) {
            $query->where('product_type', $filters['product_type']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['low_stock'])) {
            $query->whereRaw('quantity <= min_quantity');
        }

        if (isset($filters['stock_status'])) {
            switch ($filters['stock_status']) {
                case 'in_stock':
                    $query->where('quantity', '>', 0);
                    break;
                case 'low_stock':
                    $query->whereRaw('quantity <= min_quantity')->where('quantity', '>', 0);
                    break;
                case 'out_of_stock':
                    $query->where('quantity', '<=', 0);
                    break;
            }
        }

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get inventory statistics
     */
    public function getInventoryStats(): array
    {
        return [
            'total' => Product::count(),
            'in_stock' => Product::where('quantity', '>', 0)->count(),
            'low_stock' => Product::whereRaw('quantity <= min_quantity')->where('quantity', '>', 0)->count(),
            'out_of_stock' => Product::where('quantity', '<=', 0)->count(),
        ];
    }

    /**
     * Get product with all relationships
     */
    public function getProduct(Product $product): Product
    {
        return $product->load(['category', 'supplier', 'images', 'tags', 'purchaseHistory.supplier', 'purchaseHistory.createdBy']);
    }

    /**
     * Create a new product
     */
    public function createProduct(array $data): Product
    {
        DB::beginTransaction();
        try {
            // Generate SKU if not provided
            if (empty($data['sku'])) {
                $data['sku'] = $this->generateSku();
            }

            // Extract images and tags from data
            $images = $data['images'] ?? [];
            $tagIds = $data['tags'] ?? [];
            unset($data['images'], $data['tags']);

            // Ensure available_quantity is set if quantity is provided
            if (isset($data['quantity']) && !isset($data['available_quantity'])) {
                $data['available_quantity'] = $data['quantity'];
            }

            // Create product
            $product = Product::create($data);

            // Upload images
            if (!empty($images)) {
                $this->uploadImages($product, $images);
            }

            // Sync tags
            if (!empty($tagIds)) {
                $product->tags()->sync($tagIds);
            }

            DB::commit();

            // Check for low stock and send notification
            $product = $product->fresh(['category', 'supplier', 'images', 'tags']);
            if ($this->notificationService->checkLowStock($product)) {
                $this->notificationService->sendLowStockNotification($product);
            }

            // Notify all representatives about the new product
            $this->notificationService->sendNewProductNotification($product);

            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update product
     */
    public function updateProduct(Product $product, array $data): Product
    {
        \Illuminate\Support\Facades\Log::info('ProductService::updateProduct called', [
            'product_id' => $product->id,
            'data_keys' => array_keys($data),
            'quantity_in_data' => isset($data['quantity']),
            'min_quantity_in_data' => isset($data['min_quantity']),
        ]);

        DB::beginTransaction();
        try {
            // Extract images and tags from data
            $images = $data['images'] ?? null;
            $tagIds = $data['tags'] ?? null;
            unset($data['images'], $data['tags']);

            // Sync available_quantity when quantity is updated directly
            if (isset($data['quantity'])) {
                $oldQuantity = $product->quantity ?? 0;
                $difference = $data['quantity'] - $oldQuantity;
                $data['available_quantity'] = max(0, ($product->available_quantity ?? 0) + $difference);
            }

            // Update product
            $oldPrice = (float) $product->customer_price;
            $product->update($data);
            $newPrice = (float) $product->fresh()->customer_price;

            // Check for price discount
            if ($newPrice < $oldPrice && $newPrice > 0) {
                $this->notificationService->sendProductPriceDiscountNotification($product, $oldPrice, $newPrice);
            }

            \Illuminate\Support\Facades\Log::info('Product updated in database', [
                'product_id' => $product->id,
                'new_quantity' => $product->fresh()->quantity,
                'new_min_quantity' => $product->fresh()->min_quantity,
            ]);

            // Upload new images if provided
            if ($images !== null && !empty($images)) {
                // Add new images to existing ones (don't replace)
                $this->uploadImages($product, $images);
            }

            // Sync tags if provided
            if ($tagIds !== null) {
                $product->tags()->sync($tagIds);
            }

            DB::commit();

            // Check for low stock and send notification
            // Always check after update, not just when quantity/min_quantity changed
            $product = $product->fresh(['category', 'supplier', 'images', 'tags']);

            \Illuminate\Support\Facades\Log::info('Product updated - checking for low stock notification', [
                'product_id' => $product->id,
                'quantity' => $product->quantity,
                'min_quantity' => $product->min_quantity,
                'quantity_in_request' => isset($data['quantity']),
                'min_quantity_in_request' => isset($data['min_quantity']),
            ]);

            // Always check for low stock after any update
            $isLowStock = $this->notificationService->checkLowStock($product);

            \Illuminate\Support\Facades\Log::info('Low stock check result', [
                'product_id' => $product->id,
                'is_low_stock' => $isLowStock,
                'quantity' => $product->quantity,
                'min_quantity' => $product->min_quantity,
            ]);

            if ($isLowStock) {
                \Illuminate\Support\Facades\Log::info('Sending low stock notification', [
                    'product_id' => $product->id,
                ]);
                $this->notificationService->sendLowStockNotification($product);
            } else {
                \Illuminate\Support\Facades\Log::info('Product is not in low stock, skipping notification', [
                    'product_id' => $product->id,
                ]);
            }

            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete product
     */
    public function deleteProduct(Product $product): bool
    {
        DB::beginTransaction();
        try {
            // Delete images
            $this->deleteImages($product);

            // Delete product
            $product->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Add quantity to product and create purchase history
     */
    public function addQuantity(Product $product, int $quantity, array $purchaseData): PurchaseHistory
    {
        DB::beginTransaction();
        try {
            $purchasePrice = $purchaseData['purchase_price'] ?? $product->purchase_price ?? 0;
            $totalAmount = $quantity * $purchasePrice;
            $purchaseDate = $purchaseData['purchase_date'] ?? now()->toDateString();

            // Create purchase history
            $purchaseHistory = PurchaseHistory::create([
                'product_id' => $product->id,
                'supplier_id' => $purchaseData['supplier_id'] ?? null,
                'quantity' => $quantity,
                'purchase_price' => $purchasePrice,
                'total_amount' => $totalAmount,
                'purchase_date' => $purchaseDate,
                'notes' => $purchaseData['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // Update product quantity
            $product->updateQuantity($quantity, 'add');

            // Update purchase dates
            if ($product->first_purchase_date === null) {
                $product->first_purchase_date = $purchaseDate;
            }
            $product->last_purchase_date = $purchaseDate;
            $product->save();

            DB::commit();

            // Check for low stock and send notification
            $product = $product->fresh();
            if ($this->notificationService->checkLowStock($product)) {
                $this->notificationService->sendLowStockNotification($product);
            }

            return $purchaseHistory->load(['supplier', 'createdBy']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Reduce quantity from product
     */
    public function reduceQuantity(Product $product, int $quantity): void
    {
        $product->updateQuantity($quantity, 'subtract');
    }

    /**
     * Upload product images
     */
    public function uploadImages(Product $product, array $images): void
    {
        $uploadPath = "products/{$product->id}";

        // Get the highest image_order from existing images
        $maxOrder = $product->images()->max('image_order') ?? 0;

        foreach ($images as $index => $image) {
            if ($image instanceof UploadedFile) {
                $imagePath = $image->store($uploadPath, 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $imagePath,
                    'image_order' => $maxOrder + $index + 1,
                ]);
            }
        }
    }

    /**
     * Delete product images
     */
    public function deleteImages(Product $product): void
    {
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }
    }

    /**
     * Delete a single product image
     */
    public function deleteImage(ProductImage $image): void
    {
        $product = $image->product;
        $deletedOrder = $image->image_order;

        // Delete file from storage
        Storage::disk('public')->delete($image->image_path);

        // Delete image record
        $image->delete();

        // Reorder remaining images
        $remainingImages = $product->images()
            ->where('image_order', '>', $deletedOrder)
            ->orderBy('image_order')
            ->get();

        foreach ($remainingImages as $remainingImage) {
            $remainingImage->update(['image_order' => $remainingImage->image_order - 1]);
        }
    }

    /**
     * Sync tags for product
     */
    public function syncTags(Product $product, array $tagIds): void
    {
        $product->tags()->sync($tagIds);
    }

    public function getRecommendedProducts(Product $product, int $limit = 10): array
    {
        $baseQuery = Product::with(['category', 'subcategory', 'images', 'tags'])
            ->where('id', '!=', $product->id)
            ->where('is_active', true);

        $results = [];

        // 1. Same Author (Robust match)
        if ($product->author) {
            $author = trim($product->author);
            $results['same_author'] = (clone $baseQuery)
                ->where('author', 'LIKE', $author)
                ->inRandomOrder()
                ->limit($limit)
                ->get();
        }

        // 2. Same Subcategory (Precise branch)
        if ($product->subcategory_id) {
            $results['same_subcategory'] = (clone $baseQuery)
                ->where('subcategory_id', $product->subcategory_id)
                ->inRandomOrder()
                ->limit($limit)
                ->get();
        }
        // Fallback to Main Category if no subcategory results or no subcategory set
        if (empty($results['same_subcategory'])) {
            $results['same_category'] = (clone $baseQuery)
                ->where('category_id', $product->category_id)
                ->whereNull('subcategory_id') // Avoid duplicating subcategory results
                ->inRandomOrder()
                ->limit($limit)
                ->get();

            // If still empty, just get anything from the same category
            if ($results['same_category']->isEmpty()) {
                unset($results['same_category']);
                $results['same_category'] = (clone $baseQuery)
                    ->where('category_id', $product->category_id)
                    ->inRandomOrder()
                    ->limit($limit)
                    ->get();
            }
        }

        // 3. Same Publisher
        if ($product->publisher) {
            $publisher = trim($product->publisher);
            $results['same_publisher'] = (clone $baseQuery)
                ->where('publisher', 'LIKE', $publisher)
                ->inRandomOrder()
                ->limit($limit)
                ->get();
        }

        // 4. Same Tags
        $tagIds = $product->tags->pluck('id')->toArray();
        if (!empty($tagIds)) {
            $results['same_tags'] = (clone $baseQuery)
                ->whereHas('tags', function ($tagQuery) use ($tagIds) {
                    $tagQuery->whereIn('tags.id', $tagIds);
                })
                ->inRandomOrder()
                ->limit($limit)
                ->get();
        }

        return array_filter($results, function ($collection) {
            return $collection->isNotEmpty();
        });
    }

    /**
     * Generate unique SKU
     */
    private function generateSku(): string
    {
        do {
            $sku = 'PRD-' . strtoupper(uniqid());
        } while (Product::where('sku', $sku)->exists());

        return $sku;
    }
}
