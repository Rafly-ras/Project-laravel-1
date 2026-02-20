<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductTransactionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RequestOrderController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\CashFlowReportController;

Route::get('/', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('home');

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('can:employees.manage')->group(function () {
        Route::resource('employees', EmployeeController::class);
        Route::resource('roles', RoleController::class);
    });

    Route::middleware('can:products.view')->group(function () {
        Route::get('products/stock-summary', [ProductController::class, 'stockSummary'])->name('products.stock-summary')->middleware('can:products.stock-summary');
        Route::post('products/import', [ProductController::class, 'import'])->name('products.import')->middleware('can:products.create');
        Route::get('products/export/csv', [ProductController::class, 'exportStockCsv'])->name('products.export.csv')->middleware('can:products.stock-summary');
        Route::get('products/export/pdf', [ProductController::class, 'exportStockPdf'])->name('products.export.pdf')->middleware('can:products.stock-summary');
        Route::resource('products', ProductController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('warehouses', WarehouseController::class)->except(['show', 'destroy']);
    });

    Route::middleware('can:transactions.view')->group(function () {
        Route::get('transactions/export/csv', [ProductTransactionController::class, 'exportCsv'])->name('transactions.export.csv');
        Route::get('transactions/export/pdf', [ProductTransactionController::class, 'exportPdf'])->name('transactions.export.pdf');
        Route::get('products/{product}/transactions/create', [ProductTransactionController::class, 'create'])->name('products.transactions.create');
        Route::resource('transactions', ProductTransactionController::class)->only(['index', 'create', 'store']);
    });

    // Order-to-Cash (O2C) Module
    Route::middleware('can:ro.view')->group(function () {
        Route::post('request-orders/{request_order}/approve', [RequestOrderController::class, 'approve'])->name('request-orders.approve')->middleware('can:ro.approve');
        Route::post('request-orders/{request_order}/reject', [RequestOrderController::class, 'reject'])->name('request-orders.reject')->middleware('can:ro.approve');
        Route::post('request-orders/{request_order}/convert', [RequestOrderController::class, 'convertToSalesOrder'])->name('request-orders.convert')->middleware('can:ro.convert');
        Route::resource('request-orders', RequestOrderController::class);
    });

    Route::middleware('can:so.view')->group(function () {
        Route::post('sales-orders/{sales_order}/confirm', [SalesOrderController::class, 'confirm'])->name('sales-orders.confirm')->middleware('can:so.confirm');
        Route::post('sales-orders/{sales_order}/invoice', [SalesOrderController::class, 'generateInvoice'])->name('sales-orders.invoice')->middleware('can:invoices.create');
        Route::resource('sales-orders', SalesOrderController::class);
    });

    Route::middleware('can:invoices.view')->group(function () {
        Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'exportPdf'])->name('invoices.pdf')->middleware('can:invoices.export');
        Route::resource('invoices', InvoiceController::class)->only(['index', 'show']);
    });

    Route::middleware('can:payments.view')->group(function () {
        Route::resource('payments', PaymentController::class)->only(['index', 'store']);
    });

    // Finance & Expenses
    Route::middleware('auth')->prefix('finance')->group(function () {
        Route::resource('expenses', ExpenseController::class);
        Route::resource('expense-categories', ExpenseCategoryController::class);
    });

    // O2C Reports
    Route::middleware('auth')->prefix('reports')->group(function () {
        Route::get('cashflow', [CashFlowReportController::class, 'index'])->name('reports.cashflow')->middleware('can:reports.cashflow');
        Route::get('cashflow/csv', [CashFlowReportController::class, 'exportCsv'])->name('reports.cashflow.csv')->middleware('can:reports.cashflow');
        Route::get('cashflow/pdf', [CashFlowReportController::class, 'exportPdf'])->name('reports.cashflow.pdf')->middleware('can:reports.cashflow');
        
        Route::get('request-orders', [ReportController::class, 'exportRequestOrders'])->name('reports.request-orders')->middleware('can:ro.view');
        Route::get('sales-orders', [ReportController::class, 'exportSalesOrders'])->name('reports.sales-orders')->middleware('can:so.view');
        Route::get('invoices', [ReportController::class, 'exportInvoices'])->name('reports.invoices')->middleware('can:invoices.view');
        Route::get('payments', [ReportController::class, 'exportPayments'])->name('reports.payments')->middleware('can:payments.view');
        Route::get('master-o2c', [ReportController::class, 'exportMasterReport'])->name('reports.master-o2c')->middleware('can:dashboard.view');
    });

    Route::post('/notifications/{id}/mark-as-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-as-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::get('/api/chart-data', [DashboardController::class, 'getChartData'])->name('api.chart-data');

    Route::resource('posts', PostController::class)->except(['show']);
});


require __DIR__.'/auth.php';
