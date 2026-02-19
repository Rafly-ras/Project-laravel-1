<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        
        // Total Stock Value ($) = sum(product.price * product.stock)
        $totalValue = Product::select(DB::raw('SUM(price * stock) as total'))->first()->total ?? 0;
        
        $lowStockCount = Product::where('stock', '<=', 5)->count();
        
        $lowStockProducts = Product::with('category')
            ->where('stock', '<=', 5)
            ->orderBy('stock', 'asc')
            ->limit(5)
            ->get();
            
        $recentTransactions = ProductTransaction::with('product')
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalProducts',
            'totalCategories',
            'totalValue',
            'lowStockCount',
            'lowStockProducts',
            'recentTransactions'
        ));
    }
}
