<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('salesOrder')->latest()->paginate(10);
        return view('invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('salesOrder.items.product', 'payments.creator');
        return view('invoices.show', compact('invoice'));
    }

    public function exportPdf(Invoice $invoice)
    {
        $invoice->load('salesOrder.items.product');
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        return $pdf->download("Invoice-{$invoice->invoice_number}.pdf");
    }
}
