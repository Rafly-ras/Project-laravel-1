<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\ProductTransaction;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SalesOrderController extends Controller
{
    public function index()
    {
        $salesOrders = SalesOrder::with('creator', 'requestOrder')
            ->latest()
            ->paginate(10);
        return view('sales_orders.index', compact('salesOrders'));
    }

    public function show(SalesOrder $salesOrder)
    {
        $salesOrder->load('items.product', 'items.warehouse', 'creator', 'requestOrder', 'invoice');
        return view('sales_orders.show', compact('salesOrder'));
    }

    public function confirm(SalesOrder $salesOrder)
    {
        if ($salesOrder->status !== 'draft') {
            return back()->with('error', 'Only draft orders can be confirmed.');
        }

        DB::beginTransaction();
        try {
            foreach ($salesOrder->items as $item) {
                $product = $item->product;
                $warehouse = $item->warehouse;

                // Check warehouse stock
                $pivot = $product->warehouses()->where('warehouse_id', $warehouse->id)->first();
                $currentStock = $pivot ? $pivot->pivot->stock : 0;

                if ($currentStock < $item->qty) {
                    throw new \Exception("Insufficient stock for product '{$product->name}' in warehouse '{$warehouse->name}'. (Available: {$currentStock})");
                }

                // Reduce stock in pivot
                $product->warehouses()->updateExistingPivot($warehouse->id, [
                    'stock' => $currentStock - $item->qty
                ]);

                // Reduce global stock
                $product->decrement('stock', $item->qty);

                // Record transaction
                ProductTransaction::create([
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouse->id,
                    'type' => 'out',
                    'quantity' => $item->qty,
                    'description' => "Sales Order #{$salesOrder->sales_number}",
                ]);
            }

            $salesOrder->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);

            DB::commit();
            return back()->with('success', 'Sales Order confirmed and stock updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function generateInvoice(SalesOrder $salesOrder)
    {
        if ($salesOrder->status !== 'confirmed') {
            return back()->with('error', 'Only confirmed orders can be invoiced.');
        }

        if ($salesOrder->invoice) {
            return back()->with('error', 'Invoice already exists.');
        }

        DB::beginTransaction();
        try {
            $invoice = Invoice::create([
                'sales_order_id' => $salesOrder->id,
                'due_date' => now()->addDays(14),
                'total_amount' => $salesOrder->total_amount,
                'status' => 'unpaid',
                'issued_at' => now(),
            ]);

            $salesOrder->update(['status' => 'invoiced']);

            DB::commit();
            return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice generated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
