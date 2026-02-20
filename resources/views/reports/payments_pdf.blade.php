<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #4f46e5; text-transform: uppercase; letter-spacing: 2px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f3f4f6; color: #4b5563; text-transform: uppercase; font-weight: bold; padding: 10px; text-align: left; border-bottom: 2px solid #e5e7eb; }
        td { padding: 10px; border-bottom: 1px solid #f3f4f6; }
        .footer { margin-top: 30px; text-align: center; color: #9ca3af; font-size: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Payments Report</h1>
        <p>Generated on {{ date('Y-m-d H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>PAY #</th>
                <th>Invoice #</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Paid At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $payment)
                <tr>
                    <td><strong>{{ $payment->payment_number }}</strong></td>
                    <td>{{ $payment->invoice->invoice_number ?? 'N/A' }}</td>
                    <td>${{ number_format($payment->amount, 2) }}</td>
                    <td>{{ strtoupper($payment->payment_method) }}</td>
                    <td>{{ $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i') : 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        &copy; {{ date('Y') }} Inventory System - Order-to-Cash Module
    </div>
</body>
</html>
