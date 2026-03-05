<?php

namespace App\Services;

use App\Models\RecognitionSchedule;
use App\Models\RecognitionLine;
use App\Models\Invoice;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RecognitionService
{
    /**
     * Create a Revenue Recognition Schedule from an Invoice.
     */
    public function createScheduleFromInvoice(Invoice $invoice, int $months)
    {
        return DB::transaction(function () use ($invoice, $months) {
            $schedule = RecognitionSchedule::create([
                'type' => 'Revenue',
                'source_type' => Invoice::class,
                'source_id' => $invoice->id,
                'total_amount' => $invoice->total_amount,
                'currency_id' => $invoice->currency_id,
                'exchange_rate' => $invoice->exchange_rate,
                'start_date' => $invoice->issued_at ?? now(),
                'end_date' => Carbon::parse($invoice->issued_at ?? now())->addMonths($months - 1)->endOfMonth(),
                'periods' => $months,
                'status' => 'Active',
            ]);

            $this->generateLines($schedule);

            return $schedule;
        });
    }

    /**
     * Create an Expense Recognition Schedule (Prepaid) from an Expense.
     */
    public function createScheduleFromExpense(Expense $expense, int $months)
    {
        return DB::transaction(function () use ($expense, $months) {
            $schedule = RecognitionSchedule::create([
                'type' => 'Expense',
                'source_type' => Expense::class,
                'source_id' => $expense->id,
                'total_amount' => $expense->amount,
                'currency_id' => $expense->currency_id,
                'exchange_rate' => $expense->exchange_rate,
                'start_date' => $expense->expense_date,
                'end_date' => Carbon::parse($expense->expense_date)->addMonths($months - 1)->endOfMonth(),
                'periods' => $months,
                'status' => 'Active',
            ]);

            $this->generateLines($schedule);

            return $schedule;
        });
    }

    /**
     * Internal helper to generate monthly recognition lines.
     */
    protected function generateLines(RecognitionSchedule $schedule)
    {
        $monthlyAmount = round($schedule->total_amount / $schedule->periods, 2);
        $monthlyBaseAmount = round(($schedule->total_amount * $schedule->exchange_rate) / $schedule->periods, 2);
        
        $totalAllocated = 0;
        $totalBaseAllocated = 0;

        for ($i = 0; $i < $schedule->periods; $i++) {
            $isLast = ($i === $schedule->periods - 1);
            
            $amount = $isLast ? ($schedule->total_amount - $totalAllocated) : $monthlyAmount;
            $baseAmount = $isLast ? (($schedule->total_amount * $schedule->exchange_rate) - $totalBaseAllocated) : $monthlyBaseAmount;

            RecognitionLine::create([
                'recognition_schedule_id' => $schedule->id,
                'scheduled_date' => Carbon::parse($schedule->start_date)->addMonths($i)->endOfMonth(),
                'amount' => $amount,
                'base_amount' => $baseAmount,
                'status' => 'Pending',
            ]);

            $totalAllocated += $amount;
            $totalBaseAllocated += $baseAmount;
        }
    }
}
