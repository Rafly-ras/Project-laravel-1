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
        $cashIn = Payment::whereBetween('paid_at', [$startDate, $endDate])->sum('amount');
        $cashOut = Expense::whereBetween('expense_date', [$startDate, $endDate])->sum('amount');

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

            $cashIn = Payment::whereBetween('paid_at', [$startOfMonth, $endOfMonth])->sum('amount');
            $cashOut = Expense::whereBetween('expense_date', [$startOfMonth, $endOfMonth])->sum('amount');

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
        return Invoice::where('status', '!=', 'paid')->get()->sum(function ($invoice) {
            return $invoice->remaining_balance;
        });
    }

    /**
     * Get Monthly Net Result for the current month.
     */
    public function getCurrentMonthStats()
    {
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        $cashIn = Payment::whereBetween('paid_at', [$start, $end])->sum('amount');
        $cashOut = Expense::whereBetween('expense_date', [$start, $end])->sum('amount');

        return [
            'revenue' => $cashIn,
            'expense' => $cashOut,
            'net' => $cashIn - $cashOut,
        ];
    }
}
