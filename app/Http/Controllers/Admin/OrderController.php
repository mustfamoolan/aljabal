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
        protected OrderService $orderService
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

        $query = Order::with('orderItems.product', 'representative', 'createdBy')
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

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): View
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $order->load('orderItems.product', 'representative', 'createdBy');

        return view('admin.orders.show', compact('order'));
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
