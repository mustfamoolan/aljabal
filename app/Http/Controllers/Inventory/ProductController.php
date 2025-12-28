<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\AddQuantityRequest;
use App\Http\Requests\Inventory\StoreProductRequest;
use App\Http\Requests\Inventory\UpdateProductRequest;
use App\Models\Product;
use App\Services\Inventory\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse
    {
        $filters = $request->only(['search', 'category_id', 'supplier_id', 'product_type', 'is_active', 'low_stock', 'per_page']);
        $products = $this->productService->getAllProducts($filters);

        if ($request->expectsJson()) {
            return response()->json($products);
        }

        return view('inventory.products.list', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $mainCategories = \App\Models\Category::whereNull('parent_id')->where('is_active', true)->get();
        $suppliers = \App\Models\Supplier::where('is_active', true)->get();
        $tags = \App\Models\Tag::all();

        return view('inventory.products.create', compact('mainCategories', 'suppliers', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): RedirectResponse|JsonResponse
    {
        $product = $this->productService->createProduct($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'تم إنشاء المنتج بنجاح',
                'product' => $product,
            ], 201);
        }

        return redirect()->route('inventory.products.index')
            ->with('success', 'تم إنشاء المنتج بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): View|JsonResponse
    {
        $product = $this->productService->getProduct($product);
        $product->load('subcategory');

        if (request()->expectsJson()) {
            return response()->json($product);
        }

        return view('inventory.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product): View
    {
        $product->load(['category', 'subcategory', 'supplier', 'images', 'tags']);
        $mainCategories = \App\Models\Category::whereNull('parent_id')->where('is_active', true)->get();
        $suppliers = \App\Models\Supplier::where('is_active', true)->get();
        $tags = \App\Models\Tag::all();

        return view('inventory.products.edit', compact('product', 'mainCategories', 'suppliers', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse|JsonResponse
    {
        $product = $this->productService->updateProduct($product, $request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'تم تحديث المنتج بنجاح',
                'product' => $product,
            ]);
        }

        return redirect()->route('inventory.products.index')
            ->with('success', 'تم تحديث المنتج بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse|JsonResponse
    {
        $this->productService->deleteProduct($product);

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'تم حذف المنتج بنجاح',
            ]);
        }

        return redirect()->route('inventory.products.index')
            ->with('success', 'تم حذف المنتج بنجاح');
    }

    /**
     * Add quantity to product
     */
    public function addQuantity(AddQuantityRequest $request, Product $product): RedirectResponse|JsonResponse
    {
        $purchaseHistory = $this->productService->addQuantity($product, $request->quantity, $request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'تم إضافة الكمية بنجاح',
                'purchase_history' => $purchaseHistory,
            ]);
        }

        return back()->with('success', 'تم إضافة الكمية بنجاح');
    }

    /**
     * Get products with low stock
     */
    public function getLowStock(Request $request): View|JsonResponse
    {
        $filters = array_merge($request->only(['per_page']), ['low_stock' => true]);
        $products = $this->productService->getAllProducts($filters);

        if ($request->expectsJson()) {
            return response()->json($products);
        }

        return view('inventory.products.low-stock', compact('products'));
    }

    /**
     * Display products in grid view
     */
    public function grid(Request $request): View|JsonResponse
    {
        $filters = $request->only(['search', 'category_id', 'supplier_id', 'product_type', 'is_active', 'low_stock', 'per_page']);
        $products = $this->productService->getAllProducts($filters);
        $categories = \App\Models\Category::where('is_active', true)->get();

        if ($request->expectsJson()) {
            return response()->json($products);
        }

        return view('inventory.products.grid', compact('products', 'categories'));
    }
}
