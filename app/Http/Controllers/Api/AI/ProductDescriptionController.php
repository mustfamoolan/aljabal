<?php

namespace App\Http\Controllers\Api\AI;

use App\Http\Controllers\Controller;
use App\Services\AI\GeminiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProductDescriptionController extends Controller
{
    protected GeminiService $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Generate product description
     */
    public function generate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'product_type' => 'nullable|string',
            'category_id' => 'nullable|integer|exists:categories,id',
            'category_name' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'color' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255',
            'retail_price' => 'nullable|numeric',
            'type' => 'nullable|string|in:short,long,both',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = $validator->validated();
            $type = $data['type'] ?? 'both';

            // Get category name if category_id is provided
            $categoryName = $data['category_name'] ?? null;
            if (empty($categoryName) && !empty($data['category_id'])) {
                $category = \App\Models\Category::find($data['category_id']);
                if ($category) {
                    $categoryName = $category->getFullPath();
                }
            }

            // Get product type label if provided
            $productTypeLabel = $data['product_type'] ?? null;
            if (!empty($data['product_type'])) {
                try {
                    $productTypeEnum = \App\Enums\ProductType::tryFrom($data['product_type']);
                    if ($productTypeEnum) {
                        $productTypeLabel = $productTypeEnum->label();
                    }
                } catch (\Exception $e) {
                    // Use raw value if enum conversion fails
                }
            }

            // Prepare data for Gemini
            $productData = [
                'name' => $data['name'],
                'author' => $data['author'] ?? null,
                'publisher' => $data['publisher'] ?? null,
                'product_type' => $productTypeLabel,
                'category_name' => $categoryName,
                'tags' => $data['tags'] ?? [],
                'color' => $data['color'] ?? null,
                'sku' => $data['sku'] ?? null,
                'retail_price' => $data['retail_price'] ?? null,
            ];

            Log::info('Generating product description via API', [
                'product_name' => $productData['name'],
                'type' => $type,
            ]);

            $descriptions = $this->geminiService->generateProductDescription($productData, $type);

            return response()->json([
                'success' => true,
                'short_description' => $descriptions['short'],
                'long_description' => $descriptions['long'],
            ]);
        } catch (\Exception $e) {
            Log::error('Error generating product description via API', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'فشل في توليد الوصف. يرجى المحاولة مرة أخرى.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
