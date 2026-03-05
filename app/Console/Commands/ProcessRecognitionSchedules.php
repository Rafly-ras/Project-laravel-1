<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RecognitionEngine;

class ProcessRecognitionSchedules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounting:process-recognition {--date= : The date to process up to (Y-m-d)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process and post all pending revenue and expense recognition lines';

    /**
     * Execute the console command.
     */
    public function handle(RecognitionEngine $engine)
    {
        $date = $this->option('date') ?: now()->format('Y-m-d');
        
        $this->info("Processing recognition schedules up to {$date}...");
        
        $count = $engine->processPendingLines($date);
        
        $this->success("Successfully processed and posted {$count} recognition lines.");
    }
}
