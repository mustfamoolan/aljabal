<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Offer;

class OfferController extends Controller
{
    public function index()
    {
        $offers = Offer::where('is_active', true)
            ->with(['products' => function($query) {
                $query->where('is_active', true)
                      ->select('products.id', 'products.name', 'products.short_description', 'products.retail_price', 'products.quantity', 'products.author', 'products.publisher')
                      ->with('images');
            }])
            ->orderBy('order')
            ->orderBy('created_at', 'desc')
            ->get();

        // Map the image paths to full URLs
        $offers->transform(function ($offer) {
            if ($offer->image_path) {
                $offer->image_url = url(\Storage::url($offer->image_path));
            } else {
                $offer->image_url = null;
            }
            
            // Format products' images to include 'url' for Flutter
            if ($offer->products) {
                $offer->products->transform(function ($product) {
                    if ($product->images) {
                        $product->images->transform(function ($image) {
                            // $image->image_url is the accessor from ProductImage model
                            $image->url = $image->image_url;
                            return $image;
                        });
                    }
                    return $product;
                });
            }
            
            return $offer;
        });

        return response()->json([
            'status' => 'success',
            'data' => $offers
        ]);
    }
}
