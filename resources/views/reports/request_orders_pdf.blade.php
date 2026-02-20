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
        <h1>Request Orders Report</h1>
        <p>Generated on {{ date('Y-m-d H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>RO #</th>
                <th>Customer</th>
                <th>Status</th>
                <th>Amount</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $order)
                <tr>
                    <td><strong>{{ $order->request_number }}</strong></td>
                    <td>{{ $order->customer_name }}</td>
                    <td>{{ strtoupper($order->status) }}</td>
                    <td>${{ number_format($order->total_amount, 2) }}</td>
                    <td>{{ $order->created_at->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        &copy; {{ date('Y') }} Inventory System - Order-to-Cash Module
    </div>
</body>
</html>
