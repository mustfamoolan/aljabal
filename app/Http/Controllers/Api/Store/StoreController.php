<?php

namespace App\Http\Controllers\Api\Store;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * Get home page data
     */
    public function home()
    {
        $newArrivals = Product::with(['images', 'category'])
            ->where('is_active', true)
            ->latest()
            ->take(10)
            ->get();

        $suggested = Product::with(['images', 'category'])
            ->where('is_active', true)
            ->inRandomOrder()
            ->take(10)
            ->get();

        $categories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->with(['children' => function($query) {
                $query->where('is_active', true);
            }])
            ->get();

        return response()->json([
            'new_arrivals' => $this->formatProducts($newArrivals),
            'suggested' => $this->formatProducts($suggested),
            'categories' => $categories,
        ]);
    }

    /**
     * Get products with filtering
     */
    public function products(Request $request)
    {
        $query = Product::with(['images', 'category', 'subcategory'])
            ->where('is_active', true);

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->subcategory_id) {
            $query->where('subcategory_id', $request->subcategory_id);
        }

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('author', 'like', '%' . $request->search . '%');
        }

        $products = $query->latest()->paginate(20);

        return response()->json([
            'products' => $products->map(fn($p) => $this->formatProduct($p)),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'total' => $products->total(),
            ]
        ]);
    }

    /**
     * Get product details
     */
    public function productDetails(Product $product)
    {
        $product->load(['images', 'category', 'subcategory', 'tags']);
        
        return response()->json([
            'product' => $this->formatProduct($product, true),
        ]);
    }

    /**
     * Get all categories
     */
    public function categories()
    {
        $categories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->with(['children' => function($query) {
                $query->where('is_active', true);
            }])
            ->get();

        return response()->json([
            'categories' => $categories,
        ]);
    }

    /**
     * Get single category with its subcategories
     */
    public function categoryDetails(Category $category)
    {
        $category->load(['children' => function($query) {
            $query->where('is_active', true);
        }]);

        return response()->json([
            'category' => $category,
        ]);
    }

    /**
     * Format a collection of products
     */
    private function formatProducts($products)
    {
        return $products->map(fn($product) => $this->formatProduct($product));
    }

    /**
     * Format a single product for the API
     */
    private function formatProduct($product, $detailed = false)
    {
        $data = [
            'id' => $product->id,
            'name' => $product->name,
            'author' => $product->author,
            'retail_price' => (float) $product->retail_price,
            'image_url' => $product->image_url,
            'category' => $product->category ? $product->category->name : null,
        ];

        if ($detailed) {
            $data = array_merge($data, [
                'description' => $product->long_description ?? $product->short_description,
                'short_description' => $product->short_description,
                'publisher' => $product->publisher,
                'page_count' => $product->page_count,
                'is_hardcover' => $product->is_hardcover,
                'all_images' => $product->images->map(fn($img) => $img->image_url),
                'available_quantity' => $product->available_quantity,
                'subcategory' => $product->subcategory ? $product->subcategory->name : null,
                'parts' => $product->parts,
            ]);
        }

        return $data;
    }
}
