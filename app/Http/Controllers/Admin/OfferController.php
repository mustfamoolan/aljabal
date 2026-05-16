<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Offer;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use App\Services\FirebaseNotificationService; // Will create or use existing
use App\Models\Notification;

class OfferController extends Controller
{
    public function index()
    {
        $offers = Offer::withCount('products')->orderBy('order')->get();
        return view('admin.offers.index', compact('offers'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->select('id', 'name', 'author', 'publisher')->get();
        return view('admin.offers.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'order' => 'integer|min:0',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id'
        ]);

        $offer = new Offer();
        $offer->title = $validated['title'];
        $offer->description = $validated['description'] ?? null;
        $offer->price = $validated['price'] ?? null;
        $offer->order = $validated['order'] ?? 0;
        $offer->is_active = $request->has('is_active');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('offers', 'public');
            $offer->image_path = $path;
        }

        $offer->save();

        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $index => $file) {
                $path = $file->store('offers/gallery', 'public');
                $offer->images()->create([
                    'image_path' => $path,
                    'image_order' => $index
                ]);
            }
        }

        if (isset($validated['product_ids'])) {
            $offer->products()->sync($validated['product_ids']);
        }

        // Send Firebase Notification
        try {
            app(\App\Services\Notifications\NotificationService::class)->sendNewOfferNotification($offer);
        } catch (\Throwable $e) {
            \Log::error('Firebase Offer Notification Error: ' . $e->getMessage());
        }

        return redirect()->route('admin.offers.index')->with('success', 'تم إضافة العرض بنجاح وإرسال إشعار للمناديب');
    }

    public function edit(Offer $offer)
    {
        $products = Product::where('is_active', true)->select('id', 'name', 'author', 'publisher')->get();
        $offer->load(['products', 'images']);
        return view('admin.offers.edit', compact('offer', 'products'));
    }

    public function update(Request $request, Offer $offer)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'order' => 'integer|min:0',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'delete_images' => 'nullable|array',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id'
        ]);

        $offer->title = $validated['title'];
        $offer->description = $validated['description'] ?? null;
        $offer->price = $validated['price'] ?? null;
        $offer->order = $validated['order'] ?? 0;
        $offer->is_active = $request->has('is_active');

        if ($request->hasFile('image')) {
            if ($offer->image_path) {
                Storage::disk('public')->delete($offer->image_path);
            }
            $path = $request->file('image')->store('offers', 'public');
            $offer->image_path = $path;
        }

        $offer->save();

        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $imageId) {
                $image = $offer->images()->find($imageId);
                if ($image) {
                    Storage::disk('public')->delete($image->image_path);
                    $image->delete();
                }
            }
        }

        if ($request->hasFile('gallery_images')) {
            $currentCount = $offer->images()->count();
            foreach ($request->file('gallery_images') as $index => $file) {
                $path = $file->store('offers/gallery', 'public');
                $offer->images()->create([
                    'image_path' => $path,
                    'image_order' => $currentCount + $index
                ]);
            }
        }

        if (isset($validated['product_ids'])) {
            $offer->products()->sync($validated['product_ids']);
        } else {
            $offer->products()->detach();
        }

        return redirect()->route('admin.offers.index')->with('success', 'تم تحديث العرض بنجاح');
    }

    public function destroy(Offer $offer)
    {
        foreach ($offer->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        if ($offer->image_path) {
            Storage::disk('public')->delete($offer->image_path);
        }
        $offer->delete();
        return redirect()->route('admin.offers.index')->with('success', 'تم حذف العرض بنجاح');
    }
}
