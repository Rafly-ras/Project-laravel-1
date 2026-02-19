<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductTransactionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RoleController;

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

    Route::middleware('can:manage-employees')->group(function () {
        Route::resource('employees', EmployeeController::class);
        Route::resource('roles', RoleController::class)->only(['index', 'edit', 'update']);
    });
});

/*
|--------------------------------------------------------------------------
| Inventory & Transactions (Protected)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::middleware('can:manage-products')->group(function () {
        Route::get('products/stock-summary', [ProductController::class, 'stockSummary'])->name('products.stock-summary');
        Route::resource('products', ProductController::class);
    });

    Route::middleware('can:manage-transactions')->group(function () {
        Route::get('transactions/export/csv', [ProductTransactionController::class, 'exportCsv'])->name('transactions.export.csv');
        Route::get('transactions/export/pdf', [ProductTransactionController::class, 'exportPdf'])->name('transactions.export.pdf');
        Route::get('products/{product}/transactions/create', [ProductTransactionController::class, 'create'])->name('products.transactions.create');
        Route::resource('transactions', ProductTransactionController::class)->only(['index', 'create', 'store']);
    });

    Route::resource('posts', PostController::class)->except(['show']);
});


require __DIR__.'/auth.php';
