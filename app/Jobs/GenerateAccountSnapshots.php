<?php

namespace App\Jobs;

use App\Models\AccountingPeriod;
use App\Services\SnapshotService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateAccountSnapshots implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $period;

    /**
     * Create a new job instance.
     */
    public function __construct(AccountingPeriod $period)
    {
        $this->period = $period;
    }

    /**
     * Execute the job.
     */
    public function handle(SnapshotService $snapshotService): void
    {
        $snapshotService->generateSnapshotsForPeriod($this->period);
    }
}
