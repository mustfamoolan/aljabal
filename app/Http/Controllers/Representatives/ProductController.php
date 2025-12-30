<?php

namespace App\Http\Controllers\Representatives;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Representatives\ProductRecommendationService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        protected ProductRecommendationService $recommendationService
    ) {
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): View
    {
        // Ensure product is active and has quantity
        if (!$product->is_active || $product->quantity <= 0) {
            abort(404, 'المنتج غير متاح');
        }

        // Load relationships
        $product->load(['category', 'subcategory', 'supplier', 'images', 'tags']);

        // Get recommendations
        $recommendations = $this->recommendationService->getRecommendations($product, 8);

        return view('representatives.products.show', [
            'title' => $product->name,
            'product' => $product,
            'recommendations' => $recommendations,
        ]);
    }

    /**
     * Get product recommendations (API endpoint).
     */
    public function getRecommendations(Product $product): JsonResponse
    {
        // Ensure product is active and has quantity
        if (!$product->is_active || $product->quantity <= 0) {
            return response()->json(['data' => []], 404);
        }

        $recommendations = $this->recommendationService->getRecommendations($product, 8);

        return response()->json([
            'data' => $recommendations->map(function ($recProduct) {
                $firstImage = $recProduct->images->first();
                return [
                    'id' => $recProduct->id,
                    'name' => $recProduct->name,
                    'sku' => $recProduct->sku,
                    'wholesale_price' => $recProduct->wholesale_price,
                    'quantity' => $recProduct->quantity,
                    'image' => $firstImage 
                        ? storage_url($firstImage->image_path) 
                        : null,
                    'category' => $recProduct->category?->name,
                    'subcategory' => $recProduct->subcategory?->name,
                ];
            }),
        ]);
    }
}
