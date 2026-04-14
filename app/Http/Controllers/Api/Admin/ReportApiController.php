<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Enums\OrderStatus;
use App\Enums\TransactionType;
use App\Enums\TransactionStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Expense;
use App\Models\Representative;
use App\Models\RepresentativeTransaction;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportApiController extends Controller
{
    public function index(Request $request)
    {
        $period      = $request->input('period', 'month'); // day | week | month | custom
        $repId       = $request->input('representative_id'); // optional filter
        $startDate   = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate     = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Auto-adjust dates by period if not custom
        if ($period === 'day' && !$request->has('start_date')) {
            $startDate = now()->startOfDay()->format('Y-m-d');
            $endDate   = now()->endOfDay()->format('Y-m-d');
        } elseif ($period === 'week' && !$request->has('start_date')) {
            $startDate = now()->startOfWeek()->format('Y-m-d');
            $endDate   = now()->endOfWeek()->format('Y-m-d');
        }

        // ─── Base Orders Query (COMPLETED) ──────────────────────────────────
        $ordersQuery = Order::where('status', OrderStatus::COMPLETED)
            ->whereDate('completed_at', '>=', $startDate)
            ->whereDate('completed_at', '<=', $endDate);

        if ($repId) {
            $ordersQuery->where('representative_id', $repId);
        }

        // ─── Base OrderItems Query ───────────────────────────────────────────
        $itemsQuery = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', OrderStatus::COMPLETED)
            ->whereDate('orders.completed_at', '>=', $startDate)
            ->whereDate('orders.completed_at', '<=', $endDate);

        if ($repId) {
            $itemsQuery->where('orders.representative_id', $repId);
        }

        // ─── 1. Summary ──────────────────────────────────────────────────────
        $totalRevenue      = (float) (clone $ordersQuery)->sum('total_amount');
        $totalGrossProfit  = (float) (clone $ordersQuery)->sum('final_profit');
        $totalOrdersCount  = (int)   (clone $ordersQuery)->count();

        $expensesQuery = Expense::whereDate('expense_date', '>=', $startDate)
            ->whereDate('expense_date', '<=', $endDate);
        $totalExpenses = (float) $expensesQuery->sum('amount');
        $netProfit     = $totalGrossProfit - $totalExpenses;

        // ─── 2. Top Books by Sales (qty) ─────────────────────────────────────
        $topBooksBySales = (clone $itemsQuery)
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('product_images', function ($join) {
                $join->on('product_images.product_id', '=', 'products.id')
                     ->whereRaw('product_images.id = (SELECT id FROM product_images pi2 WHERE pi2.product_id = products.id ORDER BY pi2.image_order ASC LIMIT 1)');
            })
            ->selectRaw('
                products.id,
                products.name,
                products.author,
                product_images.image_path as image_path,
                SUM(order_items.quantity) as total_qty,
                SUM(order_items.subtotal) as total_revenue,
                SUM(order_items.profit_subtotal) as total_profit
            ')
            ->groupBy('products.id', 'products.name', 'products.author', 'product_images.image_path')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get()
            ->map(fn($b) => [
                'id'            => $b->id,
                'name'          => $b->name,
                'author'        => $b->author,
                'image_url'     => $b->image_path ? storage_url($b->image_path) : null,
                'sales_qty'     => (int) $b->total_qty,
                'total_revenue' => (float) $b->total_revenue,
                'total_profit'  => (float) $b->total_profit,
            ]);

        // ─── 3. Top Books by Revenue ─────────────────────────────────────────
        $topBooksByRevenue = (clone $itemsQuery)
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('product_images', function ($join) {
                $join->on('product_images.product_id', '=', 'products.id')
                     ->whereRaw('product_images.id = (SELECT id FROM product_images pi2 WHERE pi2.product_id = products.id ORDER BY pi2.image_order ASC LIMIT 1)');
            })
            ->selectRaw('
                products.id,
                products.name,
                products.author,
                product_images.image_path as image_path,
                SUM(order_items.quantity) as total_qty,
                SUM(order_items.subtotal) as total_revenue,
                SUM(order_items.profit_subtotal) as total_profit
            ')
            ->groupBy('products.id', 'products.name', 'products.author', 'product_images.image_path')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get()
            ->map(fn($b) => [
                'id'            => $b->id,
                'name'          => $b->name,
                'author'        => $b->author,
                'image_url'     => $b->image_path ? storage_url($b->image_path) : null,
                'sales_qty'     => (int) $b->total_qty,
                'total_revenue' => (float) $b->total_revenue,
                'total_profit'  => (float) $b->total_profit,
            ]);

        // ─── 4. Top Books by Profit ──────────────────────────────────────────
        $topBooksByProfit = (clone $itemsQuery)
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('product_images', function ($join) {
                $join->on('product_images.product_id', '=', 'products.id')
                     ->whereRaw('product_images.id = (SELECT id FROM product_images pi2 WHERE pi2.product_id = products.id ORDER BY pi2.image_order ASC LIMIT 1)');
            })
            ->selectRaw('
                products.id,
                products.name,
                products.author,
                product_images.image_path as image_path,
                SUM(order_items.quantity) as total_qty,
                SUM(order_items.subtotal) as total_revenue,
                SUM(order_items.profit_subtotal) as total_profit
            ')
            ->groupBy('products.id', 'products.name', 'products.author', 'product_images.image_path')
            ->orderByDesc('total_profit')
            ->limit(5)
            ->get()
            ->map(fn($b) => [
                'id'            => $b->id,
                'name'          => $b->name,
                'author'        => $b->author,
                'image_url'     => $b->image_path ? storage_url($b->image_path) : null,
                'sales_qty'     => (int) $b->total_qty,
                'total_revenue' => (float) $b->total_revenue,
                'total_profit'  => (float) $b->total_profit,
            ]);

        // ─── 5. Top Authors by Sales ─────────────────────────────────────────
        $topAuthors = (clone $itemsQuery)
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereNotNull('products.author')
            ->selectRaw('products.author, SUM(order_items.quantity) as total_qty, SUM(order_items.subtotal) as total_revenue')
            ->groupBy('products.author')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get()
            ->map(fn($a) => [
                'author'        => $a->author,
                'sales_qty'     => (int) $a->total_qty,
                'total_revenue' => (float) $a->total_revenue,
            ]);

        // ─── 6. Top Publishers by Sales ──────────────────────────────────────
        $topPublishers = (clone $itemsQuery)
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereNotNull('products.publisher')
            ->selectRaw('products.publisher, SUM(order_items.quantity) as total_qty, SUM(order_items.subtotal) as total_revenue')
            ->groupBy('products.publisher')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get()
            ->map(fn($p) => [
                'publisher'     => $p->publisher,
                'sales_qty'     => (int) $p->total_qty,
                'total_revenue' => (float) $p->total_revenue,
            ]);

        // ─── 7. Section Profits (Parent Category) ────────────────────────────
        $sectionProfits = (clone $itemsQuery)
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('categories as parents', 'categories.parent_id', '=', 'parents.id')
            ->selectRaw('COALESCE(parents.name, categories.name) as section_name, SUM(order_items.profit_subtotal) as profit')
            ->groupBy('section_name')
            ->orderByDesc('profit')
            ->get();

        // ─── 8. Rep Performance (Sales + Balance + Withdrawals) ──────────────
        $repPerfQuery = Order::where('status', OrderStatus::COMPLETED)
            ->whereDate('completed_at', '>=', $startDate)
            ->whereDate('completed_at', '<=', $endDate);

        if ($repId) {
            $repPerfQuery->where('representative_id', $repId);
        }

        $repPerformance = $repPerfQuery
            ->with(['representative' => fn($q) => $q->select('id', 'name', 'balance', 'image')])
            ->selectRaw('representative_id, SUM(final_profit) as total_profit, SUM(total_amount) as total_revenue, COUNT(*) as orders_count')
            ->groupBy('representative_id')
            ->orderByDesc('total_profit')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $rep = $row->representative;
                if (!$rep) return null;

                $totalWithdrawals = (float) WithdrawalRequest::where('representative_id', $rep->id)
                    ->where('status', 'approved')
                    ->sum('amount');

                return [
                    'representative_id'  => $row->representative_id,
                    'name'               => $rep->name,
                    'image_url'          => $rep->image ? storage_url($rep->image) : null,
                    'orders_count'       => (int) $row->orders_count,
                    'total_revenue'      => (float) $row->total_revenue,
                    'total_profit'       => (float) $row->total_profit,
                    'current_balance'    => (float) $rep->balance,
                    'total_withdrawals'  => $totalWithdrawals,
                ];
            })
            ->filter()
            ->values();

        // ─── 9. Rep Balance Summary ───────────────────────────────────────────
        $repsForBalance = $repId
            ? Representative::where('id', $repId)->get()
            : Representative::all();

        $totalRepBalance = (float) $repsForBalance->sum('balance');
        $totalRepWithdrawals = (float) WithdrawalRequest::whereIn('representative_id', $repsForBalance->pluck('id'))
            ->where('status', 'approved')
            ->sum('amount');

        // ─── 10. Expense Breakdown ────────────────────────────────────────────
        $expenseCategories = Expense::whereDate('expense_date', '>=', $startDate)
            ->whereDate('expense_date', '<=', $endDate)
            ->selectRaw('COALESCE(category, "عام") as category_name, SUM(amount) as total')
            ->groupBy('category_name')
            ->orderByDesc('total')
            ->get();

        // ─── 11. Chart Data ───────────────────────────────────────────────────
        $chartData = $this->buildChartData($startDate, $endDate, $repId);

        // ─── Response ─────────────────────────────────────────────────────────
        return response()->json([
            'summary' => [
                'total_revenue'      => $totalRevenue,
                'total_gross_profit' => $totalGrossProfit,
                'total_expenses'     => $totalExpenses,
                'net_profit'         => $netProfit,
                'total_orders'       => $totalOrdersCount,
            ],
            'section_profits'        => $sectionProfits,
            'rep_performance'        => $repPerformance,
            'expense_categories'     => $expenseCategories,
            'top_books_by_sales'     => $topBooksBySales,
            'top_books_by_revenue'   => $topBooksByRevenue,
            'top_books_by_profit'    => $topBooksByProfit,
            'top_authors'            => $topAuthors,
            'top_publishers'         => $topPublishers,
            'rep_balance_summary' => [
                'total_balance'      => $totalRepBalance,
                'total_withdrawals'  => $totalRepWithdrawals,
            ],
            'chart_data'             => $chartData,
            'dates' => [
                'start'  => $startDate,
                'end'    => $endDate,
                'period' => $period,
            ],
        ]);
    }

    /**
     * Build weekly chart data points between two dates.
     */
    private function buildChartData(string $startDate, string $endDate, ?string $repId): array
    {
        $start = \Carbon\Carbon::parse($startDate);
        $end   = \Carbon\Carbon::parse($endDate);
        $diff  = $start->diffInDays($end);

        // Group by week if range > 14 days, else by day
        $groupFormat = $diff > 60 ? '%Y-%m' : ($diff > 14 ? '%Y-%u' : '%Y-%m-%d');
        $labelFormat = $diff > 60 ? 'M Y' : ($diff > 14 ? 'W' : 'd/m');

        $revenueData = Order::where('status', OrderStatus::COMPLETED)
            ->whereDate('completed_at', '>=', $startDate)
            ->whereDate('completed_at', '<=', $endDate)
            ->when($repId, fn($q) => $q->where('representative_id', $repId))
            ->selectRaw("DATE_FORMAT(completed_at, '{$groupFormat}') as period, SUM(total_amount) as total, SUM(final_profit) as profit")
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->keyBy('period');

        $expenseData = Expense::whereDate('expense_date', '>=', $startDate)
            ->whereDate('expense_date', '<=', $endDate)
            ->selectRaw("DATE_FORMAT(expense_date, '{$groupFormat}') as period, SUM(amount) as total")
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->keyBy('period');

        $allPeriods = $revenueData->keys()->merge($expenseData->keys())->unique()->sort()->values();

        return $allPeriods->map(function ($period) use ($revenueData, $expenseData, $labelFormat, $groupFormat) {
            $rev  = $revenueData->get($period);
            $exp  = $expenseData->get($period);
            $label = $period;
            try {
                if ($groupFormat === '%Y-%m-%d') {
                    $label = \Carbon\Carbon::createFromFormat('Y-m-d', $period)->format('d/m');
                } elseif ($groupFormat === '%Y-%m') {
                    $label = \Carbon\Carbon::createFromFormat('Y-m', $period)->format('M');
                } else {
                    $label = 'أسبوع ' . (int) substr($period, -2);
                }
            } catch (\Exception $e) {}

            return [
                'label'    => $label,
                'revenue'  => (float) ($rev?->total ?? 0),
                'profit'   => (float) ($rev?->profit ?? 0),
                'expenses' => (float) ($exp?->total ?? 0),
            ];
        })->values()->toArray();
    }
}
