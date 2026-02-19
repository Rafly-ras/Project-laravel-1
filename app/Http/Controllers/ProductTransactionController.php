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

    public function index()
    {
        $transactions = ProductTransaction::with('product')
            ->latest()
            ->paginate(15);
        
        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();
        return view('transactions.create', compact('products'));
    }

    public function store(ProductTransactionRequest $request)
    {
        $validated = $request->validated();
        $product = Product::findOrFail($validated['product_id']);

        // Check stock for 'Keluar' (Out) transactions
        if ($validated['type'] === 'Keluar' && $product->stock < $validated['quantity']) {
            return back()->withInput()->with('error', "Insufficient stock. Current stock is {$product->stock}.");
        }

        try {
            DB::transaction(function () use ($validated, $product) {
                // Create transaction
                ProductTransaction::create($validated);

                // Update product stock
                if ($validated['type'] === 'Masuk') {
                    $product->increment('stock', $validated['quantity']);
                } else {
                    $product->decrement('stock', $validated['quantity']);
                }
            });

            return redirect()->route('transactions.index')
                ->with('success', 'Transaction recorded successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to record transaction. Please try again.');
        }
    }
}
