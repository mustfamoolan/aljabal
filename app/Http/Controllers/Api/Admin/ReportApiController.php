<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Enums\OrderStatus;
use App\Enums\WithdrawalStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Expense;
use App\Models\Representative;
use App\Models\WithdrawalRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportApiController extends Controller
{
    public function index(Request $request)
    {
        $period    = $request->input('period', 'month');
        $repId     = $request->input('representative_id');
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');

        // Auto-adjust dates based on period
        if (!$startDate || !$endDate) {
            switch ($period) {
                case 'day':
                    $startDate = now()->startOfDay()->format('Y-m-d');
                    $endDate   = now()->endOfDay()->format('Y-m-d');
                    break;
                case 'week':
                    $startDate = now()->startOfWeek()->format('Y-m-d');
                    $endDate   = now()->endOfWeek()->format('Y-m-d');
                    break;
                default: // month
                    $startDate = now()->startOfMonth()->format('Y-m-d');
                    $endDate   = now()->endOfMonth()->format('Y-m-d');
            }
        }

        // ─── 1. Summary ──────────────────────────────────────────────────────
        $ordersBase = DB::table('orders')
            ->where('status', OrderStatus::SENT_TO_GATEWAY->value)
            ->whereIn('waseet_status', ['واصل', 'مباع', 'تم تسليم المبالغ', 'تم التسليم للزبون'])
            ->whereDate('updated_at', '>=', $startDate)
            ->whereDate('updated_at', '<=', $endDate);

        if ($repId) {
            $ordersBase->where('representative_id', $repId);
        }

        $totalRevenue     = (float) (clone $ordersBase)->sum('total_amount');
        $totalGrossProfit = (float) (clone $ordersBase)->sum('final_profit');
        $totalOrdersCount = (int)   (clone $ordersBase)->count();

        $totalExpenses = (float) DB::table('expenses')
            ->whereDate('expense_date', '>=', $startDate)
            ->whereDate('expense_date', '<=', $endDate)
            ->sum('amount');

        $netProfit = $totalGrossProfit - $totalExpenses;

        // ─── 2. Helper: Base items joined with orders + products ──────────────
        // We'll build this inline for each query to avoid clone issues

        // ─── 3. Top Books by Sales ────────────────────────────────────────────
        $topBooksBySales = $this->queryTopBooks($startDate, $endDate, $repId, 'total_qty');

        // ─── 4. Top Books by Revenue ──────────────────────────────────────────
        $topBooksByRevenue = $this->queryTopBooks($startDate, $endDate, $repId, 'total_revenue');

        // ─── 5. Top Books by Profit ───────────────────────────────────────────
        $topBooksByProfit = $this->queryTopBooks($startDate, $endDate, $repId, 'total_profit');

        // ─── 6. Top Authors by Sales ──────────────────────────────────────────
        $topAuthors = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.status', OrderStatus::SENT_TO_GATEWAY->value)
            ->whereIn('orders.waseet_status', ['واصل', 'مباع', 'تم تسليم المبالغ', 'تم التسليم للزبون'])
            ->whereDate('orders.updated_at', '>=', $startDate)
            ->whereDate('orders.updated_at', '<=', $endDate)
            ->when($repId, fn($q) => $q->where('orders.representative_id', $repId))
            ->whereNotNull('products.author')
            ->where('products.author', '!=', '')
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

        // ─── 7. Top Publishers by Sales ───────────────────────────────────────
        $topPublishers = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.status', OrderStatus::SENT_TO_GATEWAY->value)
            ->whereIn('orders.waseet_status', ['واصل', 'مباع', 'تم تسليم المبالغ', 'تم التسليم للزبون'])
            ->whereDate('orders.updated_at', '>=', $startDate)
            ->whereDate('orders.updated_at', '<=', $endDate)
            ->when($repId, fn($q) => $q->where('orders.representative_id', $repId))
            ->whereNotNull('products.publisher')
            ->where('products.publisher', '!=', '')
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

        // ─── 8. Section Profits (Parent Category) ─────────────────────────────
        $sectionProfits = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('categories as parents', 'categories.parent_id', '=', 'parents.id')
            ->where('orders.status', OrderStatus::SENT_TO_GATEWAY->value)
            ->whereIn('orders.waseet_status', ['واصل', 'مباع', 'تم تسليم المبالغ', 'تم التسليم للزبون'])
            ->whereDate('orders.updated_at', '>=', $startDate)
            ->whereDate('orders.updated_at', '<=', $endDate)
            ->when($repId, fn($q) => $q->where('orders.representative_id', $repId))
            ->selectRaw('COALESCE(parents.name, categories.name) as section_name, SUM(order_items.profit_subtotal) as profit')
            ->groupBy('section_name')
            ->orderByDesc('profit')
            ->get();

        // ─── 9. Rep Performance ────────────────────────────────────────────────
        $repPerformance = [];
        $repRows = DB::table('orders')
            ->where('status', OrderStatus::SENT_TO_GATEWAY->value)
            ->whereIn('waseet_status', ['واصل', 'مباع', 'تم تسليم المبالغ', 'تم التسليم للزبون'])
            ->whereDate('updated_at', '>=', $startDate)
            ->whereDate('updated_at', '<=', $endDate)
            ->when($repId, fn($q) => $q->where('representative_id', $repId))
            ->selectRaw('representative_id, SUM(final_profit) as total_profit, SUM(total_amount) as total_revenue, COUNT(*) as orders_count')
            ->groupBy('representative_id')
            ->orderByDesc('total_profit')
            ->limit(10)
            ->get();

        foreach ($repRows as $row) {
            if (!$row->representative_id) continue;
            $rep = DB::table('representatives')->where('id', $row->representative_id)->first(['id', 'name', 'balance', 'image']);
            if (!$rep) continue;

            $totalWithdrawals = (float) DB::table('withdrawal_requests')
                ->where('representative_id', $rep->id)
                ->where('status', WithdrawalStatus::APPROVED->value)
                ->sum('amount');

            $repPerformance[] = [
                'representative_id' => $row->representative_id,
                'name'              => $rep->name,
                'image_url'         => $rep->image ? storage_url($rep->image) : null,
                'orders_count'      => (int) $row->orders_count,
                'total_revenue'     => (float) $row->total_revenue,
                'total_profit'      => (float) $row->total_profit,
                'current_balance'   => (float) $rep->balance,
                'total_withdrawals' => $totalWithdrawals,
            ];
        }

        // ─── 10. Rep Balance Summary ───────────────────────────────────────────
        if ($repId) {
            $repForBalance = DB::table('representatives')->where('id', $repId)->first(['id', 'balance']);
            $totalRepBalance = $repForBalance ? (float) $repForBalance->balance : 0.0;
            $totalRepWithdrawals = (float) DB::table('withdrawal_requests')
                ->where('representative_id', $repId)
                ->where('status', WithdrawalStatus::APPROVED->value)
                ->sum('amount');
        } else {
            $totalRepBalance = (float) DB::table('representatives')->sum('balance');
            $totalRepWithdrawals = (float) DB::table('withdrawal_requests')
                ->where('status', WithdrawalStatus::APPROVED->value)
                ->sum('amount');
        }

        // ─── 11. Expense Breakdown ─────────────────────────────────────────────
        $expenseCategories = DB::table('expenses')
            ->whereDate('expense_date', '>=', $startDate)
            ->whereDate('expense_date', '<=', $endDate)
            ->selectRaw('COALESCE(category, "عام") as category_name, SUM(amount) as total')
            ->groupBy('category_name')
            ->orderByDesc('total')
            ->get();

        // ─── 12. Chart Data ────────────────────────────────────────────────────
        $chartData = $this->buildChartData($startDate, $endDate, $repId);

        return response()->json([
            'summary' => [
                'total_revenue'      => $totalRevenue,
                'total_gross_profit' => $totalGrossProfit,
                'total_expenses'     => $totalExpenses,
                'net_profit'         => $netProfit,
                'total_orders'       => $totalOrdersCount,
            ],
            'section_profits'      => $sectionProfits,
            'rep_performance'      => $repPerformance,
            'expense_categories'   => $expenseCategories,
            'top_books_by_sales'   => $topBooksBySales,
            'top_books_by_revenue' => $topBooksByRevenue,
            'top_books_by_profit'  => $topBooksByProfit,
            'top_authors'          => $topAuthors,
            'top_publishers'       => $topPublishers,
            'rep_balance_summary'  => [
                'total_balance'     => $totalRepBalance,
                'total_withdrawals' => $totalRepWithdrawals,
            ],
            'chart_data' => $chartData,
            'dates'      => [
                'start'  => $startDate,
                'end'    => $endDate,
                'period' => $period,
            ],
        ]);
    }

    /**
     * Query top books with image — separate function to avoid clone issues.
     */
    private function queryTopBooks(string $startDate, string $endDate, ?string $repId, string $orderBy): \Illuminate\Support\Collection
    {
        $rows = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.status', OrderStatus::SENT_TO_GATEWAY->value)
            ->whereIn('orders.waseet_status', ['واصل', 'مباع', 'تم تسليم المبالغ', 'تم التسليم للزبون'])
            ->whereDate('orders.updated_at', '>=', $startDate)
            ->whereDate('orders.updated_at', '<=', $endDate)
            ->when($repId, fn($q) => $q->where('orders.representative_id', $repId))
            ->selectRaw('
                products.id,
                products.name,
                products.author,
                SUM(order_items.quantity) as total_qty,
                SUM(order_items.subtotal) as total_revenue,
                SUM(order_items.profit_subtotal) as total_profit
            ')
            ->groupBy('products.id', 'products.name', 'products.author')
            ->orderByDesc($orderBy)
            ->limit(5)
            ->get();

        return $rows->map(function ($book) {
            // Get first image for this product
            $image = DB::table('product_images')
                ->where('product_id', $book->id)
                ->orderBy('image_order')
                ->first(['image_path']);

            return [
                'id'            => $book->id,
                'name'          => $book->name,
                'author'        => $book->author,
                'image_url'     => ($image && $image->image_path) ? storage_url($image->image_path) : null,
                'sales_qty'     => (int) $book->total_qty,
                'total_revenue' => (float) $book->total_revenue,
                'total_profit'  => (float) $book->total_profit,
            ];
        });
    }

    /**
     * Build chart data points (revenue + expenses over time).
     */
    private function buildChartData(string $startDate, string $endDate, ?string $repId): array
    {
        $start = Carbon::parse($startDate);
        $end   = Carbon::parse($endDate);
        $diff  = $start->diffInDays($end);

        if ($diff > 60) {
            $groupFormat = '%Y-%m';
        } elseif ($diff > 14) {
            $groupFormat = '%Y-%u'; // week number
        } else {
            $groupFormat = '%Y-%m-%d';
        }

        $revenueRows = DB::table('orders')
            ->where('status', OrderStatus::SENT_TO_GATEWAY->value)
            ->whereIn('waseet_status', ['واصل', 'مباع', 'تم تسليم المبالغ', 'تم التسليم للزبون'])
            ->whereDate('updated_at', '>=', $startDate)
            ->whereDate('updated_at', '<=', $endDate)
            ->when($repId, fn($q) => $q->where('representative_id', $repId))
            ->selectRaw("DATE_FORMAT(updated_at, '{$groupFormat}') as period, SUM(total_amount) as revenue, SUM(final_profit) as profit")
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->keyBy('period');

        $expenseRows = DB::table('expenses')
            ->whereDate('expense_date', '>=', $startDate)
            ->whereDate('expense_date', '<=', $endDate)
            ->selectRaw("DATE_FORMAT(expense_date, '{$groupFormat}') as period, SUM(amount) as expenses")
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->keyBy('period');

        $allPeriods = $revenueRows->keys()
            ->merge($expenseRows->keys())
            ->unique()
            ->sort()
            ->values();

        return $allPeriods->map(function ($period) use ($revenueRows, $expenseRows, $groupFormat) {
            $rev  = $revenueRows->get($period);
            $exp  = $expenseRows->get($period);
            $label = $this->formatPeriodLabel($period, $groupFormat);
            return [
                'label'    => $label,
                'revenue'  => (float) ($rev?->revenue ?? 0),
                'profit'   => (float) ($rev?->profit ?? 0),
                'expenses' => (float) ($exp?->expenses ?? 0),
            ];
        })->values()->toArray();
    }

    private function formatPeriodLabel(string $period, string $format): string
    {
        try {
            if ($format === '%Y-%m-%d') {
                return Carbon::createFromFormat('Y-m-d', $period)->format('d/m');
            }
            if ($format === '%Y-%m') {
                return Carbon::createFromFormat('Y-m', $period)->translatedFormat('M');
            }
            // Week format: 2025-03 (week 3 of 2025)
            return 'أسبوع ' . (int) substr($period, -2);
        } catch (\Exception $e) {
            return $period;
        }
    }
}
