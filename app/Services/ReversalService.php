<?php

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Support\Facades\DB;
use Exception;

class ReversalService
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    /**
     * Reverse a Journal Entry by creating a new offsetting entry.
     */
    public function reverse(JournalEntry $entry, string $reason = "Correction of error")
    {
        if ($entry->reversed_at) {
            throw new Exception("Journal entry #{$entry->id} has already been reversed.");
        }

        if ($entry->is_reversal) {
            throw new Exception("Cannot reverse a reversal entry.");
        }

        return DB::transaction(function () use ($entry, $reason) {
            $entryData = [
                'entry_date' => now(),
                'reference' => "REV-{$entry->reference}",
                'description' => "Reversal of #{$entry->id}: {$reason}",
                'currency_id' => $entry->currency_id,
                'exchange_rate' => $entry->exchange_rate,
                'is_reversal' => true,
            ];

            $lines = $entry->lines->map(function ($line) {
                return [
                    'account_id' => $line->account_id,
                    'description' => "Reversing: " . $line->description,
                    'debit' => $line->credit, // Flip Dr/Cr
                    'credit' => $line->debit,
                    'base_debit' => $line->base_credit,
                    'base_credit' => $line->base_debit,
                ];
            })->toArray();

            $reversalEntry = $this->accountingService->createJournalEntry($entryData, $lines);

            // Link them (Bypass immutability trait for this specific relationship update)
            $entry->reversed_at = now();
            $entry->reversing_entry_id = $reversalEntry->id;
            $entry->saveQuietly();

            return $reversalEntry;
        });
    }
}
