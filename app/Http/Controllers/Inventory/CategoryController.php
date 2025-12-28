<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreCategoryRequest;
use App\Http\Requests\Inventory\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\Inventory\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse
    {
        if ($request->has('tree')) {
            $categories = $this->categoryService->getCategoryTree();
        } else {
            $filters = $request->only(['search', 'parent_id', 'is_active', 'per_page']);
            $categories = $this->categoryService->getAllCategories($filters);
        }

        if ($request->expectsJson()) {
            return response()->json($categories);
        }

        return view('inventory.categories.list', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $parentCategories = \App\Models\Category::whereNull('parent_id')->where('is_active', true)->get();
        return view('inventory.categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse|JsonResponse
    {
        $data = $request->validated();
        
        // Remove category_type from data as it's not a database field
        unset($data['category_type']);
        
        $category = $this->categoryService->createCategory($data);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'تم إنشاء الفئة بنجاح',
                'category' => $category,
            ], 201);
        }

        return redirect()->route('inventory.categories.index')
            ->with('success', 'تم إنشاء الفئة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category): View|JsonResponse
    {
        $category->load(['parent', 'children', 'products']);

        if (request()->expectsJson()) {
            return response()->json($category);
        }

        return view('inventory.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category): View
    {
        $category->load(['parent', 'children']);
        $parentCategories = \App\Models\Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->where('is_active', true)
            ->get();

        return view('inventory.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse|JsonResponse
    {
        try {
            $category = $this->categoryService->updateCategory($category, $request->validated());

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'تم تحديث الفئة بنجاح',
                    'category' => $category,
                ]);
            }

            return redirect()->route('inventory.categories.index')
                ->with('success', 'تم تحديث الفئة بنجاح');
        } catch (\InvalidArgumentException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Get subcategories for a given category.
     */
    public function getSubcategories(Category $category): JsonResponse
    {
        $subcategories = $category->children()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($subcategories);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): RedirectResponse|JsonResponse
    {
        try {
            $this->categoryService->deleteCategory($category);

            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'تم حذف الفئة بنجاح',
                ]);
            }

            return redirect()->route('inventory.categories.index')
                ->with('success', 'تم حذف الفئة بنجاح');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
