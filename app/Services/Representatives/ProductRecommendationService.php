<?php

namespace App\Services\Representatives;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductRecommendationService
{
    /**
     * Get product recommendations based on tags, publisher, author, and category.
     *
     * @param Product $product The product to get recommendations for
     * @param int $limit Maximum number of recommendations to return
     * @return Collection
     */
    public function getRecommendations(Product $product, int $limit = 8): Collection
    {
        // Load relationships if not already loaded
        if (!$product->relationLoaded('tags')) {
            $product->load('tags');
        }
        if (!$product->relationLoaded('category')) {
            $product->load('category');
        }
        if (!$product->relationLoaded('subcategory')) {
            $product->load('subcategory');
        }

        // Get all active products with quantity, excluding current product
        $query = Product::where('is_active', true)
            ->where('quantity', '>', 0)
            ->where('id', '!=', $product->id)
            ->with(['images', 'category', 'subcategory', 'tags']);

        // Build recommendation query with scoring
        $recommendations = $query->get()->map(function ($recommendedProduct) use ($product) {
            $score = 0;
            $matches = [];

            // Score by tags (highest priority - 10 points per tag)
            if ($product->tags->isNotEmpty() && $recommendedProduct->tags->isNotEmpty()) {
                $commonTags = $product->tags->pluck('id')
                    ->intersect($recommendedProduct->tags->pluck('id'));
                if ($commonTags->isNotEmpty()) {
                    $score += $commonTags->count() * 10;
                    $matches[] = 'tags';
                }
            }

            // Score by publisher (8 points)
            if ($product->publisher && $recommendedProduct->publisher) {
                if (strtolower(trim($product->publisher)) === strtolower(trim($recommendedProduct->publisher))) {
                    $score += 8;
                    $matches[] = 'publisher';
                }
            }

            // Score by author (8 points)
            if ($product->author && $recommendedProduct->author) {
                if (strtolower(trim($product->author)) === strtolower(trim($recommendedProduct->author))) {
                    $score += 8;
                    $matches[] = 'author';
                }
            }

            // Score by main category (5 points)
            if ($product->category_id && $recommendedProduct->category_id) {
                if ($product->category_id === $recommendedProduct->category_id) {
                    $score += 5;
                    $matches[] = 'category';
                }
            }

            // Score by subcategory (6 points)
            if ($product->subcategory_id && $recommendedProduct->subcategory_id) {
                if ($product->subcategory_id === $recommendedProduct->subcategory_id) {
                    $score += 6;
                    $matches[] = 'subcategory';
                }
            }

            // Add score and matches as attributes
            $recommendedProduct->recommendation_score = $score;
            $recommendedProduct->recommendation_matches = $matches;

            return $recommendedProduct;
        })
        ->filter(function ($product) {
            // Only return products with at least one match
            return $product->recommendation_score > 0;
        })
        ->sortByDesc('recommendation_score')
        ->take($limit)
        ->values();

        return $recommendations;
    }
}

