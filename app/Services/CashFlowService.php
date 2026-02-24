<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Expense;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CashFlowService
{
    /**
     * Get Cash Flow Summary for a given date range.
     */
    public function getSummary($startDate, $endDate)
    {
        $cashIn = Payment::whereBetween('paid_at', [$startDate, $endDate])->sum('base_amount');
        $cashOut = Expense::whereBetween('expense_date', [$startDate, $endDate])->sum('base_amount');

        return [
            'cash_in' => $cashIn,
            'cash_out' => $cashOut,
            'net_cashflow' => $cashIn - $cashOut,
        ];
    }

    /**
     * Get Cash In Details (Payments).
     */
    public function getCashInDetails($startDate, $endDate, $paginate = 10)
    {
        return Payment::with(['invoice.salesOrder', 'creator'])
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->orderBy('paid_at', 'desc')
            ->paginate($paginate);
    }

    /**
     * Get Cash Out Details (Expenses).
     */
    public function getCashOutDetails($startDate, $endDate, $paginate = 10)
    {
        return Expense::with(['category', 'creator'])
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->orderBy('expense_date', 'desc')
            ->paginate($paginate);
    }

    /**
     * Get Monthly Breakdown for charts.
     */
    public function getMonthlyBreakdown($months = 6)
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $cashIn = Payment::whereBetween('paid_at', [$startOfMonth, $endOfMonth])->sum('base_amount');
            $cashOut = Expense::whereBetween('expense_date', [$startOfMonth, $endOfMonth])->sum('base_amount');

            $data[] = [
                'month' => $date->format('M Y'),
                'cash_in' => (float)$cashIn,
                'cash_out' => (float)$cashOut,
                'net' => (float)($cashIn - $cashOut),
            ];
        }

        return $data;
    }

    /**
     * Get Outstanding Receivables (Unpaid Invoices).
     */
    public function getOutstandingReceivables()
    {
        return Invoice::where('status', '!=', 'paid')
            ->withSum('payments', 'base_amount')
            ->get()
            ->sum(function ($invoice) {
                return $invoice->base_amount - ($invoice->payments_sum_base_amount ?? 0);
            });
    }

    /**
     * Get Monthly Net Result for the current month.
     */
    public function getCurrentMonthStats()
    {
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        $cashIn = Payment::whereBetween('paid_at', [$start, $end])->sum('base_amount');
        $cashOut = Expense::whereBetween('expense_date', [$start, $end])->sum('base_amount');

        return [
            'revenue' => $cashIn,
            'expense' => $cashOut,
            'net' => $cashIn - $cashOut,
        ];
    }
}
