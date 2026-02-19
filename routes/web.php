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

    Route::post('/notifications/{id}/mark-as-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-as-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::get('/api/chart-data', [DashboardController::class, 'getChartData'])->name('api.chart-data');

    Route::resource('posts', PostController::class)->except(['show']);
});


require __DIR__.'/auth.php';
