<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\SalesOrder;
use Exception;

class PostingEngine
{
    protected $accountingService;
    protected $recognitionService;

    public function __construct(AccountingService $accountingService, RecognitionService $recognitionService)
    {
        $this->accountingService = $accountingService;
        $this->recognitionService = $recognitionService;
    }

    /**
     * Post a Sales Order to Accounting (Revenue Recognition).
     */
    public function postSalesOrder(SalesOrder $salesOrder)
    {
        $arAccount = Account::where('code', '1200')->first(); // Accounts Receivable
        $salesAccount = Account::where('code', '4100')->first(); // Sales Revenue

        if (!$arAccount || !$salesAccount) {
            throw new Exception("Accounting accounts (1200 or 4100) not found.");
        }

        $lines = [
            [
                'account_id' => $arAccount->id,
                'description' => "Accounts Receivable recognition for SO " . $salesOrder->sales_number,
                'debit' => $salesOrder->total_amount,
                'credit' => 0,
                'base_debit' => $salesOrder->base_amount,
                'base_credit' => 0,
            ],
            [
                'account_id' => $salesAccount->id,
                'description' => "Revenue recognition for SO " . $salesOrder->sales_number,
                'debit' => 0,
                'credit' => $salesOrder->total_amount,
                'base_debit' => 0,
                'base_credit' => $salesOrder->base_amount,
            ],
        ];

        return $this->accountingService->createJournalEntry([
            'entry_date' => $salesOrder->confirmed_at ?? now(),
            'reference' => $salesOrder->sales_number,
            'description' => "Automated entry for Sales Order Confirmation " . $salesOrder->sales_number,
            'currency_id' => $salesOrder->currency_id,
            'exchange_rate' => $salesOrder->exchange_rate,
        ], $lines);
    }

    /**
     * Post an Invoice to Accounting.
     */
    public function postInvoice(Invoice $invoice)
    {
        $arAccount = Account::where('code', '1200')->first(); // Accounts Receivable
        
        // If deferred, hit Deferred Revenue (2200), otherwise hit Sales Revenue (4100)
        $revenueCode = $invoice->is_deferred ? '2200' : '4100';
        $revenueAccount = Account::where('code', $revenueCode)->first();

        if (!$arAccount || !$revenueAccount) {
            throw new Exception("Accounting accounts (1200 or {$revenueCode}) not found.");
        }

        $lines = [
            [
                'account_id' => $arAccount->id,
                'description' => "Accounts Receivable for Invoice " . $invoice->invoice_number,
                'debit' => $invoice->total_amount,
                'credit' => 0,
                'base_debit' => $invoice->base_amount,
                'base_credit' => 0,
            ],
            [
                'account_id' => $revenueAccount->id,
                'description' => ($invoice->is_deferred ? "Deferred Revenue" : "Sales Revenue") . " for Invoice " . $invoice->invoice_number,
                'debit' => 0,
                'credit' => $invoice->total_amount,
                'base_debit' => 0,
                'base_credit' => $invoice->base_amount,
            ],
        ];

        $entry = $this->accountingService->createJournalEntry([
            'entry_date' => $invoice->issued_at ?? now(),
            'reference' => $invoice->invoice_number,
            'description' => "Automated entry for Invoice " . $invoice->invoice_number,
            'currency_id' => $invoice->currency_id,
            'exchange_rate' => $invoice->exchange_rate,
        ], $lines);

        // If deferred, create the recognition schedule
        if ($invoice->is_deferred && $invoice->recognition_periods > 0) {
            $this->recognitionService->createScheduleFromInvoice($invoice, $invoice->recognition_periods);
        }

        return $entry;
    }

    /**
     * Post a Payment to Accounting.
     */
    public function postPayment(Payment $payment)
    {
        $cashAccount = Account::where('code', '1100')->first(); // Cash & Bank
        $arAccount = Account::where('code', '1200')->first(); // Accounts Receivable

        if (!$cashAccount || !$arAccount) {
            throw new Exception("Accounting accounts (1100 or 1200) not found.");
        }

        $lines = [
            [
                'account_id' => $cashAccount->id,
                'description' => "Payment received for " . ($payment->invoice->invoice_number ?? $payment->payment_number),
                'debit' => $payment->amount,
                'credit' => 0,
                'base_debit' => $payment->base_amount,
                'base_credit' => 0,
            ],
            [
                'account_id' => $arAccount->id,
                'description' => "AR clearing for " . ($payment->invoice->invoice_number ?? $payment->payment_number),
                'debit' => 0,
                'credit' => $payment->amount,
                'base_debit' => 0,
                'base_credit' => $payment->base_amount,
            ],
        ];

        return $this->accountingService->createJournalEntry([
            'entry_date' => $payment->paid_at,
            'reference' => $payment->payment_number,
            'description' => "Automated entry for Payment " . $payment->payment_number,
            'currency_id' => $payment->currency_id,
            'exchange_rate' => $payment->exchange_rate,
        ], $lines);
    }

    /**
     * Post an Expense to Accounting.
     */
    public function postExpense(Expense $expense)
    {
        $expenseAccount = Account::where('code', '5200')->first(); // Operating Expenses
        $cashAccount = Account::where('code', '1100')->first(); // Cash & Bank

        if (!$expenseAccount || !$cashAccount) {
            throw new Exception("Accounting accounts (5200 or 1100) not found.");
        }

        $lines = [
            [
                'account_id' => $expenseAccount->id,
                'description' => "Expense: " . $expense->description,
                'debit' => $expense->amount,
                'credit' => 0,
                'base_debit' => $expense->base_amount,
                'base_credit' => 0,
            ],
            [
                'account_id' => $cashAccount->id,
                'description' => "Payment for Expense: " . $expense->description,
                'debit' => 0,
                'credit' => $expense->amount,
                'base_debit' => 0,
                'base_credit' => $expense->base_amount,
            ],
        ];

        return $this->accountingService->createJournalEntry([
            'entry_date' => $expense->expense_date,
            'reference' => "EXP-" . $expense->id,
            'description' => "Automated entry for Expense: " . $expense->description,
            'currency_id' => $expense->currency_id,
            'exchange_rate' => $expense->exchange_rate,
        ], $lines);
    }
}
