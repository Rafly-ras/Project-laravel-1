<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\ProductTransaction;
use App\Http\Requests\ProductTransactionRequest;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductTransactionController extends Controller
{
    public function exportCsv()
    {
        $transactions = ProductTransaction::with(['product', 'product.category'])->latest()->get();
        $filename = "transactions_" . now()->format('YmdHis') . ".csv";
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Date', 'Product', 'Category', 'Type', 'Quantity', 'Description'];

        $callback = function() use($transactions, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->created_at->format('Y-m-d H:i'),
                    $transaction->product->name,
                    $transaction->product->category->name ?? 'Uncategorized',
                    $transaction->type,
                    $transaction->quantity,
                    $transaction->description
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf()
    {
        $transactions = ProductTransaction::with(['product', 'product.category'])->latest()->get();
        $pdf = Pdf::loadView('transactions.pdf', compact('transactions'));
        
        return $pdf->download('transactions_' . now()->format('YmdHis') . '.pdf');
    }

    public function index(Request $request)
    {
        $transactions = ProductTransaction::with('product')
            ->when($request->product_id, function ($query, $productId) {
                return $query->where('product_id', $productId);
            })
            ->when($request->type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->when($request->start_date, function ($query, $startDate) {
                return $query->whereDate('created_at', '>=', $startDate);
            })
            ->when($request->end_date, function ($query, $endDate) {
                return $query->whereDate('created_at', '<=', $endDate);
            })
            ->latest()
            ->paginate(10);
        
        $products = Product::orderBy('name')->get();
        return view('transactions.index', compact('transactions', 'products'));
    }

    public function create(Request $request, Product $product = null)
    {
        $selectedProductId = $product ? $product->id : $request->query('product_id');
        $products = Product::orderBy('name')->get();
        $warehouses = \App\Models\Warehouse::where('is_active', true)->get();
        return view('transactions.create', compact('products', 'selectedProductId', 'warehouses'));
    }

    public function store(ProductTransactionRequest $request)
    {
        $validated = $request->validated();
        $product = Product::findOrFail($validated['product_id']);
        $warehouse = \App\Models\Warehouse::findOrFail($validated['warehouse_id']);

        // Check stock in specific warehouse for 'Keluar' transactions
        $warehouseStock = $product->warehouses()->where('warehouse_id', $warehouse->id)->first()?->pivot->stock ?? 0;

        if ($validated['type'] === 'Keluar' && $warehouseStock < $validated['quantity']) {
            return back()->withInput()->with('error', "Insufficient stock in {$warehouse->name}. Current stock: {$warehouseStock}.");
        }

        try {
            DB::transaction(function () use ($validated, $product, $warehouse) {
                // Create transaction
                ProductTransaction::create($validated);

                // Update product-warehouse pivot stock
                $currentPivotStock = $product->warehouses()->where('warehouse_id', $warehouse->id)->first()?->pivot->stock ?? 0;
                $newPivotStock = ($validated['type'] === 'Masuk') 
                    ? $currentPivotStock + $validated['quantity'] 
                    : $currentPivotStock - $validated['quantity'];

                $product->warehouses()->syncWithoutDetaching([
                    $warehouse->id => ['stock' => $newPivotStock]
                ]);

                // Update product total stock cache
                if ($validated['type'] === 'Masuk') {
                    $product->increment('stock', $validated['quantity']);
                } else {
                    $product->decrement('stock', $validated['quantity']);
                }

                // Check for low stock and notify
                if ($product->fresh()->stock <= 5) {
                    $admins = \App\Models\User::whereHas('role', function($q) {
                        $q->where('name', 'Admin');
                    })->get();
                    
                    \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\LowStockNotification($product, $warehouse));
                }
            });

            return redirect()->route('transactions.index')
                ->with('success', 'Transaction recorded successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to record transaction: ' . $e->getMessage());
        }
    }
}
