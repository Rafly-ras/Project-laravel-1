<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: sans-serif; color: #333; margin: 0; padding: 20px; }
        .header { margin-bottom: 50px; }
        .invoice-title { font-size: 30px; font-weight: bold; color: #4F46E5; }
        .meta-info { margin-top: 10px; font-size: 14px; color: #666; }
        .section-title { font-size: 10px; font-weight: bold; color: #999; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; }
        .table { width: 100%; border-collapse: collapse; margin: 30px 0; }
        .table th { background: #f9fafb; padding: 12px; font-size: 12px; text-transform: uppercase; color: #666; text-align: left; border-bottom: 2px solid #eee; }
        .table td { padding: 12px; font-size: 14px; border-bottom: 1px solid #eee; }
        .total-section { margin-top: 30px; float: right; width: 300px; }
        .total-row { display: flex; justify-content: space-between; padding: 10px 0; font-size: 14px; }
        .grand-total { font-size: 24px; font-weight: bold; color: #4F46E5; border-top: 2px solid #eee; margin-top: 10px; padding-top: 10px; }
        .status-badge { display: inline-block; padding: 5px 10px; border-radius: 5px; font-size: 10px; font-weight: bold; text-transform: uppercase; background: #eee; }
    </style>
</head>
<body>
    <div class="header">
        <div style="float: left;">
            <div class="invoice-title">INVOICE</div>
            <div class="meta-info">#{{ $invoice->invoice_number }}</div>
            <div class="meta-info">Issued: {{ $invoice->issued_at->format('M d, Y') }}</div>
        </div>
        <div style="float: right; text-align: right;">
            <div class="section-title">Customer</div>
            <div style="font-weight: bold; font-size: 16px;">{{ $invoice->salesOrder->customer_name }}</div>
            <div class="meta-info">SO Ref: {{ $invoice->salesOrder->sales_number }}</div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Description</th>
                <th style="text-align: center;">Qty</th>
                <th style="text-align: right;">Price</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->salesOrder->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td style="text-align: center;">{{ $item->qty }}</td>
                    <td style="text-align: right;">${{ number_format($item->price, 2) }}</td>
                    <td style="text-align: right; font-weight: bold;">${{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <table style="width: 100%;">
            <tr>
                <td style="text-align: right; font-size: 12px; color: #999; text-transform: uppercase;">Total Amount</td>
                <td style="text-align: right; font-size: 20px; font-weight: bold; color: #4F46E5;">${{ number_format($invoice->total_amount, 2) }}</td>
            </tr>
            <tr>
                <td style="text-align: right; font-size: 12px; color: #999; text-transform: uppercase;">Amount Paid</td>
                <td style="text-align: right; font-size: 14px; font-weight: bold; color: #10B981;">${{ number_format($invoice->paid_amount, 2) }}</td>
            </tr>
            <tr>
                <td style="text-align: right; font-size: 12px; color: #999; text-transform: uppercase; padding-top: 10px; border-top: 1px solid #eee;">Current Balance</td>
                <td style="text-align: right; font-size: 16px; font-weight: bold; padding-top: 10px; border-top: 1px solid #eee;">${{ number_format($invoice->remaining_balance, 2) }}</td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 100px;">
        <div class="section-title">Status</div>
        <div class="status-badge">{{ $invoice->status }}</div>
    </div>
</body>
</html>
