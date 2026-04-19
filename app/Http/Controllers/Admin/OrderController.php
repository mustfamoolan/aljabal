<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\Orders\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
        protected \App\Services\GatewayIntegrationService $gatewayService
    ) {
    }

    /**
     * Display a listing of all orders.
     */
    public function index(Request $request): View
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isEmployee()) {
            abort(403);
        }

        $query = Order::with('orderItems.product', 'representative', 'createdBy', 'governorate', 'district')
            ->latest();

        // Filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('representative_id')) {
            $query->where('representative_id', $request->representative_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(15);
        $waseetStatuses = $this->gatewayService->getWaseetStatuses();

        return view('admin.orders.index', compact('orders', 'waseetStatuses'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): View
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isEmployee()) {
            abort(403);
        }

        $order->load(['orderItems.product', 'representative', 'createdBy', 'governorate', 'district']);
        
        $waseetDetails = [];
        if ($order->waseet_order_id) {
            $waseetDetails = $this->gatewayService->getWaseetOrderDetails($order->waseet_order_id);
        }

        return view('admin.orders.show', compact('order', 'waseetDetails'));
    }

    /**
     * Send Order to Al-Waseet and update status to prepared.
     */
    public function sendToWaseet(Order $order): RedirectResponse
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isEmployee()) {
            abort(403);
        }

        try {
            $result = $this->gatewayService->sendToWaseet($order);

            if ($result['success'] ?? false) {
                // Automate Status Change to Prepared
                $this->orderService->changeOrderStatus($order, OrderStatus::PREPARED, auth()->user());

                return back()
                    ->with('success', 'تم تجهيز الطلب وإرساله للوسيط بنجاح!');
            }

            return back()->withErrors(['error' => $result['message'] ?? 'فشل إرسال الطلب للوسيط.']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'حدث خطأ أثناء الاتصال بالبوابة: ' . $e->getMessage()]);
        }
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isEmployee()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:new,prepared,completed,cancelled,returned,replaced'],
        ]);

        try {
            $status = OrderStatus::from($validated['status']);
            $this->orderService->changeOrderStatus($order, $status, auth()->user());

            return back()->with('success', 'تم تحديث حالة الطلب بنجاح.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified order.
     */
    public function edit(Order $order): View
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isEmployee()) {
            abort(403);
        }

        $order->load('orderItems.product', 'representative', 'createdBy');

        return view('admin.orders.edit', compact('order'));
    }

    /**
     * Update the specified order.
     */
    public function update(Request $request, Order $order): RedirectResponse
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isEmployee()) {
            abort(403);
        }

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_address' => ['required', 'string'],
            'customer_phone' => ['required', 'string', 'max:255'],
            'customer_phone_2' => ['nullable', 'string', 'max:255'],
            'customer_social_media' => ['nullable', 'string', 'max:255'],
            'customer_notes' => ['nullable', 'string'],
        ]);

        try {
            $order->update($validated);

            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'تم تحديث الطلب بنجاح.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
    /**
     * Store a new order from admin/employee.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isEmployee()) {
            abort(403);
        }

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
            'representative_id' => ['nullable', 'exists:representatives,id'],
            'is_withdrawal_order' => ['boolean'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.customer_price' => ['required', 'numeric', 'min:0.01'],
        ]);

        try {
            $user = auth()->user();
            $representative = null;
            if ($request->representative_id) {
                $representative = \App\Models\Representative::find($request->representative_id);
            }

            // Create order
            $order = $this->orderService->createOrder(
                $validated,
                $representative,
                $user
            );

            // Add items to order
            foreach ($validated['items'] as $item) {
                $product = \App\Models\Product::findOrFail($item['product_id']);
                $this->orderService->addItemToOrder(
                    $order,
                    $product,
                    $item['quantity'],
                    (float) $item['customer_price']
                );
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم إنشاء الطلب بنجاح',
                    'order' => $order->load('orderItems.product'),
                ]);
            }

            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'تم إنشاء الطلب بنجاح.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
}
