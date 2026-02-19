<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    $lowStockProducts = \App\Models\Product::with('category')
        ->where('stock', '<=', 5)
        ->get();
    return view('dashboard', compact('lowStockProducts'));
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Posts (Protected)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::resource('posts', PostController::class)
        ->except(['show']);
    Route::get('products/stock-summary', [ProductController::class, 'stockSummary'])->name('products.stock-summary');
    Route::resource('products', ProductController::class);
    Route::get('transactions/export/csv', [ProductTransactionController::class, 'exportCsv'])->name('transactions.export.csv');
    Route::get('transactions/export/pdf', [ProductTransactionController::class, 'exportPdf'])->name('transactions.export.pdf');
    Route::resource('transactions', ProductTransactionController::class)->only(['index', 'create', 'store']);
});


require __DIR__.'/auth.php';
