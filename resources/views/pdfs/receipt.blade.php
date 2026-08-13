<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Receipt {{ $receiptNumber }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .info {
            margin: 20px 0;
        }

        .info table {
            width: 100%;
            border-collapse: collapse;
        }

        .info td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        .total {
            font-size: 18px;
            font-weight: bold;
            text-align: right;
            margin-top: 20px;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ $journal->name }}</h1>
        <p>Payment Receipt</p>
    </div>

    <div class="info">
        <table>
            <tr>
                <td><strong>Receipt Number:</strong></td>
                <td>{{ $receiptNumber }}</td>
            </tr>
            <tr>
                <td><strong>Date:</strong></td>
                <td>{{ now()->format('d M Y H:i') }}</td>
            </tr>
            <tr>
                <td><strong>Article:</strong></td>
                <td>{{ $submission->title ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Invoice Number:</strong></td>
                <td>{{ $invoice->invoice_number }}</td>
            </tr>
            <tr>
                <td><strong>Author:</strong></td>
                <td>{{ $submission->author->name ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="total">
        <p>Amount Paid: {{ format_currency($invoice->amount, $invoice->currency) }}</p>
    </div>

    <div class="footer">
        <p>This is an automatically generated receipt.</p>
        <p>Generated on: {{ now()->format('d M Y H:i:s') }}</p>
    </div>
</body>

</html>
