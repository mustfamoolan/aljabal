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
                      ->select('products.id', 'products.name', 'products.description', 'products.price', 'products.stock', 'products.author', 'products.publisher')
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
            return $offer;
        });

        return response()->json([
            'status' => 'success',
            'data' => $offers
        ]);
    }
}
