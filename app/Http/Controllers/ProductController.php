<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = Product::query()
            ->when($request->name, function ($query, $name) {
                return $query->where('name', 'like', '%' . $name . '%');
            })
            ->when($request->min_price, function ($query, $minPrice) {
                return $query->where('price', '>=', $minPrice);
            })
            ->when($request->max_price, function ($query, $maxPrice) {
                return $query->where('price', '<=', $maxPrice);
            })
            ->with('category')
            ->latest()
            ->paginate(10);

        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        try {
            Product::create($request->validated());
            return redirect()->route('products.index')
                ->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to create product. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load('category');
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
        try {
            $product->update($request->validated());
            return redirect()->route('products.index')
                ->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update product. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            $product->delete();
            return redirect()->route('products.index')
                ->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('products.index')
                ->with('error', 'Failed to delete product. It may be in use.');
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle); // Assuming: Name, Category, Price, Stock

        $imported = 0;
        DB::beginTransaction();
        try {
            $mainWarehouse = \App\Models\Warehouse::where('name', 'Main Warehouse')->first() 
                ?? \App\Models\Warehouse::first();

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 4) continue;

                $name = $row[0];
                $categoryName = $row[1];
                $price = (float) $row[2];
                $stock = (int) $row[3];

                $category = Category::firstOrCreate(['name' => $categoryName]);

                $product = Product::updateOrCreate(
                    ['name' => $name],
                    [
                        'category_id' => $category->id,
                        'price' => $price,
                        'stock' => $stock
                    ]
                );

                if ($mainWarehouse) {
                    $product->warehouses()->syncWithoutDetaching([
                        $mainWarehouse->id => ['stock' => $stock]
                    ]);
                }
                $imported++;
            }
            DB::commit();
            fclose($handle);
            return back()->with('success', "Successfully imported {$imported} products.");
        } catch (\Exception $e) {
            DB::rollBack();
            if ($handle) fclose($handle);
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function stockSummary()
    {
        $products = Product::with('category', 'warehouses')->paginate(20);
        return view('products.stock-summary', compact('products'));
    }

    public function exportStockCsv()
    {
        $products = Product::with('category')->get();
        $filename = "stock_summary_" . now()->format('YmdHis') . ".csv";
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        $callback = function() use($products) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Product', 'Category', 'Price', 'Total Stock']);
            foreach ($products as $product) {
                fputcsv($file, [$product->name, $product->category->name ?? 'Uncategorized', $product->price, $product->stock]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function exportStockPdf()
    {
        $products = Product::with('category')->get();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('products.pdf-summary', compact('products'));
        return $pdf->download('stock_summary_' . now()->format('YmdHis') . '.pdf');
    }
}
