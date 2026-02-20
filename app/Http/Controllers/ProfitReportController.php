<?php

namespace App\Http\Controllers;

use App\Services\ProfitService;
use App\Services\CashFlowService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ProfitReportController extends Controller
{
    protected $profitService;
    protected $cashFlowService;

    public function __construct(ProfitService $profitService, CashFlowService $cashFlowService)
    {
        $this->profitService = $profitService;
        $this->cashFlowService = $cashFlowService; // Still need cashflow details for expenses breakdown
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

        if ($request->input('month') && $request->input('year')) {
            $startDate = Carbon::createFromDate($request->input('year'), $request->input('month'), 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();
        }

        $summary = $this->profitService->getSummary($startDate, $endDate);
        $revenueDetails = $this->profitService->getRevenueBreakdown($startDate, $endDate);
        $expenseDetails = $this->cashFlowService->getCashOutDetails($startDate, $endDate);
        $monthlyBreakdown = $this->profitService->getMonthlyBreakdown(12);

        return view('reports.profit.index', compact(
            'summary', 'revenueDetails', 'expenseDetails', 'monthlyBreakdown', 'startDate', 'endDate'
        ));
    }

    public function exportCsv(Request $request)
    {
        $this->authorizeAccess();

        $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date'))->endOfDay();

        $revenue = $this->profitService->getRevenueBreakdown($startDate, $endDate, 10000);
        $expenses = $this->cashFlowService->getCashOutDetails($startDate, $endDate, 10000);

        $filename = "profit-report-{$startDate->format('Ymd')}.csv";
        
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($revenue, $expenses) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['PROFIT & LOSS REPORT (ACCRUAL BASIS)']);
            fputcsv($file, []);

            fputcsv($file, ['--- REVENUE BREAKDOWN ---']);
            fputcsv($file, ['Sales #', 'Date', 'Customer', 'Revenue', 'COGS', 'Gross Profit']);
            foreach ($revenue as $row) {
                fputcsv($file, [
                    $row->sales_number,
                    $row->confirmed_at->format('Y-m-d'),
                    $row->customer_name,
                    $row->total_amount,
                    $row->total_amount - $row->gross_profit,
                    $row->gross_profit
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['--- EXPENSE BREAKDOWN ---']);
            fputcsv($file, ['Date', 'Category', 'Description', 'Amount']);
            foreach ($expenses as $row) {
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

        $summary = $this->profitService->getSummary($startDate, $endDate);
        $revenue = $this->profitService->getRevenueBreakdown($startDate, $endDate, 500);
        $expenses = $this->cashFlowService->getCashOutDetails($startDate, $endDate, 500);

        $pdf = Pdf::loadView('reports.profit.pdf', compact(
            'summary', 'revenue', 'expenses', 'startDate', 'endDate'
        ))->setPaper('a4', 'landscape');

        return $pdf->download("profit-report-{$startDate->format('Ymd')}.pdf");
    }

    protected function authorizeAccess()
    {
        if (!auth()->user()->can('reports.profit')) {
            abort(403);
        }
    }
}
