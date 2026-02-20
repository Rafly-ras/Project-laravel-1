<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cash Flow Report</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; font-size: 10pt; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #4f46e5; padding-bottom: 20px; }
        .header h1 { margin: 0; color: #1e1b4b; font-size: 24pt; font-weight: 900; }
        .header p { margin: 5px 0 0; color: #666; }
        
        .summary-grid { width: 100%; margin-bottom: 30px; border-collapse: collapse; }
        .summary-card { padding: 20px; border: 1px solid #e5e7eb; background: #f9fafb; text-align: center; width: 33.33%; }
        .summary-label { font-size: 8pt; font-weight: bold; text-transform: uppercase; color: #6b7280; margin-bottom: 5px; }
        .summary-value { font-size: 18pt; font-weight: 900; }
        .cash-in { color: #059669; }
        .cash-out { color: #dc2626; }
        .net { color: #4f46e5; }

        .section-title { font-size: 12pt; font-weight: bold; margin: 20px 0 10px; padding-left: 10px; border-left: 4px solid #4f46e5; color: #1f2937; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #f3f4f6; padding: 10px; text-align: left; font-size: 8pt; text-transform: uppercase; color: #4b5563; border-bottom: 1px solid #e5e7eb; }
        td { padding: 10px; border-bottom: 1px solid #f3f4f6; font-size: 9pt; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8pt; color: #9ca3af; padding-top: 20px; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Cash Flow Statement</h1>
        <p>Period: {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</p>
    </div>

    <table class="summary-grid">
        <tr>
            <td class="summary-card">
                <div class="summary-label">Total Cash In</div>
                <div class="summary-value cash-in">${{ number_format($summary['cash_in'], 2) }}</div>
            </td>
            <td class="summary-card">
                <div class="summary-label">Total Cash Out</div>
                <div class="summary-value cash-out">${{ number_format($summary['cash_out'], 2) }}</div>
            </td>
            <td class="summary-card">
                <div class="summary-label">Net Liquidity</div>
                <div class="summary-value net">${{ number_format($summary['net_cashflow'], 2) }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Cash Inflow Details</div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Receipt #</th>
                <th>Invoice #</th>
                <th>Customer</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cashIn as $pay)
            <tr>
                <td>{{ $pay->paid_at->format('Y-m-d') }}</td>
                <td class="font-bold">{{ $pay->payment_number }}</td>
                <td>{{ $pay->invoice->invoice_number }}</td>
                <td>{{ $pay->invoice->salesOrder->customer_name }}</td>
                <td class="text-right font-bold">${{ number_format($pay->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title" style="page-break-before: always;">Cash Outflow Details</div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Category</th>
                <th>Description</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cashOut as $exp)
            <tr>
                <td>{{ $exp->expense_date->format('Y-m-d') }}</td>
                <td>{{ $exp->category->name }}</td>
                <td>{{ $exp->description }}</td>
                <td class="text-right font-bold">${{ number_format($exp->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ now()->format('M d, Y H:i:s') }} | ERP Financial System
    </div>
</body>
</html>
