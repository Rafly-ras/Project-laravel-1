<?php

namespace App\Services;

use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Expense;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProfitService
{
    /**
     * Get Profit Summary for a given date range.
     */
    public function getSummary($startDate, $endDate)
    {
        $confirmedOrders = SalesOrder::whereBetween('confirmed_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled');

        $revenue = $confirmedOrders->sum('total_amount');
        $grossProfit = $confirmedOrders->sum('gross_profit');
        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])->sum('amount');
        
        $cogs = $revenue - $grossProfit;
        $netProfit = $grossProfit - $expenses;
        $margin = $revenue > 0 ? ($netProfit / $revenue) * 100 : 0;

        return [
            'revenue' => (float)$revenue,
            'cogs' => (float)$cogs,
            'gross_profit' => (float)$grossProfit,
            'expenses' => (float)$expenses,
            'net_profit' => (float)$netProfit,
            'margin_percentage' => (float)$margin,
        ];
    }

    /**
     * Get Revenue/Profit Breakdown by Sales Order.
     */
    public function getRevenueBreakdown($startDate, $endDate, $paginate = 10)
    {
        return SalesOrder::with(['creator', 'requestOrder'])
            ->whereBetween('confirmed_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->orderBy('confirmed_at', 'desc')
            ->paginate($paginate);
    }

    /**
     * Get Monthly Profit Breakdown for charts.
     */
    public function getMonthlyBreakdown($months = 6)
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $monthDate = now()->subMonths($i);
            $start = $monthDate->copy()->startOfMonth();
            $end = $monthDate->copy()->endOfMonth();

            $confirmedOrders = SalesOrder::whereBetween('confirmed_at', [$start, $end])
                ->where('status', '!=', 'cancelled');

            $revenue = $confirmedOrders->sum('total_amount');
            $grossProfit = $confirmedOrders->sum('gross_profit');
            $expenses = Expense::whereBetween('expense_date', [$start, $end])->sum('amount');

            $data[] = [
                'month' => $monthDate->format('M Y'),
                'revenue' => (float)$revenue,
                'expense' => (float)$expenses,
                'net_profit' => (float)($grossProfit - $expenses),
            ];
        }

        return $data;
    }

    /**
     * Get Top Profitable Products.
     */
    public function getTopProducts($limit = 5)
    {
        return SalesOrderItem::select('product_id', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM((price - (SELECT cost_price FROM products WHERE id = sales_order_items.product_id)) * qty) as total_profit'))
            ->whereHas('salesOrder', function($q) {
                $q->where('status', '!=', 'cancelled')->whereNotNull('confirmed_at');
            })
            ->groupBy('product_id')
            ->orderBy('total_profit', 'desc')
            ->limit($limit)
            ->with('product')
            ->get();
    }

    /**
     * Get Most Profitable Month in the last year.
     */
    public function getMostProfitableMonth()
    {
        $startOfYear = now()->startOfYear();
        $endOfYear = now()->endOfYear();

        $isSqlite = DB::getDriverName() === 'sqlite';
        $dateFormat = $isSqlite ? "strftime('%Y-%m', confirmed_at)" : "DATE_FORMAT(confirmed_at, '%Y-%m')";

        $monthlyData = SalesOrder::select(
                DB::raw("$dateFormat as month"),
                DB::raw('SUM(gross_profit) as profit')
            )
            ->whereNotNull('confirmed_at')
            ->whereBetween('confirmed_at', [$startOfYear, $endOfYear])
            ->groupBy('month')
            ->get();

        $dateFormatExp = $isSqlite ? "strftime('%Y-%m', expense_date)" : "DATE_FORMAT(expense_date, '%Y-%m')";

        $expenses = Expense::select(
                DB::raw("$dateFormatExp as month"),
                DB::raw('SUM(amount) as cost')
            )
            ->whereBetween('expense_date', [$startOfYear, $endOfYear])
            ->groupBy('month')
            ->get()
            ->pluck('cost', 'month');

        $bestMonth = null;
        $maxProfit = -INF;

        foreach ($monthlyData as $data) {
            $net = $data->profit - ($expenses[$data->month] ?? 0);
            if ($net > $maxProfit) {
                $maxProfit = $net;
                $bestMonth = Carbon::createFromFormat('Y-m', $data->month)->format('F Y');
            }
        }

        return [
            'month' => $bestMonth ?? 'N/A',
            'profit' => $maxProfit == -INF ? 0 : $maxProfit
        ];
    }
}
