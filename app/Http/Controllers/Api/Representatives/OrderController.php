<?php

namespace App\Http\Controllers\Api\Representatives;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\OrderResource;
use App\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Get statistics for the representative.
     */
    public function statistics(): JsonResponse
    {
        $representativeId = auth()->id();
        $today = now()->startOfDay();

        $stats = [
            'sales_today' => (float) Order::where('representative_id', $representativeId)
                ->where('created_at', '>=', $today)
                ->whereNotIn('status', ['cancelled', 'returned'])
                ->sum('total_amount'),
            'orders_today' => Order::where('representative_id', $representativeId)
                ->where('created_at', '>=', $today)
                ->count(),
            'profit_today' => (float) Order::where('representative_id', $representativeId)
                ->where('created_at', '>=', $today)
                ->whereNotIn('status', ['cancelled', 'returned'])
                ->sum('total_profit'),
        ];

        return response()->json($stats);
    }

    /**
     * Display a listing of orders for the authenticated representative.
     */
    public function index(Request $request): JsonResponse
    {
        $representativeId = auth()->id();
        
        $query = Order::with(['orderItems.product', 'governorate', 'district'])
            ->where('representative_id', $representativeId)
            ->latest();

        // Filters
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 15);
        $orders = $query->paginate($perPage);

        return response()->json([
            'data' => OrderResource::collection($orders->items()),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
            'stats' => [
                'total' => Order::where('representative_id', $representativeId)->count(),
                'pending' => Order::where('representative_id', $representativeId)
                    ->whereIn('status', ['new', 'prepared'])->count(),
                'completed' => Order::where('representative_id', $representativeId)
                    ->where('status', 'completed')->count(),
            ],
        ]);
    }

    /**
     * Display the specified order for the representative.
     */
    public function show(Order $order): JsonResponse
    {
        // Ensure the order belongs to this representative
        if ($order->representative_id !== auth()->id()) {
            return response()->json(['message' => 'غير مصرح لك بالوصول لهذا الطلب'], 403);
        }

        $order->load(['orderItems.product', 'governorate', 'district', 'gift', 'giftBox']);

        return response()->json([
            'data' => new OrderResource($order),
        ]);
    }

    /**
     * Get data required for order checkout (governorates, gifts, boxes).
     */
    public function checkout(): JsonResponse
    {
        $representative = auth()->user();
        
        // Calculate preparation commission for this representative
        $commission = \App\Models\OrderPreparationCommissionSetting::getCommissionForOrder(new \App\Models\Order(['representative_id' => $representative->id]));

        return response()->json([
            'governorate_id' => $representative->governorate_id, // Useful for defaulting
            'governorates' => \App\Models\Governorate::active()->orderBy('name')->get(['id', 'name']),
            'gifts' => \App\Models\GiftSetting::gifts()->active()->orderBy('name')->get(),
            'giftBoxes' => \App\Models\GiftSetting::giftBoxes()->active()->orderBy('min_books')->get(),
            'preparation_commission' => (float) $commission,
        ]);
    }

    /**
     * Get districts for a governorate.
     */
    public function getDistricts($governorateId): JsonResponse
    {
        try {
            $districts = \App\Models\District::where('governorate_id', $governorateId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']);

            return response()->json($districts);
        } catch (\Exception $e) {
            return response()->json(['message' => 'حدث خطأ أثناء جلب المناطق'], 500);
        }
    }

    /**
     * Store a new order.
     */
    public function store(Request $request): JsonResponse
    {
        $representative = auth()->user();

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_address' => ['required', 'string'],
            'customer_phone' => ['required', 'string', 'max:255'],
            'customer_phone_2' => ['nullable', 'string', 'max:255'],
            'customer_social_media' => ['nullable', 'string', 'max:255'],
            'customer_notes' => ['nullable', 'string'],
            'governorate_id' => ['required', 'exists:governorates,id'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'gift_id' => ['nullable', 'exists:gift_settings,id'],
            'gift_box_id' => ['nullable', 'exists:gift_settings,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.customer_price' => ['required', 'numeric', 'min:0.01'],
        ]);

        try {
            $orderService = app(\App\Services\Orders\OrderService::class);
            
            // Create order
            $order = $orderService->createOrder(
                $validated,
                $representative,
                null
            );

            // Add items to order
            foreach ($validated['items'] as $item) {
                $product = \App\Models\Product::findOrFail($item['product_id']);
                $orderService->addItemToOrder(
                    $order,
                    $product,
                    $item['quantity'],
                    (float) $item['customer_price']
                );
            }

            return response()->json([
                'message' => 'تم إنشاء الطلب بنجاح',
                'order' => new OrderResource($order),
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
