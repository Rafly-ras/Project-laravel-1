<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Profit & Loss Statement</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; font-size: 10pt; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #4f46e5; padding-bottom: 20px; }
        .header h1 { margin: 0; color: #1e1b4b; font-size: 24pt; font-weight: 900; }
        .header p { margin: 5px 0 0; color: #666; font-style: italic; }
        
        .summary-grid { width: 100%; margin-bottom: 30px; border-collapse: collapse; }
        .summary-card { padding: 15px; border: 1px solid #e5e7eb; background: #f9fafb; text-align: center; width: 25%; }
        .summary-label { font-size: 7pt; font-weight: bold; text-transform: uppercase; color: #6b7280; margin-bottom: 5px; }
        .summary-value { font-size: 14pt; font-weight: 900; }
        
        .revenue-color { color: #1e1b4b; }
        .cogs-color { color: #dc2626; }
        .gross-color { color: #059669; }
        .net-color { color: #4f46e5; }

        .section-title { font-size: 11pt; font-weight: bold; margin: 20px 0 10px; padding-left: 10px; border-left: 4px solid #4f46e5; color: #1f2937; text-transform: uppercase; letter-spacing: 1px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #f3f4f6; padding: 8px; text-align: left; font-size: 7pt; text-transform: uppercase; color: #4b5563; border-bottom: 1px solid #e5e7eb; }
        td { padding: 8px; border-bottom: 1px solid #f3f4f6; font-size: 8pt; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 7pt; color: #9ca3af; padding-top: 15px; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Income Statement</h1>
        <p>Accrual Basis | Period: {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</p>
    </div>

    <table class="summary-grid">
        <tr>
            <td class="summary-card">
                <div class="summary-label">Total Revenue</div>
                <div class="summary-value revenue-color">${{ number_format($summary['revenue'], 2) }}</div>
            </td>
            <td class="summary-card">
                <div class="summary-label">Gross Profit</div>
                <div class="summary-value gross-color">${{ number_format($summary['gross_profit'], 2) }}</div>
            </td>
            <td class="summary-card">
                <div class="summary-label">Total Expenses</div>
                <div class="summary-value cogs-color">${{ number_format($summary['expenses'], 2) }}</div>
            </td>
            <td class="summary-card">
                <div class="summary-label">Net Profit</div>
                <div class="summary-value net-color">${{ number_format($summary['net_profit'], 2) }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Revenue Breakdown</div>
    <table>
        <thead>
            <tr>
                <th>Sales Order</th>
                <th>Confirmed Date</th>
                <th>Customer</th>
                <th class="text-right">Revenue</th>
                <th class="text-right">COGS</th>
                <th class="text-right">Gross Profit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($revenue as $so)
            <tr>
                <td class="font-bold">{{ $so->sales_number }}</td>
                <td>{{ $so->confirmed_at->format('Y-m-d') }}</td>
                <td>{{ $so->customer_name }}</td>
                <td class="text-right">${{ number_format($so->total_amount, 2) }}</td>
                <td class="text-right">${{ number_format($so->total_amount - $so->gross_profit, 2) }}</td>
                <td class="text-right font-bold">${{ number_format($so->gross_profit, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title" style="page-break-before: always;">Expense Breakdown</div>
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
            @foreach($expenses as $exp)
            <tr>
                <td>{{ $exp->expense_date->format('Y-m-d') }}</td>
                <td>{{ $exp->category->name }}</td>
                <td>{{ $exp->description }}</td>
                <td class="text-right font-bold text-rose-600">${{ number_format($exp->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ now()->format('M d, Y H:i:s') }} | P&L Report | Internal Financial Use
    </div>
</body>
</html>
