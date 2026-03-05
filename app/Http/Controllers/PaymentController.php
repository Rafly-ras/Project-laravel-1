<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected $postingEngine;

    public function __construct(\App\Services\PostingEngine $postingEngine)
    {
        $this->postingEngine = $postingEngine;
    }

    public function index()
    {
        $payments = Payment::with('invoice', 'creator', 'currency')->latest()->paginate(10);
        $currencies = Currency::where('is_active', true)->get();
        return view('payments.index', compact('payments', 'currencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'currency_id' => 'required|exists:currencies,id',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string',
            'paid_at' => 'required|date',
        ]);

        $invoice = Invoice::findOrFail($validated['invoice_id']);

        // Check if payment currency matches invoice currency or handle conversion if needed
        // For now, we assume simple match or validation
        
        DB::beginTransaction();
        try {
            // Pin exchange rate to invoice to ensure base_amount parity for status checks
            $exchangeRate = $invoice->exchange_rate;
            $baseAmount = $validated['amount'] * $exchangeRate;

            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $validated['amount'],
                'currency_id' => $validated['currency_id'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'],
                'paid_at' => $validated['paid_at'],
                'created_by' => Auth::id(),
                'exchange_rate' => $exchangeRate,
                'base_amount' => $baseAmount,
            ]);

            // Post to Accounting
            $this->postingEngine->postPayment($payment);

            // Update Invoice Status based on base_amount
            $invoice->refresh(); 
            $paidInBase = $invoice->payments()->sum('base_amount');
            
            if ($paidInBase >= ($invoice->base_amount - 0.01)) { // Allow for tiny rounding diffs
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
