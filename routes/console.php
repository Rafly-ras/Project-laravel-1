<?php

use Illuminate\Support\Facades\Schedule;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\User;
use App\Notifications\OverdueInvoiceNotification;
use App\Notifications\LowStockNotification;
use App\Notifications\FinancialSummaryNotification;
use Illuminate\Support\Facades\DB;

// Daily check for overdue invoices
Schedule::call(function () {
    $overdueInvoices = Invoice::where('status', 'unpaid')
        ->where('due_date', '<', now())
        ->whereDoesntHave('salesOrder', function($q) {
            $q->where('status', 'cancelled');
        })->get();

    foreach ($overdueInvoices as $invoice) {
        // Notify Creator or Admin
        $invoice->salesOrder->creator->notify(new OverdueInvoiceNotification($invoice));
    }
})->daily();

// Daily check for low stock
Schedule::call(function () {
    $lowStockProducts = Product::where('stock', '<=', 5)->get();
    
    foreach ($lowStockProducts as $product) {
        // Notify Admins
        $admins = User::all(); // Simple for now
        foreach ($admins as $admin) {
            $admin->notify(new LowStockNotification($product));
        }
    }
})->daily();

// Monthly financial summary
Schedule::call(function () {
    $lastMonth = now()->subMonth();
    
    $revenue = \App\Models\SalesOrder::whereNotNull('confirmed_at')
        ->whereMonth('confirmed_at', $lastMonth->month)
        ->whereYear('confirmed_at', $lastMonth->year)
        ->sum('total_amount');
        
    $grossProfit = \App\Models\SalesOrder::whereNotNull('confirmed_at')
        ->whereMonth('confirmed_at', $lastMonth->month)
        ->whereYear('confirmed_at', $lastMonth->year)
        ->sum('gross_profit');
        
    $expenses = \App\Models\Expense::whereMonth('expense_date', $lastMonth->month)
        ->whereYear('expense_date', $lastMonth->year)
        ->sum('amount');

    $summary = [
        'revenue' => $revenue,
        'gross_profit' => $grossProfit,
        'expenses' => $expenses,
        'net_profit' => $grossProfit - $expenses,
        'margin' => $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0,
        'month' => $lastMonth->format('F Y'),
    ];

    $admins = User::all();
    foreach ($admins as $admin) {
        $admin->notify(new FinancialSummaryNotification($summary));
    }
})->monthlyOn(1, '08:00');
