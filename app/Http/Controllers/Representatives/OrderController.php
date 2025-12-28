<?php

namespace App\Http\Controllers\Representatives;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Services\Orders\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {
    }

    /**
     * Display the order creation page with products list.
     */
    public function create(Request $request): View
    {
        $representative = auth()->guard('representative')->user();
        
        $query = Product::where('is_active', true)
            ->where('quantity', '>', 0)
            ->with('category', 'subcategory', 'images', 'tags', 'supplier');

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('publisher', 'like', "%{$search}%");
            });
        }

        // Category filter (main category)
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Subcategory filter
        if ($request->has('subcategory_id') && $request->subcategory_id) {
            $query->where('subcategory_id', $request->subcategory_id);
        }

        // Publisher filter
        if ($request->has('publisher') && $request->publisher) {
            $query->where('publisher', 'like', "%{$request->publisher}%");
        }

        // Author filter
        if ($request->has('author') && $request->author) {
            $query->where('author', 'like', "%{$request->author}%");
        }

        // Tags filter
        if ($request->has('tags')) {
            $tags = $request->tags;
            // Handle both array and single value
            if (!is_array($tags)) {
                $tags = [$tags];
            }
            if (count($tags) > 0) {
                $query->whereHas('tags', function ($q) use ($tags) {
                    $q->whereIn('tags.id', $tags);
                });
            }
        }

        // Product type filter
        if ($request->has('product_type') && $request->product_type) {
            $query->where('product_type', $request->product_type);
        }

        // Price range filter
        if ($request->has('min_price') && $request->min_price) {
            $query->where('wholesale_price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && $request->max_price) {
            $query->where('wholesale_price', '<=', $request->max_price);
        }

        $products = $query->paginate(20);
        
        // Get filter options
        $mainCategories = \App\Models\Category::whereNull('parent_id')->where('is_active', true)->get();
        $publishers = Product::where('is_active', true)
            ->whereNotNull('publisher')
            ->where('publisher', '!=', '')
            ->distinct()
            ->pluck('publisher')
            ->sort()
            ->values();
        
        $authors = Product::where('is_active', true)
            ->whereNotNull('author')
            ->where('author', '!=', '')
            ->distinct()
            ->pluck('author')
            ->sort()
            ->values();
        
        $tags = \App\Models\Tag::all();
        
        // Get subcategories if category is selected
        $subcategories = collect();
        if ($request->has('category_id') && $request->category_id) {
            $subcategories = \App\Models\Category::where('parent_id', $request->category_id)
                ->where('is_active', true)
                ->get();
        }

        return view('representatives.orders.create', compact(
            'products', 
            'mainCategories', 
            'subcategories',
            'publishers',
            'authors',
            'tags',
            'representative'
        ));
    }

    /**
     * Store a new order.
     */
    public function store(Request $request): RedirectResponse
    {
        $representative = auth()->guard('representative')->user();

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_address' => ['required', 'string'],
            'customer_phone' => ['required', 'string', 'max:255'],
            'customer_phone_2' => ['nullable', 'string', 'max:255'],
            'customer_social_media' => ['nullable', 'string', 'max:255'],
            'customer_notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.customer_price' => ['required', 'numeric', 'min:0.01'],
        ]);

        try {
            // Create order
            $order = $this->orderService->createOrder(
                $validated,
                $representative,
                null
            );

            // Add items to order
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $this->orderService->addItemToOrder(
                    $order,
                    $product,
                    $item['quantity'],
                    (float) $item['customer_price']
                );
            }

            return redirect()->route('representative.orders.show', $order)
                ->with('success', 'تم إنشاء الطلب بنجاح.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the cart page.
     */
    public function cart(): View
    {
        return view('representatives.orders.cart');
    }

    /**
     * Display the checkout page.
     */
    public function checkout(): View
    {
        return view('representatives.orders.checkout');
    }

    /**
     * Display a listing of representative's orders.
     */
    public function index(Request $request): View
    {
        $representative = auth()->guard('representative')->user();

        $query = Order::where('representative_id', $representative->id)
            ->with('orderItems.product')
            ->latest();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(15);

        return view('representatives.orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): View
    {
        $representative = auth()->guard('representative')->user();

        // Ensure order belongs to representative
        if ($order->representative_id !== $representative->id) {
            abort(403);
        }

        $order->load('orderItems.product', 'representative');

        return view('representatives.orders.show', compact('order'));
    }

    /**
     * Calculate commission for a given total amount.
     */
    public function calculateCommission(Request $request)
    {
        $representative = auth()->guard('representative')->user();
        
        // Check for exception first
        $exception = \App\Models\OrderPreparationCommissionException::getForRepresentative($representative->id);
        if ($exception) {
            $commission = (float) $exception->commission_value;
        } else {
            // Use general settings
            $settings = \App\Models\OrderPreparationCommissionSetting::where('is_active', true)->first();
            $commission = $settings ? (float) $settings->commission_value : 0.0;
        }

        return response()->json([
            'commission' => $commission,
            'formatted_commission' => format_currency($commission),
        ]);
    }
}
