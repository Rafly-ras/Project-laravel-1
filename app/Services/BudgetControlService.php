<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\AccountingPeriod;
use App\Exceptions\BudgetExceededException;
use Illuminate\Support\Facades\DB;

class BudgetControlService
{
    /**
     * Check if the given amount fits within the budget for a specific account and department.
     */
    public function checkBudget(float $baseAmount, int $accountId, int $departmentId, ?int $periodId = null)
    {
        if (!$periodId) {
            $period = AccountingPeriod::where('status', 'open')
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->first();
            
            if (!$period) return; // Or handle as error if periods are mandatory
            $periodId = $period->id;
        }

        $budget = Budget::where('department_id', $departmentId)
            ->where('account_id', $accountId)
            ->where('accounting_period_id', $periodId)
            ->first();

        if (!$budget) {
            return; // No budget defined, allow by default? Or block?
            // Enterprise standard: If no budget, it might mean unlimited OR blocked. 
            // We'll allow for now to avoid breaking existing flows.
        }

        if ($budget->spent_amount + $baseAmount > $budget->amount_limit) {
            throw new BudgetExceededException(
                "Budget exceeded for department {$budget->department->name}. " .
                "Remaining: " . number_format($budget->remaining, 2) . ", " .
                "Requested: " . number_format($baseAmount, 2)
            );
        }
    }

    /**
     * Increment the spent amount in the budget.
     */
    public function incrementSpent(float $baseAmount, int $accountId, int $departmentId, ?int $periodId = null)
    {
        if (!$periodId) {
            $period = AccountingPeriod::where('status', 'open')
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->first();
            if (!$period) return;
            $periodId = $period->id;
        }

        Budget::where('department_id', $departmentId)
            ->where('account_id', $accountId)
            ->where('accounting_period_id', $periodId)
            ->increment('spent_amount', $baseAmount);
    }
}
