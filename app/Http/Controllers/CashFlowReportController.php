<?php

namespace App\Http\Controllers;

use App\Services\CashFlowService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class CashFlowReportController extends Controller
{
    protected $cashFlowService;

    public function __construct(CashFlowService $cashFlowService)
    {
        $this->cashFlowService = $cashFlowService;
    }

    public function index(Request $request)
    {
        $this->authorizeAccess();

        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date'))->startOfDay() 
            : now()->startOfMonth();
            
        $endDate = $request->input('end_date') 
            ? Carbon::parse($request->input('end_date'))->endOfDay() 
            : now()->endOfDay();

        // Handle quick month/year filter
        if ($request->input('month') && $request->input('year')) {
            $startDate = Carbon::createFromDate($request->input('year'), $request->input('month'), 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();
        }

        $summary = $this->cashFlowService->getSummary($startDate, $endDate);
        $cashIn = $this->cashFlowService->getCashInDetails($startDate, $endDate);
        $cashOut = $this->cashFlowService->getCashOutDetails($startDate, $endDate);
        $monthlyBreakdown = $this->cashFlowService->getMonthlyBreakdown(12);

        return view('reports.cashflow.index', compact(
            'summary', 'cashIn', 'cashOut', 'monthlyBreakdown', 'startDate', 'endDate'
        ));
    }

    public function exportCsv(Request $request)
    {
        $this->authorizeAccess();

        $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date'))->endOfDay();

        $cashIn = $this->cashFlowService->getCashInDetails($startDate, $endDate, 10000);
        $cashOut = $this->cashFlowService->getCashOutDetails($startDate, $endDate, 10000);

        $filename = "cashflow-report-{$startDate->format('Ymd')}-{$endDate->format('Ymd')}.csv";
        
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($cashIn, $cashOut) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['CASH FLOW REPORT']);
            fputcsv($file, []);

            fputcsv($file, ['--- CASH IN ---']);
            fputcsv($file, ['Date', 'Number', 'Customer', 'Method', 'Amount', 'Source']);
            foreach ($cashIn as $row) {
                fputcsv($file, [
                    $row->paid_at->format('Y-m-d'),
                    $row->invoice->invoice_number,
                    $row->invoice->salesOrder->customer_name,
                    $row->payment_method,
                    $row->amount,
                    'Payment'
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['--- CASH OUT ---']);
            fputcsv($file, ['Date', 'Category', 'Description', 'Amount']);
            foreach ($cashOut as $row) {
                fputcsv($file, [
                    $row->expense_date->format('Y-m-d'),
                    $row->category->name,
                    $row->description,
                    $row->amount
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $this->authorizeAccess();

        $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date'))->endOfDay();

        $summary = $this->cashFlowService->getSummary($startDate, $endDate);
        $cashIn = $this->cashFlowService->getCashInDetails($startDate, $endDate, 500);
        $cashOut = $this->cashFlowService->getCashOutDetails($startDate, $endDate, 500);

        $pdf = Pdf::loadView('reports.cashflow.pdf', compact(
            'summary', 'cashIn', 'cashOut', 'startDate', 'endDate'
        ))->setPaper('a4', 'landscape');

        return $pdf->download("cashflow-report-{$startDate->format('Ymd')}.pdf");
    }

    protected function authorizeAccess()
    {
        if (!auth()->user()->can('reports.cashflow')) {
            abort(403);
        }
    }
}
