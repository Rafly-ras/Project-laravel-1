<?php

namespace App\Jobs;

use App\Models\BankStatement;
use App\Services\BankReconciliationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProcessBankStatementJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300; // 5 minutes for large files
    public int $tries = 3;

    public function __construct(public BankStatement $statement) {}

    public function handle(BankReconciliationService $service): void
    {
        $this->statement->update(['status' => 'processing']);

        try {
            $path = Storage::path('bank-statements/' . $this->statement->filename);
            $rows = $this->parseCsv($path);
            $service->importLines($this->statement, $rows);
        } catch (\Exception $e) {
            $this->statement->update(['status' => 'failed']);
            throw $e;
        }
    }

    private function parseCsv(string $path): array
    {
        $rows = [];
        if (($handle = fopen($path, 'r')) !== false) {
            $headers = fgetcsv($handle); // Skip header row
            while (($data = fgetcsv($handle)) !== false) {
                if (count($headers) !== count($data)) continue;
                $row = array_combine($headers, $data);
                $rows[] = [
                    'date'        => $row['date'] ?? $row['Date'] ?? now()->toDateString(),
                    'description' => $row['description'] ?? $row['Description'] ?? '',
                    'reference'   => $row['reference'] ?? $row['Reference'] ?? null,
                    'debit'       => (float)($row['debit']  ?? $row['Debit']  ?? 0),
                    'credit'      => (float)($row['credit'] ?? $row['Credit'] ?? 0),
                    'balance'     => (float)($row['balance'] ?? $row['Balance'] ?? 0),
                ];
            }
            fclose($handle);
        }
        return $rows;
    }
}
