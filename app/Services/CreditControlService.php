<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Invoice;
use App\Exceptions\CreditLimitExceededException;
use App\Exceptions\ArAgingBlockException;
use Carbon\Carbon;

class CreditControlService
{
    /**
     * Verify that a customer is allowed to place a new order.
     * Throws an exception if blocked by credit limit or AR aging rules.
     */
    public function verifyCustomerCredit(int $customerId, float $newOrderAmount): void
    {
        $customer = Customer::findOrFail($customerId);

        if (!$customer->is_active) {
            throw new CreditLimitExceededException("Customer #{$customerId} is inactive.");
        }

        // Rule 1: Check AR Aging – block if any invoice > 60 days overdue
        $overdueInvoice = Invoice::whereHas('salesOrder', fn($q) => $q->where('customer_id', $customerId))
            ->where('status', '!=', 'paid')
            ->where('due_date', '<', Carbon::now()->subDays(60))
            ->first();

        if ($overdueInvoice) {
            throw new ArAgingBlockException(
                "Order blocked: Invoice #{$overdueInvoice->invoice_number} is more than 60 days overdue."
            );
        }

        // Rule 2: Check Credit Limit
        if ($customer->credit_limit > 0) {
            $outstandingBalance = Invoice::whereHas('salesOrder', fn($q) => $q->where('customer_id', $customerId))
                ->whereIn('status', ['unpaid', 'partial', 'overdue'])
                ->sum('total_amount');

            if ($outstandingBalance + $newOrderAmount > $customer->credit_limit) {
                throw new CreditLimitExceededException(
                    "Credit limit exceeded. Limit: {$customer->credit_limit}, " .
                    "Outstanding: {$outstandingBalance}, New Order: {$newOrderAmount}."
                );
            }
        }
    }

    /**
     * Return a summary of credit exposure for the dashboard.
     */
    public function getCustomerCreditSummary(int $customerId): array
    {
        $customer = Customer::findOrFail($customerId);

        $outstandingBalance = Invoice::whereHas('salesOrder', fn($q) => $q->where('customer_id', $customerId))
            ->whereIn('status', ['unpaid', 'partial', 'overdue'])
            ->sum('total_amount');

        $overdueCount = Invoice::whereHas('salesOrder', fn($q) => $q->where('customer_id', $customerId))
            ->where('status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->count();

        return [
            'credit_limit'       => $customer->credit_limit,
            'outstanding'        => $outstandingBalance,
            'available_credit'   => max(0, $customer->credit_limit - $outstandingBalance),
            'utilization_pct'    => $customer->credit_limit > 0
                ? round(($outstandingBalance / $customer->credit_limit) * 100, 1)
                : null,
            'overdue_invoices'   => $overdueCount,
        ];
    }
}
