<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\AddQuantityRequest;
use App\Http\Requests\Inventory\StoreProductRequest;
use App\Http\Requests\Inventory\UpdateProductRequest;
use App\Models\Product;
use App\Models\ProductImage;
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
        // Check permission (allow if user is admin or has permission)
        if (!auth()->user()->isAdmin() && !auth()->user()->can('inventory.products.view')) {
            abort(403, 'ليس لديك صلاحية لعرض المنتجات');
        }

        $filters = $request->only(['search', 'category_id', 'supplier_id', 'product_type', 'is_active', 'low_stock', 'per_page', 'author', 'publisher', 'tag_id']);
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
        // Check permission (allow if user is admin or has permission)
        if (!auth()->user()->isAdmin() && !auth()->user()->can('inventory.products.create')) {
            abort(403, 'ليس لديك صلاحية لإنشاء منتجات');
        }

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
        // Check permission (allow if user is admin or has permission)
        if (!auth()->user()->isAdmin() && !auth()->user()->can('inventory.products.create')) {
            abort(403, 'ليس لديك صلاحية لإنشاء منتجات');
        }

        $data = $request->validated();
        // If user is not admin, remove purchase_price from data
        if (!auth()->user()->isAdmin()) {
            unset($data['purchase_price']);
        }

        $product = $this->productService->createProduct($data);

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
        // Check permission (allow if user is admin or has permission)
        if (!auth()->user()->isAdmin() && !auth()->user()->can('inventory.products.update')) {
            abort(403, 'ليس لديك صلاحية لتعديل المنتجات');
        }

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
        // Check permission (allow if user is admin or has permission)
        if (!auth()->user()->isAdmin() && !auth()->user()->can('inventory.products.update')) {
            abort(403, 'ليس لديك صلاحية لتعديل المنتجات');
        }

        // If user is not admin, remove purchase_price from data
        $data = $request->validated();
        if (!auth()->user()->isAdmin()) {
            unset($data['purchase_price']);
        }

        $product = $this->productService->updateProduct($product, $data);

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
        // Check permission (allow if user is admin or has permission)
        if (!auth()->user()->isAdmin() && !auth()->user()->can('inventory.products.delete')) {
            abort(403, 'ليس لديك صلاحية لحذف المنتجات');
        }

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
        // Check permission (allow if user is admin or has permission)
        if (!auth()->user()->isAdmin() && !auth()->user()->can('inventory.products.update')) {
            abort(403, 'ليس لديك صلاحية لتعديل المنتجات');
        }

        $data = $request->validated();
        // If user is not admin, remove purchase_price from data
        if (!auth()->user()->isAdmin()) {
            unset($data['purchase_price']);
        }

        $purchaseHistory = $this->productService->addQuantity($product, $request->quantity, $data);

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
        $filters = $request->only(['search', 'category_id', 'supplier_id', 'product_type', 'is_active', 'low_stock', 'per_page', 'author', 'publisher', 'tag_id']);
        $products = $this->productService->getAllProducts($filters);
        $categories = \App\Models\Category::where('is_active', true)->get();

        if ($request->expectsJson()) {
            return response()->json($products);
        }

        return view('inventory.products.grid', compact('products', 'categories'));
    }

    /**
     * Delete a product image
     */
    public function deleteImage(Product $product, ProductImage $image): JsonResponse
    {
        // Check permission (allow if user is admin or has permission)
        if (!auth()->user()->isAdmin() && !auth()->user()->can('inventory.products.update')) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية لحذف صور المنتجات',
            ], 403);
        }

        // Ensure the image belongs to the product
        if ($image->product_id !== $product->id) {
            return response()->json([
                'success' => false,
                'message' => 'الصورة لا تنتمي لهذا المنتج',
            ], 404);
        }

        try {
            $this->productService->deleteImage($image);

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الصورة بنجاح',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف الصورة: ' . $e->getMessage(),
            ], 500);
        }
    }
}
