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

        $orders = Order::where('representative_id', $representativeId)
            ->where('created_at', '>=', $today)
            ->whereNotIn('status', ['cancelled', 'returned'])
            ->get();

        $stats = [
            'sales_today' => (float) $orders->sum('total_amount'),
            'orders_today' => $orders->count(),
            'profit_today' => (float) $orders->sum('final_profit'),
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

        // Fetch movements (logs) from Gateway if connected
        $movements = [];
        if ($order->waseet_order_id) {
            try {
                $gatewayService = app(\App\Services\GatewayIntegrationService::class);
                $waseetDetails = $gatewayService->getWaseetOrderDetails($order->waseet_order_id);
                // Al-Waseet uses 'logs' for tracking history
                $movements = $waseetDetails['logs'] ?? $waseetDetails['movements'] ?? $waseetDetails['tracking'] ?? [];
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to fetch movements for order {$order->id}: " . $e->getMessage());
            }
        }

        return response()->json([
            'data' => new OrderResource($order),
            'order_movements' => $movements,
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
            'is_withdrawal_order' => ['nullable', 'boolean'],
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

    /**
     * Get recent activities (unified orders and transactions) for the representative.
     */
    public function activities(Request $request): JsonResponse
    {
        $representativeId = auth()->id();
        $limit = $request->get('limit', 10);
        $perPage = $request->get('per_page', 20);

        // Fetch Orders
        $orders = Order::where('representative_id', $representativeId)
            ->latest()
            ->limit(50) // Limit initial pool for performance when merging
            ->get()
            ->map(function ($order) {
                return [
                    'id' => 'order_' . $order->id,
                    'original_id' => $order->id,
                    'type' => 'order',
                    'title' => 'طلب جديد #' . $order->id,
                    'description' => 'من: ' . ($order->customer_name ?? 'عميل غير معروف'),
                    'amount' => (float) $order->total_amount,
                    'status' => $order->status->label(),
                    'status_key' => $order->status->value,
                    'date' => $order->created_at->toDateTimeString(),
                    'created_at' => $order->created_at,
                ];
            });

        // Fetch Transactions
        $transactions = \App\Models\RepresentativeTransaction::where('representative_id', $representativeId)
            ->latest()
            ->limit(50)
            ->get()
            ->map(function ($tx) {
                return [
                    'id' => 'tx_' . $tx->id,
                    'original_id' => $tx->id,
                    'type' => 'transaction',
                    'title' => $tx->type->getLabel(),
                    'description' => $tx->description,
                    'amount' => (float) $tx->amount,
                    'status' => $tx->status->getLabel(),
                    'status_key' => $tx->status->value,
                    'date' => $tx->created_at->toDateTimeString(),
                    'created_at' => $tx->created_at,
                ];
            });

        // Merge and Sort
        $activities = $orders->concat($transactions)
            ->sortByDesc('created_at')
            ->values();

        // Manual pagination if requested for "View All"
        if ($request->has('page')) {
            $page = $request->get('page', 1);
            $pagedData = $activities->forPage($page, $perPage)->values();
            
            return response()->json([
                'data' => $pagedData,
                'meta' => [
                    'total' => $activities->count(),
                    'per_page' => $perPage,
                    'current_page' => (int)$page,
                    'last_page' => (int) ceil($activities->count() / $perPage),
                ]
            ]);
        }

        return response()->json([
            'data' => $activities->take($limit),
        ]);
    }
}
