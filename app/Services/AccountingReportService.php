<?php

namespace App\Services;

use App\Models\Account;
use App\Models\JournalEntryLine;
use Illuminate\Support\Facades\DB;

class AccountingReportService
{
    /**
     * Generate Trial Balance.
     */
    public function getTrialBalance($endDate = null)
    {
        $endDate = $endDate ?: now()->format('Y-m-d');

        return Account::select('accounts.id', 'accounts.code', 'accounts.name', 'accounts.type')
            ->withSum(['journalEntryLines as total_debit' => function ($query) use ($endDate) {
                $query->whereHas('entry', function ($q) use ($endDate) {
                    $q->where('entry_date', '<=', $endDate);
                });
            }], 'base_debit')
            ->withSum(['journalEntryLines as total_credit' => function ($query) use ($endDate) {
                $query->whereHas('entry', function ($q) use ($endDate) {
                    $q->where('entry_date', '<=', $endDate);
                });
            }], 'base_credit')
            ->orderBy('code')
            ->get()
            ->map(function ($account) {
                $debit = $account->total_debit ?? 0;
                $credit = $account->total_credit ?? 0;
                
                // Normal Balance Logic
                $balance = in_array($account->type, ['Asset', 'Expense']) 
                    ? $debit - $credit 
                    : $credit - $debit;

                return [
                    'code' => $account->code,
                    'name' => $account->name,
                    'type' => $account->type,
                    'debit' => $debit,
                    'credit' => $credit,
                    'balance' => $balance
                ];
            });
    }

    /**
     * Generate Balance Sheet.
     */
    public function getBalanceSheet($endDate = null)
    {
        $endDate = $endDate ?: now()->format('Y-m-d');
        $trialBalance = $this->getTrialBalance($endDate);

        $assets = $trialBalance->where('type', 'Asset');
        $liabilities = $trialBalance->where('type', 'Liability');
        $equity = $trialBalance->where('type', 'Equity');

        // Net Profit from P&L is needed for Retained Earnings (Equity)
        $netProfit = $this->getNetProfit($endDate);

        return [
            'assets' => [
                'items' => $assets,
                'total' => $assets->sum('balance'),
            ],
            'liabilities' => [
                'items' => $liabilities,
                'total' => $liabilities->sum('balance'),
            ],
            'equity' => [
                'items' => $equity,
                'net_profit' => $netProfit,
                'total' => $equity->sum('balance') + $netProfit,
            ],
        ];
    }

    /**
     * Generate Profit & Loss.
     */
    public function getProfitLoss($startDate, $endDate)
    {
        $revenue = Account::where('type', 'Revenue')
            ->withSum(['journalEntryLines as amount' => function ($query) use ($startDate, $endDate) {
                $query->whereHas('entry', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('entry_date', [$startDate, $endDate]);
                });
            }], 'base_credit') // Revenue normal balance is Credit
            ->get();

        $expenses = Account::where('type', 'Expense')
            ->withSum(['journalEntryLines as amount' => function ($query) use ($startDate, $endDate) {
                $query->whereHas('entry', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('entry_date', [$startDate, $endDate]);
                });
            }], 'base_debit') // Expense normal balance is Debit
            ->get();

        $totalRevenue = $revenue->sum('amount');
        $totalExpenses = $expenses->sum('amount');

        return [
            'revenue' => [
                'items' => $revenue,
                'total' => $totalRevenue,
            ],
            'expenses' => [
                'items' => $expenses,
                'total' => $totalExpenses,
            ],
            'net_profit' => $totalRevenue - $totalExpenses,
        ];
    }

    /**
     * Helper to get Net Profit for a period/date.
     */
    protected function getNetProfit($endDate)
    {
        $pl = $this->getProfitLoss('1900-01-01', $endDate);
        return $pl['net_profit'];
    }
}
