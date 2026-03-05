<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AccountingPeriod;
use App\Models\AccountSnapshot;
use App\Models\JournalEntryLine;
use Illuminate\Support\Facades\DB;
use Exception;

class SnapshotService
{
    /**
     * Generate snapshots for all accounts for a specific period.
     */
    public function generateSnapshotsForPeriod(AccountingPeriod $period)
    {
        return DB::transaction(function () use ($period) {
            $accounts = Account::all();
            $previousPeriod = AccountingPeriod::where('end_date', '<', $period->start_date)
                ->orderBy('end_date', 'desc')
                ->first();

            foreach ($accounts as $account) {
                $this->createSnapshot($account, $period, $previousPeriod);
            }
        });
    }

    /**
     * Create or update a snapshot for a single account and period.
     */
    protected function createSnapshot(Account $account, AccountingPeriod $period, ?AccountingPeriod $previousPeriod)
    {
        // 1. Get starting balance from previous snapshot
        $openingBalance = 0;
        $baseOpeningBalance = 0;

        if ($previousPeriod) {
            $prevSnapshot = AccountSnapshot::where('account_id', $account->id)
                ->where('accounting_period_id', $previousPeriod->id)
                ->first();
            
            if ($prevSnapshot) {
                $openingBalance = $prevSnapshot->ending_balance;
                $baseOpeningBalance = $prevSnapshot->base_ending_balance;
            }
        }

        // 2. Calculate current period turnovers
        $turnovers = JournalEntryLine::where('account_id', $account->id)
            ->whereHas('entry', function ($query) use ($period) {
                $query->where('accounting_period_id', $period->id);
            })
            ->selectRaw('
                SUM(debit) as total_debit, 
                SUM(credit) as total_credit,
                SUM(base_debit) as base_total_debit,
                SUM(base_credit) as base_total_credit
            ')
            ->first();

        $debit = $turnovers->total_debit ?? 0;
        $credit = $turnovers->total_credit ?? 0;
        $baseDebit = $turnovers->base_total_debit ?? 0;
        $baseCredit = $turnovers->base_total_credit ?? 0;

        // 3. Calculate ending balance based on account type
        // Asset/Expense: Dr - Cr
        // Liability/Equity/Revenue: Cr - Dr
        $netChange = in_array($account->type, ['Asset', 'Expense']) ? ($debit - $credit) : ($credit - $debit);
        $baseNetChange = in_array($account->type, ['Asset', 'Expense']) ? ($baseDebit - $baseCredit) : ($baseCredit - $baseDebit);

        $endingBalance = $openingBalance + $netChange;
        $baseEndingBalance = $baseOpeningBalance + $baseNetChange;

        // 4. Persist snapshot
        return AccountSnapshot::updateOrCreate(
            ['account_id' => $account->id, 'accounting_period_id' => $period->id],
            [
                'ending_balance' => $endingBalance,
                'debit_turnover' => $debit,
                'credit_turnover' => $credit,
                'base_ending_balance' => $baseEndingBalance,
                'base_debit_turnover' => $baseDebit,
                'base_credit_turnover' => $baseCredit,
            ]
        );
    }

    /**
     * Rebuild all snapshots from the source of truth (Journal Entries).
     */
    public function rebuildAllSnapshots()
    {
        return DB::transaction(function () {
            AccountSnapshot::truncate();
            
            $periods = AccountingPeriod::orderBy('start_date', 'asc')->get();
            
            foreach ($periods as $period) {
                $this->generateSnapshotsForPeriod($period);
            }
        });
    }
}
