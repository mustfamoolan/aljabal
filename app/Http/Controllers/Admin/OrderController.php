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
        if (!auth()->user()->isAdmin()) {
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
        if (!auth()->user()->isAdmin()) {
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
        if (!auth()->user()->isAdmin()) {
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
        if (!auth()->user()->isAdmin()) {
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
        if (!auth()->user()->isAdmin()) {
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
        if (!auth()->user()->isAdmin()) {
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
}
