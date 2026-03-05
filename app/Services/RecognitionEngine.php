<?php

namespace App\Services;

use App\Models\RecognitionLine;
use App\Models\Account;
use Illuminate\Support\Facades\DB;
use Exception;

class RecognitionEngine
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    /**
     * Process all pending recognition lines up to a certain date.
     */
    public function processPendingLines($date = null)
    {
        $date = $date ?: now()->format('Y-m-d');

        $lines = RecognitionLine::where('status', 'Pending')
            ->whereDate('scheduled_date', '<=', $date)
            ->with('schedule')
            ->get();

        $processedCount = 0;

        foreach ($lines as $line) {
            DB::transaction(function () use ($line) {
                $this->postLine($line);
            });
            $processedCount++;
        }

        return $processedCount;
    }

    /**
     * Post a single recognition line to the ledger.
     */
    protected function postLine(RecognitionLine $line)
    {
        $schedule = $line->schedule;

        if ($schedule->type === 'Revenue') {
            // Deferred Revenue (Liability) -> Sales Revenue (Revenue)
            $deferredAccount = Account::where('code', '2200')->first();
            $targetAccount = Account::where('code', '4100')->first();
            
            $description = "Monthly Revenue Recognition for " . $schedule->source_type . " #" . $schedule->source_id;
            
            $journalLines = [
                [
                    'account_id' => $deferredAccount->id,
                    'description' => $description,
                    'debit' => $line->amount,
                    'credit' => 0,
                    'base_debit' => $line->base_amount,
                    'base_credit' => 0,
                ],
                [
                    'account_id' => $targetAccount->id,
                    'description' => $description,
                    'debit' => 0,
                    'credit' => $line->amount,
                    'base_debit' => 0,
                    'base_credit' => $line->base_amount,
                ],
            ];
        } else {
            // Prepaid Expenses (Asset) -> Operating Expenses (Expense)
            $prepaidAccount = Account::where('code', '1400')->first();
            $targetAccount = Account::where('code', '5200')->first();
            
            $description = "Monthly Expense Recognition for " . $schedule->source_type . " #" . $schedule->source_id;

            $journalLines = [
                [
                    'account_id' => $targetAccount->id,
                    'description' => $description,
                    'debit' => $line->amount,
                    'credit' => 0,
                    'base_debit' => $line->base_amount,
                    'base_credit' => 0,
                ],
                [
                    'account_id' => $prepaidAccount->id,
                    'description' => $description,
                    'debit' => 0,
                    'credit' => $line->amount,
                    'base_debit' => 0,
                    'base_credit' => $line->base_amount,
                ],
            ];
        }

        $entry = $this->accountingService->createJournalEntry([
            'entry_date' => $line->scheduled_date,
            'reference' => 'REC-' . $line->id,
            'description' => $description,
            'currency_id' => $schedule->currency_id,
            'exchange_rate' => $schedule->exchange_rate,
        ], $journalLines);

        $line->update([
            'journal_entry_id' => $entry->id,
            'status' => 'Posted',
        ]);
    }
}
