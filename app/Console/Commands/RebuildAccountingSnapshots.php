<?php

namespace App\Console\Commands;

use App\Services\SnapshotService;
use Illuminate\Console\Command;

class RebuildAccountingSnapshots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounting:rebuild-snapshots';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rebuild all account snapshots from journal entries for all periods';

    /**
     * Execute the console command.
     */
    public function handle(SnapshotService $snapshotService)
    {
        $this->info('Starting snapshot rebuild process...');
        
        try {
            $snapshotService->rebuildAllSnapshots();
            $this->info('Successfully rebuilt all account snapshots.');
        } catch (\Exception $e) {
            $this->error('Failed to rebuild snapshots: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
