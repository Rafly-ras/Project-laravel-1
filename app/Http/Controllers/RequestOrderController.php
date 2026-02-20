<?php

namespace App\Http\Controllers;

use App\Models\RequestOrder;
use App\Models\RequestOrderItem;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RequestOrderController extends Controller
{
    public function index()
    {
        $requestOrders = RequestOrder::with('creator', 'approver')
            ->latest()
            ->paginate(10);
        return view('request_orders.index', compact('requestOrders'));
    }

    public function create()
    {
        $products = Product::where('stock', '>', 0)->get();
        return view('request_orders.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $totalAmount = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $subtotal = $product->price * $item['qty'];
                $totalAmount += $subtotal;
                
                $itemsData[] = [
                    'product_id' => $product->id,
                    'qty' => $item['qty'],
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                ];
            }

            $requestOrder = RequestOrder::create([
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'total_amount' => $totalAmount,
                'created_by' => Auth::id(),
                'status' => 'draft',
            ]);

            foreach ($itemsData as $itemData) {
                $requestOrder->items()->create($itemData);
            }

            DB::commit();
            return redirect()->route('request-orders.index')->with('success', 'Request Order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function show(RequestOrder $requestOrder)
    {
        $requestOrder->load('items.product', 'creator', 'approver');
        return view('request_orders.show', compact('requestOrder'));
    }

    public function approve(RequestOrder $requestOrder)
    {
        if ($requestOrder->status !== 'draft') {
            return back()->with('error', 'Only draft orders can be approved.');
        }

        $requestOrder->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Request Order approved.');
    }

    public function reject(RequestOrder $requestOrder)
    {
        if ($requestOrder->status !== 'draft') {
            return back()->with('error', 'Only draft orders can be rejected.');
        }

        $requestOrder->update([
            'status' => 'rejected',
        ]);

        return back()->with('success', 'Request Order rejected.');
    }

    public function convertToSalesOrder(RequestOrder $requestOrder)
    {
        if ($requestOrder->status !== 'approved') {
            return back()->with('error', 'Only approved orders can be converted.');
        }

        DB::beginTransaction();
        try {
            $salesOrder = SalesOrder::create([
                'request_order_id' => $requestOrder->id,
                'customer_name' => $requestOrder->customer_name,
                'total_amount' => $requestOrder->total_amount,
                'created_by' => Auth::id(),
                'status' => 'draft',
            ]);

            foreach ($requestOrder->items as $item) {
                $salesOrder->items()->create([
                    'product_id' => $item->product_id,
                    'warehouse_id' => 1, // Default to main warehouse for now, can be changed in SO edit
                    'qty' => $item->qty,
                    'price' => $item->price,
                    'subtotal' => $item->subtotal,
                ]);
            }

            $requestOrder->update(['status' => 'converted']);

            DB::commit();
            return redirect()->route('sales-orders.show', $salesOrder)->with('success', 'Converted to Sales Order.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
