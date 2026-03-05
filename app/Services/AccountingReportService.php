<?php

namespace App\Services;

use App\Models\Account;
use App\Models\JournalEntryLine;
use App\Models\AccountingPeriod;
use App\Models\AccountSnapshot;
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
            ->get()
            ->map(function ($account) use ($endDate) {
                
                // 1. Sum turnovers from all snapshots up to the end date
                $snapshotTotals = AccountSnapshot::where('account_id', $account->id)
                    ->whereHas('period', function($q) use ($endDate) {
                        $q->where('end_date', '<=', $endDate);
                    })
                    ->selectRaw('SUM(base_debit_turnover) as debit, SUM(base_credit_turnover) as credit')
                    ->first();

                // 2. Identify the last date covered by a snapshot
                $lastSnapshotDate = AccountSnapshot::where('account_id', $account->id)
                    ->join('accounting_periods', 'account_snapshots.accounting_period_id', '=', 'accounting_periods.id')
                    ->where('accounting_periods.end_date', '<=', $endDate)
                    ->max('accounting_periods.end_date') ?: '1900-01-01';

                // 3. Sum current journals not yet snapshotted up to the end date
                $currentJournals = JournalEntryLine::where('account_id', $account->id)
                    ->whereHas('entry', function ($q) use ($lastSnapshotDate, $endDate) {
                        $q->where('entry_date', '>', $lastSnapshotDate)
                          ->where('entry_date', '<=', $endDate);
                    })
                    ->selectRaw('SUM(base_debit) as debit, SUM(base_credit) as credit')
                    ->first();

                $finalDebit = ($snapshotTotals->debit ?? 0) + ($currentJournals->debit ?? 0);
                $finalCredit = ($snapshotTotals->credit ?? 0) + ($currentJournals->credit ?? 0);

                $balance = in_array($account->type, ['Asset', 'Expense']) 
                    ? $finalDebit - $finalCredit 
                    : $finalCredit - $finalDebit;

                return [
                    'code' => $account->code,
                    'name' => $account->name,
                    'type' => $account->type,
                    'debit' => $finalDebit,
                    'credit' => $finalCredit,
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
        $revenue = Account::where('type', 'Revenue')->get()->map(function ($account) use ($startDate, $endDate) {
            $amount = $this->getPeriodTurnover($account, $startDate, $endDate, 'base_credit');
            return (object) ['code' => $account->code, 'name' => $account->name, 'amount' => $amount];
        });

        $expenses = Account::where('type', 'Expense')->get()->map(function ($account) use ($startDate, $endDate) {
            $amount = $this->getPeriodTurnover($account, $startDate, $endDate, 'base_debit');
            return (object) ['code' => $account->code, 'name' => $account->name, 'amount' => $amount];
        });

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
     * Helper to get turnover using snapshots where possible.
     */
    protected function getPeriodTurnover($account, $startDate, $endDate, $column)
    {
        // 1. Get turnover from snapshots for fully contained closed periods
        $snapshotTurnover = AccountSnapshot::where('account_id', $account->id)
            ->whereHas('period', function($q) use ($startDate, $endDate) {
                $q->where('start_date', '>=', $startDate)
                  ->where('end_date', '<=', $endDate);
            })
            ->sum($column === 'base_credit' ? 'base_credit_turnover' : 'base_debit_turnover');

        // 2. Identify periods not covered by snapshots (partially contained or open)
        // Find intervals not covered by snapshots
        $snapshotExcludedMaxDate = AccountSnapshot::where('account_id', $account->id)
            ->join('accounting_periods', 'account_snapshots.accounting_period_id', '=', 'accounting_periods.id')
            ->where('accounting_periods.start_date', '>=', $startDate)
            ->where('accounting_periods.end_date', '<=', $endDate)
            ->max('accounting_periods.end_date') ?: $startDate;

        // If snapshot actually started AFTER startDate, we have a gap at the beginning
        $snapshotExcludedMinDate = AccountSnapshot::where('account_id', $account->id)
            ->join('accounting_periods', 'account_snapshots.accounting_period_id', '=', 'accounting_periods.id')
            ->where('accounting_periods.start_date', '>=', $startDate)
            ->where('accounting_periods.end_date', '<=', $endDate)
            ->min('accounting_periods.start_date') ?: $endDate;

        // Simplify for now: Just sum journals that are NOT in a snapshotted period within the range
        // A better way: journals where entry_date BETWEEN $startDate AND $endDate 
        // AND NOT EXISTS in a snapshotted period.
        
        $journalTurnover = JournalEntryLine::where('account_id', $account->id)
            ->whereHas('entry', function ($q) use ($startDate, $endDate) {
                $q->where('entry_date', '>=', $startDate)
                  ->where('entry_date', '<=', $endDate)
                  ->whereNotExists(function ($sub) {
                      $sub->select(DB::raw(1))
                          ->from('account_snapshots')
                          ->join('accounting_periods', 'account_snapshots.accounting_period_id', '=', 'accounting_periods.id')
                          ->whereColumn('account_snapshots.account_id', 'journal_entry_lines.account_id')
                          ->whereColumn('accounting_periods.id', 'journal_entries.accounting_period_id');
                  });
            })
            ->sum($column);

        return $snapshotTurnover + $journalTurnover;
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
