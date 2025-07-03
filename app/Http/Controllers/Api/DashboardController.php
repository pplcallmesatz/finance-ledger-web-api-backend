<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\SalesLedger;
use App\Models\ExpenseLedger;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\ProductMaster;
use App\Models\CategoryMaster;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get dashboard overview statistics
     */
    public function overview(Request $request): JsonResponse
    {
        $this->authorize('view-any', SalesLedger::class);

        // Use provided dates or default to current year
        $startDate = $request->get('start_date', now()->startOfYear());
        $endDate = $request->get('end_date', now()->endOfYear());

        $totalExpenses = ExpenseLedger::whereBetween('purchase_date', [$startDate, $endDate])->sum('purchase_price');
        $totalSales = SalesLedger::whereBetween('sales_date', [$startDate, $endDate])->sum('total_customer_price');
        $totalPending = SalesLedger::whereBetween('sales_date', [$startDate, $endDate])
            ->where('payment_status', 'pending')->sum('total_customer_price');
        $totalProductPrice = SalesLedger::whereBetween('sales_date', [$startDate, $endDate])->sum('total_product_price');
        $totalProfit = $totalSales - $totalProductPrice;

        // Recent transactions (filtered by date)
        $recentTransactions = Transaction::with(['salesLedger', 'expenseLedger'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->take(5)
            ->get();

        // Payment status breakdown (filtered by date)
        $paymentStatusBreakdown = SalesLedger::whereBetween('sales_date', [$startDate, $endDate])
            ->select('payment_status', DB::raw('count(*) as count'), DB::raw('sum(total_customer_price) as total_amount'))
            ->groupBy('payment_status')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'statistics' => [
                    'total_expenses' => $totalExpenses,
                    'total_sales' => $totalSales,
                    'total_pending' => $totalPending,
                    'total_profit' => $totalProfit,
                    'profit_margin' => $totalSales > 0 ? round(($totalProfit / $totalSales) * 100, 2) : 0,
                ],
                'recent_transactions' => $recentTransactions,
                'payment_status_breakdown' => $paymentStatusBreakdown,
            ]
        ]);
    }

    /**
     * Get product profit/loss analysis
     */
    public function productAnalysis(Request $request): JsonResponse
    {
        $this->authorize('view-any', SalesLedger::class);

        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $productsSummary = DB::table('ledger_product')
            ->join('sales_ledgers', 'ledger_product.sales_ledger_id', '=', 'sales_ledgers.id')
            ->select(
                'product_id',
                DB::raw('SUM(product_price) as total_product_price'),
                DB::raw('SUM(customer_price) as total_customer_price'),
                DB::raw('SUM(quantity) as total_quantity')
            )
            ->whereBetween('sales_ledgers.sales_date', [$startDate, $endDate])
            ->groupBy('product_id')
            ->get();

        $productsSummary = $productsSummary->map(function ($productSummary) {
            $product = Product::with('categoryMaster')->find($productSummary->product_id);
            
            $productSummary->product_name = $product ? $product->name : 'Unknown Product';
            $productSummary->category_name = $product && $product->categoryMaster ? $product->categoryMaster->name : 'Unknown Category';
            $productSummary->category_id = $product && $product->categoryMaster ? $product->categoryMaster->id : null;
            $productSummary->profit = $productSummary->total_customer_price - $productSummary->total_product_price;
            $productSummary->profit_margin = $productSummary->total_customer_price > 0 
                ? round(($productSummary->profit / $productSummary->total_customer_price) * 100, 2) 
                : 0;
            
            return $productSummary;
        });

        $groupedByCategory = $productsSummary->groupBy('category_id');

        return response()->json([
            'success' => true,
            'data' => [
                'products_summary' => $productsSummary,
                'grouped_by_category' => $groupedByCategory,
                'overall_totals' => [
                    'total_product_price' => $productsSummary->sum('total_product_price'),
                    'total_customer_price' => $productsSummary->sum('total_customer_price'),
                    'total_profit' => $productsSummary->sum('profit'),
                ]
            ]
        ]);
    }

    /**
     * Get inventory status
     */
    public function inventoryStatus(): JsonResponse
    {
        $this->authorize('view-any', ProductMaster::class);

        $categoryInventory = DB::table('category_masters')
            ->leftJoin('product_masters', 'category_masters.id', '=', 'product_masters.category_id')
            ->select(
                'category_masters.id',
                'category_masters.name as category_name',
                'category_masters.symbol',
                DB::raw('SUM(product_masters.total_piece) as total_available_quantity'),
                DB::raw('COUNT(product_masters.id) as product_count')
            )
            ->groupBy('category_masters.id', 'category_masters.name', 'category_masters.symbol')
            ->orderBy('total_available_quantity', 'asc')
            ->get();

        // Low stock alerts (less than 10 pieces)
        $lowStockProducts = ProductMaster::with('categoryMaster')
            ->where('total_piece', '<', 10)
            ->get();

        // Expiring products (within 30 days)
        $expiringProducts = ProductMaster::with('categoryMaster')
            ->where('expire_date', '<=', now()->addDays(30))
            ->where('expire_date', '>=', now())
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'category_inventory' => $categoryInventory,
                'low_stock_alerts' => $lowStockProducts,
                'expiring_products' => $expiringProducts,
            ]
        ]);
    }

    /**
     * Get sales trends
     */
    public function salesTrends(Request $request): JsonResponse
    {
        $this->authorize('view-any', SalesLedger::class);

        $period = $request->get('period', 'month'); // month, week, year
        $limit = $request->get('limit', 12);

        $query = SalesLedger::select(
            DB::raw('DATE(sales_date) as date'),
            DB::raw('SUM(total_customer_price) as total_sales'),
            DB::raw('SUM(total_product_price) as total_cost'),
            DB::raw('COUNT(*) as order_count')
        );

        switch ($period) {
            case 'week':
                $query->whereBetween('sales_date', [now()->subWeeks($limit), now()]);
                break;
            case 'year':
                $query->whereBetween('sales_date', [now()->subYears($limit), now()]);
                break;
            default: // month
                $query->whereBetween('sales_date', [now()->subMonths($limit), now()]);
        }

        $trends = $query->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                $item->profit = $item->total_sales - $item->total_cost;
                $item->profit_margin = $item->total_sales > 0 
                    ? round(($item->profit / $item->total_sales) * 100, 2) 
                    : 0;
                return $item;
            });

        return response()->json([
            'success' => true,
            'data' => [
                'trends' => $trends,
                'period' => $period,
                'summary' => [
                    'total_sales' => $trends->sum('total_sales'),
                    'total_profit' => $trends->sum('profit'),
                    'average_order_value' => $trends->sum('order_count') > 0 
                        ? round($trends->sum('total_sales') / $trends->sum('order_count'), 2) 
                        : 0,
                ]
            ]
        ]);
    }

    /**
     * Get top performing products
     */
    public function topProducts(Request $request): JsonResponse
    {
        $this->authorize('view-any', SalesLedger::class);

        $limit = $request->get('limit', 10);
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $topProducts = DB::table('ledger_product')
            ->join('sales_ledgers', 'ledger_product.sales_ledger_id', '=', 'sales_ledgers.id')
            ->join('products', 'ledger_product.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.name',
                'products.barcode',
                DB::raw('SUM(ledger_product.quantity) as total_quantity'),
                DB::raw('SUM(ledger_product.customer_price) as total_revenue'),
                DB::raw('SUM(ledger_product.product_price) as total_cost'),
                DB::raw('COUNT(DISTINCT sales_ledgers.id) as order_count')
            )
            ->whereBetween('sales_ledgers.sales_date', [$startDate, $endDate])
            ->groupBy('products.id', 'products.name', 'products.barcode')
            ->orderBy('total_revenue', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($product) {
                $product->profit = $product->total_revenue - $product->total_cost;
                $product->profit_margin = $product->total_revenue > 0 
                    ? round(($product->profit / $product->total_revenue) * 100, 2) 
                    : 0;
                return $product;
            });

        return response()->json([
            'success' => true,
            'data' => $topProducts
        ]);
    }

    /**
     * Get customer analytics
     */
    public function customerAnalytics(Request $request): JsonResponse
    {
        $this->authorize('view-any', SalesLedger::class);

        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $customerAnalytics = DB::table('sales_ledgers')
            ->join('users', 'sales_ledgers.user_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.phone',
                DB::raw('COUNT(sales_ledgers.id) as total_orders'),
                DB::raw('SUM(sales_ledgers.total_customer_price) as total_spent'),
                DB::raw('AVG(sales_ledgers.total_customer_price) as average_order_value'),
                DB::raw('MAX(sales_ledgers.sales_date) as last_order_date')
            )
            ->whereBetween('sales_ledgers.sales_date', [$startDate, $endDate])
            ->groupBy('users.id', 'users.name', 'users.email', 'users.phone')
            ->orderBy('total_spent', 'desc')
            ->get();

        $topCustomers = $customerAnalytics->take(10);
        $newCustomers = User::whereBetween('created_at', [$startDate, $endDate])->count();

        return response()->json([
            'success' => true,
            'data' => [
                'customer_analytics' => $customerAnalytics,
                'top_customers' => $topCustomers,
                'new_customers' => $newCustomers,
                'summary' => [
                    'total_customers' => $customerAnalytics->count(),
                    'total_revenue' => $customerAnalytics->sum('total_spent'),
                    'average_order_value' => $customerAnalytics->avg('average_order_value'),
                ]
            ]
        ]);
    }
} 