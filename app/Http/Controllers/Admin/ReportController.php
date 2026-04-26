<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        // 1. Basic Stats (Completed Orders only)
        $ordersQuery = \App\Models\Order::where('status', \App\Enums\OrderStatus::SENT_TO_GATEWAY)
            ->whereIn('waseet_status', ['واصل', 'مباع', 'تم تسليم المبالغ', 'تم التسليم للزبون'])
            ->whereDate('updated_at', '>=', $startDate)
            ->whereDate('updated_at', '<=', $endDate);

        $totalRevenue = (float) $ordersQuery->sum('total_amount');
        $totalGrossProfit = (float) $ordersQuery->sum('final_profit');

        $totalExpenses = (float) \App\Models\Expense::whereDate('expense_date', '>=', $startDate)
            ->whereDate('expense_date', '<=', $endDate)
            ->sum('amount');

        $netProfit = $totalGrossProfit - $totalExpenses;

        // 2. Profit by Section (Parent Category)
        $sectionProfits = \App\Models\OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('categories as parents', 'categories.parent_id', '=', 'parents.id')
            ->where('orders.status', \App\Enums\OrderStatus::SENT_TO_GATEWAY)
            ->whereIn('orders.waseet_status', ['واصل', 'مباع', 'تم تسليم المبالغ', 'تم التسليم للزبون'])
            ->whereDate('orders.updated_at', '>=', $startDate)
            ->whereDate('orders.updated_at', '<=', $endDate)
            ->selectRaw('COALESCE(parents.name, categories.name) as section_name, SUM(order_items.profit_subtotal) as profit')
            ->groupBy('section_name')
            ->get();

        // 3. Profit by Branch (Sub Category)
        $branchProfits = \App\Models\OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.status', \App\Enums\OrderStatus::SENT_TO_GATEWAY)
            ->whereIn('orders.waseet_status', ['واصل', 'مباع', 'تم تسليم المبالغ', 'تم التسليم للزبون'])
            ->whereDate('orders.updated_at', '>=', $startDate)
            ->whereDate('orders.updated_at', '<=', $endDate)
            ->selectRaw('categories.name as branch_name, SUM(order_items.profit_subtotal) as profit')
            ->groupBy('branch_name')
            ->get();

        // 4. Rep Performance
        $repPerformance = \App\Models\Order::where('status', \App\Enums\OrderStatus::SENT_TO_GATEWAY)
            ->whereIn('waseet_status', ['واصل', 'مباع', 'تم تسليم المبالغ', 'تم التسليم للزبون'])
            ->whereDate('updated_at', '>=', $startDate)
            ->whereDate('updated_at', '<=', $endDate)
            ->with([
                'representative' => function ($q) {
                    $q->select('id', 'name');
                }
            ])
            ->selectRaw('representative_id, SUM(final_profit) as total_profit, COUNT(*) as orders_count')
            ->groupBy('representative_id')
            ->orderByDesc('total_profit')
            ->take(10)
            ->get();

        // 5. Expense Breakdown
        $expenseCategories = \App\Models\Expense::whereDate('expense_date', '>=', $startDate)
            ->whereDate('expense_date', '<=', $endDate)
            ->selectRaw('COALESCE(category, "عام") as category_name, SUM(amount) as total')
            ->groupBy('category_name')
            ->get();

        return view('admin.reports.index', compact(
            'totalRevenue',
            'totalGrossProfit',
            'totalExpenses',
            'netProfit',
            'sectionProfits',
            'branchProfits',
            'repPerformance',
            'expenseCategories',
            'startDate',
            'endDate'
        ));
    }
}
