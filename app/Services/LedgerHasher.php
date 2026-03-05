<?php

namespace App\Services;

use App\Models\JournalEntry;
use Illuminate\Support\Facades\Log;

class LedgerHasher
{
    /**
     * Compute the hash for a Journal Entry based on its content and the previous entry's hash.
     */
    public function computeHash(JournalEntry $entry): string
    {
        $previousEntry = JournalEntry::where('id', '<', $entry->id)
            ->orderBy('id', 'desc')
            ->first();

        $previousHash = $previousEntry ? $previousEntry->hash : str_repeat('0', 64);

        $payload = [
            'previous_hash' => $previousHash,
            'entry_date' => $entry->entry_date->format('Y-m-d'),
            'reference' => $entry->reference,
            'description' => $entry->description,
            'currency_id' => $entry->currency_id,
            'exchange_rate' => (string) $entry->exchange_rate,
            'lines' => $entry->lines->map(fn($line) => [
                'account_id' => $line->account_id,
                'debit' => (string) $line->debit,
                'credit' => (string) $line->credit,
            ])->toArray(),
        ];

        return hash('sha256', json_encode($payload));
    }

    /**
     * Verify the entire ledger's integrity.
     */
    public function verifyChain(): array
    {
        $entries = JournalEntry::with('lines')->orderBy('id', 'asc')->get();
        $errors = [];
        $expectedPreviousHash = str_repeat('0', 64);

        foreach ($entries as $entry) {
            $actualHash = $this->calculateHashForVerification($entry, $expectedPreviousHash);

            if ($entry->hash !== $actualHash) {
                $errors[] = "Integrity breach at Journal Entry #{$entry->id} (Reference: {$entry->reference}). Hash mismatch.";
            }

            if ($entry->previous_hash !== $expectedPreviousHash) {
                $errors[] = "Chain break at Journal Entry #{$entry->id}. Previous hash mismatch.";
            }

            $expectedPreviousHash = $entry->hash;
        }

        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
            'count' => $entries->count()
        ];
    }

    /**
     * Internal helper to calculate hash for verification using a known previous hash.
     */
    protected function calculateHashForVerification(JournalEntry $entry, string $previousHash): string
    {
        $payload = [
            'previous_hash' => $previousHash,
            'entry_date' => $entry->entry_date->format('Y-m-d'),
            'reference' => $entry->reference,
            'description' => $entry->description,
            'currency_id' => $entry->currency_id,
            'exchange_rate' => (string) $entry->exchange_rate,
            'lines' => $entry->lines->sortBy('id')->map(fn($line) => [
                'account_id' => $line->account_id,
                'debit' => (string) $line->debit,
                'credit' => (string) $line->credit,
            ])->values()->toArray(),
        ];

        return hash('sha256', json_encode($payload));
    }
}
