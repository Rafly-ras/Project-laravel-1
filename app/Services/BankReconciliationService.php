<?php

namespace App\Services;

use App\Models\Account;
use App\Models\BankStatement;
use App\Models\BankStatementLine;
use App\Models\BankReconciliation;
use App\Models\JournalEntry;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Exception;

class BankReconciliationService
{
    public function __construct(
        protected AccountingService $accountingService
    ) {}

    /**
     * Import CSV rows into bank_statement_lines for a given BankStatement.
     * Called by a background job after CSV parsing.
     */
    public function importLines(BankStatement $statement, array $rows): void
    {
        DB::transaction(function () use ($statement, $rows) {
            foreach ($rows as $row) {
                BankStatementLine::create([
                    'bank_statement_id' => $statement->id,
                    'transaction_date'  => $row['date'],
                    'description'       => $row['description'],
                    'reference'         => $row['reference'] ?? null,
                    'debit'             => $row['debit'] ?? 0,
                    'credit'            => $row['credit'] ?? 0,
                    'running_balance'   => $row['balance'] ?? 0,
                    'status'            => 'unmatched',
                ]);
            }
            $statement->update(['status' => 'completed']);
        });
    }

    /**
     * Auto-match statement lines against General Ledger journal entries.
     * Returns an array of suggestions keyed by statement_line_id.
     * Tier 1: Exact match (amount + date + reference).
     * Tier 2: Fuzzy match (amount + date within ±3 days).
     * Tier 3: Pattern match (description contains invoice number).
     */
    public function suggestMatches(BankStatement $statement): Collection
    {
        $suggestions = collect();
        $unmatchedLines = $statement->lines()->where('status', 'unmatched')->get();

        foreach ($unmatchedLines as $line) {
            $netAmount = abs($line->credit - $line->debit);

            // Tier 1 – Exact
            $candidate = $this->findExactMatch($line, $netAmount);
            if ($candidate) {
                $suggestions->put($line->id, ['line' => $line, 'journal' => $candidate, 'tier' => 'exact']);
                continue;
            }

            // Tier 2 – Fuzzy (±3 days)
            $candidate = $this->findFuzzyMatch($line, $netAmount);
            if ($candidate) {
                $suggestions->put($line->id, ['line' => $line, 'journal' => $candidate, 'tier' => 'fuzzy']);
                continue;
            }

            // Tier 3 – Pattern (invoice number in description)
            $candidate = $this->findPatternMatch($line);
            if ($candidate) {
                $suggestions->put($line->id, ['line' => $line, 'journal' => $candidate, 'tier' => 'pattern']);
            }
        }

        // Mark as "suggested" in DB
        $suggestions->each(function ($match) {
            $match['line']->update(['status' => 'suggested']);
        });

        return $suggestions;
    }

    /**
     * Confirm a manual or suggested match. Posts an adjustment journal if a
     * nominal difference exists (e.g., sender deducted bank fees).
     */
    public function confirmMatch(
        BankStatementLine $line,
        JournalEntry $journal,
        int $userId,
        float $difference = 0.0,
        ?string $notes = null
    ): BankReconciliation {
        return DB::transaction(function () use ($line, $journal, $userId, $difference, $notes) {
            $adjJournalId = null;

            // If nominal difference exists, post an adjustment (e.g., bank fee)
            if (abs($difference) > 0.001) {
                $adjJournalId = $this->postAdjustmentJournal($line, $difference)->id;
            }

            $reconciliation = BankReconciliation::create([
                'statement_line_id'   => $line->id,
                'journal_entry_id'    => $journal->id,
                'reconciled_by'       => $userId,
                'match_type'          => 'manual',
                'amount_matched'      => $journal->total_debit,
                'difference'          => $difference,
                'adjustment_journal_id' => $adjJournalId,
                'notes'               => $notes,
            ]);

            $line->update(['status' => 'reconciled']);

            return $reconciliation;
        });
    }

    // ─── Private helpers ────────────────────────────────────────────────

    private function findExactMatch(BankStatementLine $line, float $amount): ?JournalEntry
    {
        return JournalEntry::where('entry_date', $line->transaction_date)
            ->where('total_debit', $amount)
            ->whereDoesntHave('reconciliation')
            ->first();
    }

    private function findFuzzyMatch(BankStatementLine $line, float $amount): ?JournalEntry
    {
        return JournalEntry::whereBetween('entry_date', [
                $line->transaction_date->subDays(3),
                $line->transaction_date->addDays(3),
            ])
            ->where('total_debit', $amount)
            ->whereDoesntHave('reconciliation')
            ->first();
    }

    private function findPatternMatch(BankStatementLine $line): ?JournalEntry
    {
        // Extract anything that looks like an invoice reference  e.g.  INV/2024/001
        preg_match('/INV[\/\-]\d+[\/\-]?\d*/i', $line->description, $matches);
        if (empty($matches)) return null;

        return JournalEntry::where('reference', 'LIKE', '%' . $matches[0] . '%')
            ->whereDoesntHave('reconciliation')
            ->first();
    }

    private function postAdjustmentJournal(BankStatementLine $line, float $difference): JournalEntry
    {
        $bankFeeAccount = Account::where('code', '5300')->first(); // Bank Charges
        $bankAccount    = Account::where('code', '1100')->first(); // Cash & Bank

        if (!$bankFeeAccount || !$bankAccount) {
            throw new Exception('Bank fee account (5300) or cash account (1100) not found for adjustment.');
        }

        $lines = [
            ['account_id' => $bankFeeAccount->id, 'description' => 'Bank fee adjustment', 'debit' => $difference, 'credit' => 0, 'base_debit' => $difference, 'base_credit' => 0],
            ['account_id' => $bankAccount->id,    'description' => 'Bank fee offset',     'debit' => 0, 'credit' => $difference, 'base_debit' => 0, 'base_credit' => $difference],
        ];

        return $this->accountingService->createJournalEntry([
            'entry_date'  => $line->transaction_date->format('Y-m-d'),
            'reference'   => 'RECON-ADJ-' . $line->id,
            'description' => 'Reconciliation adjustment for statement line #' . $line->id,
            'currency_id' => 1,
            'exchange_rate' => 1,
        ], $lines);
    }
}
