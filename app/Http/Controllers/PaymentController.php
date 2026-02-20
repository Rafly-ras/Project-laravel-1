<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('invoice', 'creator')->latest()->paginate(10);
        return view('payments.index', compact('payments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string',
            'paid_at' => 'required|date',
        ]);

        $invoice = Invoice::findOrFail($validated['invoice_id']);

        if (round($validated['amount'], 2) != round($invoice->remaining_balance, 2)) {
            return back()->with('error', "Payment amount must match the remaining balance exactly (\$ " . number_format($invoice->remaining_balance, 2) . ")");
        }

        DB::beginTransaction();
        try {
            Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'],
                'paid_at' => $validated['paid_at'],
                'created_by' => Auth::id(),
            ]);

            // Update Invoice Status
            $newPaid = $invoice->paid_amount + $validated['amount'];
            if ($newPaid >= $invoice->total_amount) {
                $invoice->update(['status' => 'paid']);
            } else {
                $invoice->update(['status' => 'partial']);
            }

            DB::commit();
            return back()->with('success', 'Payment recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
