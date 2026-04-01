<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Service Payment Receipt</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background: #f2f2f2; }
        .right { text-align: right; }
        .no-border { border: none; }
    </style>
</head>
<body>

<h2>Service Payment Receipt</h2>

<p>
    <strong>Receipt No:</strong> {{ $payment->receipt_no }}<br>
    <strong>Date:</strong> {{ \Carbon\Carbon::parse($payment->payment_date)->format('d-m-Y') }}
</p>

<hr>

<p>
    <strong>Client:</strong><br>
    {{ $payment->serviceInvoice->client->company_name }}<br>
    GSTIN: {{ $payment->serviceInvoice->client->gstin ?? 'Unregistered' }}
</p>

<p>
    <strong>Service Invoice No:</strong> {{ $payment->serviceInvoice->invoice_no }}
</p>

<table>
    <tr>
        <th>Payment Method</th>
        <th class="right">Amount Paid</th>
    </tr>
    <tr>
        <td>{{ ucfirst($payment->payment_method) }}</td>
        <td class="right">{{ number_format($payment->amount, 2) }}</td>
    </tr>
</table>

<table>
    <tr>
        <td class="no-border right"><strong>Invoice Total:</strong></td>
        <td class="right">{{ number_format($payment->serviceInvoice->grand_total, 2) }}</td>
    </tr>
    <tr>
        <td class="no-border right"><strong>Total Paid:</strong></td>
        <td class="right">{{ number_format($payment->serviceInvoice->payments()->sum('amount'), 2) }}</td>
    </tr>
    <tr>
        <td class="no-border right"><strong>Balance Due:</strong></td>
        <td class="right"><strong>{{ number_format($payment->due_amount, 2) }}</strong></td>
    </tr>
</table>

<p style="margin-top:30px;">
    Generated on {{ now()->format('d-m-Y H:i') }}
</p>

</body>
</html>