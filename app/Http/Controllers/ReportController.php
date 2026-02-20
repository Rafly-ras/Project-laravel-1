<?php

namespace App\Http\Controllers;

use App\Exports\RequestOrdersExport;
use App\Exports\SalesOrdersExport;
use App\Exports\InvoicesExport;
use App\Exports\PaymentsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\RequestOrder;
use App\Models\SalesOrder;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function exportRequestOrders(Request $request)
    {
        $format = $request->get('format', 'excel');
        
        if ($format === 'pdf') {
            $data = RequestOrder::with('creator', 'approver')->latest()->get();
            $pdf = Pdf::loadView('reports.request_orders_pdf', compact('data'));
            return $pdf->download('Request_Orders_Report.pdf');
        }

        return Excel::download(new RequestOrdersExport, 'Request_Orders_Report.xlsx');
    }

    public function exportSalesOrders(Request $request)
    {
        $format = $request->get('format', 'excel');
        
        if ($format === 'pdf') {
            $data = SalesOrder::with('creator')->latest()->get();
            $pdf = Pdf::loadView('reports.sales_orders_pdf', compact('data'));
            return $pdf->download('Sales_Orders_Report.pdf');
        }

        return Excel::download(new SalesOrdersExport, 'Sales_Orders_Report.xlsx');
    }

    public function exportInvoices(Request $request)
    {
        $format = $request->get('format', 'excel');
        
        if ($format === 'pdf') {
            $data = Invoice::with('salesOrder')->latest()->get();
            $pdf = Pdf::loadView('reports.invoices_pdf', compact('data'));
            return $pdf->download('Invoices_Report.pdf');
        }

        return Excel::download(new InvoicesExport, 'Invoices_Report.xlsx');
    }

    public function exportPayments(Request $request)
    {
        $format = $request->get('format', 'excel');
        
        if ($format === 'pdf') {
            $data = Payment::with('invoice', 'creator')->latest()->get();
            $pdf = Pdf::loadView('reports.payments_pdf', compact('data'));
            return $pdf->download('Payments_Report.pdf');
        }

        return Excel::download(new PaymentsExport, 'Payments_Report.xlsx');
    }

    public function exportMasterReport()
    {
        // For Master report, we only use Excel as requested
        return Excel::download(new \App\Exports\MasterO2CExport, 'Master_O2C_Report.xlsx');
    }
}
