<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductTransaction;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $warehouseId = $request->query('warehouse_id');
        
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        
        $queryValue = Product::query();
        $queryLowStock = Product::query();
        $queryRecentTrans = ProductTransaction::with('product');

        if ($warehouseId) {
            $queryValue->whereHas('warehouses', function($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            });
            $queryLowStock->whereHas('warehouses', function($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId)->where('stock', '<=', 5);
            });
            $queryRecentTrans->where('warehouse_id', $warehouseId);
        }

        // Total Stock Value ($) - this is tricky with pivot, let's just use products.stock for simplicity unless warehouse selected
        $totalValue = $queryValue->select(DB::raw('SUM(price * stock) as total'))->first()->total ?? 0;
        
        $lowStockCount = $warehouseId ? $queryLowStock->count() : Product::where('stock', '<=', 5)->count();
        
        $lowStockProducts = $queryLowStock->with('category')
            ->orderBy('stock', 'asc')
            ->limit(5)
            ->get();
            
        $recentTransactions = $queryRecentTrans->latest()
            ->limit(5)
            ->get();

        $recentActivities = \App\Models\ActivityLog::with('user')
            ->latest()
            ->limit(5)
            ->get();

        $warehouses = \App\Models\Warehouse::where('is_active', true)->get();

        // O2C Metrics
        $totalRevenue = \App\Models\SalesOrder::where('status', '!=', 'cancelled')->whereNotNull('confirmed_at')->sum('total_amount');
        $totalGrossProfit = \App\Models\SalesOrder::where('status', '!=', 'cancelled')->whereNotNull('confirmed_at')->sum('gross_profit');
        $totalExpenses = \App\Models\Expense::sum('amount');
        $netProfit = $totalGrossProfit - $totalExpenses;
        $marginPercentage = $totalRevenue > 0 ? ($totalGrossProfit / $totalRevenue) * 100 : 0;

        $pendingROs = \App\Models\RequestOrder::where('status', 'draft')->count();
        $unpaidInvoiceTotal = \App\Models\Invoice::whereIn('status', ['unpaid', 'partial'])->get()->sum('remaining_balance');

        return view('dashboard', compact(
            'totalProducts',
            'totalCategories',
            'totalValue',
            'lowStockCount',
            'lowStockProducts',
            'recentTransactions',
            'recentActivities',
            'warehouses',
            'warehouseId',
            'totalRevenue',
            'totalGrossProfit',
            'netProfit',
            'totalExpenses',
            'marginPercentage',
            'pendingROs',
            'unpaidInvoiceTotal'
        ));
    }

    public function getChartData()
    {
        // Stock per category
        $stockByCategory = Category::withSum('products', 'stock')
            ->get()
            ->map(fn($cat) => [
                'label' => $cat->name,
                'value' => $cat->products_sum_stock ?? 0
            ]);

        // Transactions per month (last 6 months)
        $monthlyTransactions = ProductTransaction::select(
                DB::raw('COUNT(*) as count'),
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month")
            )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get()
            ->reverse()
            ->values();

        // Profit & Expense trends (last 6 months)
        $profitTrend = \App\Models\SalesOrder::select(
                DB::raw('SUM(gross_profit) as total'),
                DB::raw("DATE_FORMAT(confirmed_at, '%Y-%m') as month")
            )
            ->whereNotNull('confirmed_at')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get()
            ->reverse()
            ->values();

        $expenseTrend = \App\Models\Expense::select(
                DB::raw('SUM(amount) as total'),
                DB::raw("DATE_FORMAT(expense_date, '%Y-%m') as month")
            )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get()
            ->reverse()
            ->values();

        return response()->json([
            'stockByCategory' => $stockByCategory,
            'monthlyTransactions' => $monthlyTransactions,
            'profitTrend' => $profitTrend,
            'expenseTrend' => $expenseTrend,
        ]);
    }
}
