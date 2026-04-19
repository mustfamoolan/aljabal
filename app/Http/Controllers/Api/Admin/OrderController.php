<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\OrderResource;
use App\Models\Order;
use App\Services\Orders\OrderService;
use App\Enums\OrderStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
        protected \App\Services\GatewayIntegrationService $gatewayService
    ) {
    }

    /**
     * Display a listing of orders.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Order::with(['orderItems.product', 'representative', 'createdBy', 'governorate', 'district'])
            ->latest();

        // Filters
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('representative_id') && $request->representative_id != '') {
            $query->where('representative_id', $request->representative_id);
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
                'total' => Order::count(),
                'pending' => Order::whereIn('status', ['new', 'prepared'])->count(),
                'completed' => Order::where('status', 'completed')->count(),
            ],
        ]);
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): JsonResponse
    {
        $order->load(['orderItems.product', 'representative', 'createdBy', 'governorate', 'district', 'gift', 'giftBox']);
        
        $waseetDetails = [];
        if ($order->waseet_order_id) {
            $waseetDetails = $this->gatewayService->getWaseetOrderDetails($order->waseet_order_id);
        }

        return response()->json([
            'data' => new OrderResource($order),
            'waseet_details' => $waseetDetails,
        ]);
    }

    /**
     * Update order.
     */
    public function update(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'customer_name' => 'sometimes|string',
            'customer_phone' => 'sometimes|string',
            'customer_phone_2' => 'sometimes|nullable|string',
            'customer_social_media' => 'sometimes|nullable|string',
            'customer_address' => 'sometimes|string',
            'customer_notes' => 'sometimes|nullable|string',
            'governorate_id' => 'sometimes|exists:governorates,id',
            'district_id' => 'sometimes|exists:districts,id',
            'representative_id' => 'sometimes|nullable|exists:representatives,id',
            'gift_id' => 'sometimes|nullable|exists:gift_settings,id',
            'gift_box_id' => 'sometimes|nullable|exists:gift_settings,id',
            'is_withdrawal_order' => 'sometimes|boolean',
            'total_amount' => 'sometimes|numeric',
            'items' => 'sometimes|array',
            'items.*.product_id' => 'required_with:items|exists:products,id',
            'items.*.quantity' => 'required_with:items|integer|min:1',
            'items.*.customer_price' => 'sometimes|numeric',
        ]);

        try {
            $updatedOrder = $this->orderService->updateOrder($order, $validated);

            return response()->json([
                'message' => 'تم تحديث الطلب بنجاح',
                'data' => new OrderResource($updatedOrder->load(['orderItems.product', 'representative', 'createdBy', 'governorate', 'district', 'gift', 'giftBox'])),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:new,prepared,completed,cancelled,returned,replaced'],
        ]);

        try {
            $status = OrderStatus::from($validated['status']);
            $updatedOrder = $this->orderService->changeOrderStatus($order, $status, auth()->user());

            return response()->json([
                'message' => 'تم تحديث حالة الطلب بنجاح',
                'data' => new OrderResource($updatedOrder->load(['orderItems.product', 'representative', 'createdBy', 'governorate', 'district'])),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
    /**
     * Send order to AlWaseet Gateway.
     */
    public function sendToWaseet(Order $order): JsonResponse
    {
        try {
            $result = $this->gatewayService->sendToWaseet($order);

            if ($result['success'] ?? false) {
                // Automate Status Change to Prepared
                $this->orderService->changeOrderStatus($order, OrderStatus::PREPARED, auth()->user());

                return response()->json([
                    'status' => true,
                    'message' => 'تم تجهيز الطلب وإرساله للوسيط بنجاح!',
                    'data' => new OrderResource($order->load(['orderItems.product', 'representative', 'createdBy', 'governorate', 'district'])),
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => $result['message'] ?? 'فشل إرسال الطلب للوسيط.',
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء الاتصال بالبوابة: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get dynamic statuses list from Al-Waseet via Gateway.
     */
    public function waseetStatuses(): JsonResponse
    {
        $statuses = $this->gatewayService->getWaseetStatuses();
        return response()->json([
            'status' => true,
            'data' => $statuses,
        ]);
    }
}
