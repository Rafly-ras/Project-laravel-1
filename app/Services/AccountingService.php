<?php

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\AccountingPeriod;
use App\Jobs\GenerateAccountSnapshots;
use Illuminate\Support\Facades\DB;
use Exception;

class AccountingService
{
    protected $ledgerHasher;

    public function __construct(LedgerHasher $ledgerHasher)
    {
        $this->ledgerHasher = $ledgerHasher;
    }

    /**
     * Create a balanced Journal Entry.
     */
    public function createJournalEntry(array $data, array $lines)
    {
        return DB::transaction(function () use ($data, $lines) {
            $period = $this->getPeriodForDate($data['entry_date']);
            
            if ($period->status === 'Closed') {
                throw new Exception("Cannot post to a closed accounting period.");
            }

            $entry = JournalEntry::create(array_merge($data, [
                'accounting_period_id' => $period->id,
                'created_by' => auth()->id() ?? 1,
            ]));

            $totalDebit = 0;
            $totalCredit = 0;

            foreach ($lines as $line) {
                $lineModel = new JournalEntryLine($line);
                $entry->lines()->save($lineModel);
                
                $totalDebit += $line['debit'];
                $totalCredit += $line['credit'];
            }

            // Reload lines for hashing and isBalanced check
            $entry->load('lines');

            if (!$entry->isBalanced()) {
                throw new Exception("Journal entry is not balanced. Total Debit: $totalDebit, Total Credit: $totalCredit");
            }

            // Compute and store cryptographic hash
            $previousEntry = JournalEntry::where('id', '<', $entry->id)
                ->orderBy('id', 'desc')
                ->first();
            
            $entry->previous_hash = $previousEntry ? $previousEntry->hash : str_repeat('0', 64);
            $entry->hash = $this->ledgerHasher->computeHash($entry);
            
            // Bypass ImmutableLedger trait just for initial hash storage
            $entry->saveQuietly();

            return $entry;
        });
    }

    /**
     * Get the accounting period for a specific date.
     */
    public function getPeriodForDate($date)
    {
        $date = is_string($date) ? $date : $date->format('Y-m-d');
        
        $period = AccountingPeriod::where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->first();

        if (!$period) {
            throw new Exception("No accounting period found for the date: $date");
        }

        return $period;
    }

    /**
     * Update period status.
     */
    public function updatePeriodStatus($periodId, $status)
    {
        if (!in_array($status, ['Open', 'Soft-Closed', 'Closed'])) {
            throw new Exception("Invalid status provided.");
        }

        $period = AccountingPeriod::findOrFail($periodId);
        $period->update(['status' => $status]);

        if ($status === 'Closed') {
            GenerateAccountSnapshots::dispatch($period);
        }

        return $period;
    }
}
