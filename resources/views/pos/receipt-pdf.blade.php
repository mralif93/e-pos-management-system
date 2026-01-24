<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Receipt #{{ $sale->id }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px dashed #000;
            pb-10px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        .header p {
            margin: 2px 0;
        }

        .meta {
            margin-bottom: 20px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }

        .meta-row:after {
            content: "";
            display: table;
            clear: both;
        }

        /* Clearfix for float if needed, but flex doesn't work well in dompdf sometimes. using table for structure is safer */

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            text-align: left;
            border-bottom: 1px dashed #000;
            padding: 5px 0;
        }

        td {
            padding: 5px 0;
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .totals {
            border-top: 1px dashed #000;
            padding-top: 10px;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #555;
        }

        /* Table layout for totals to ensure alignment in PDF */
        .totals-table {
            width: 100%;
        }

        .totals-table td {
            padding: 2px 0;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ $outletSettings['name'] ?? 'POS Outlet' }}</h1>
        <p>{{ $outletSettings['address'] ?? 'Store Address' }}</p>
        <p>Tel: {{ $outletSettings['phone'] ?? '-' }}</p>
    </div>

    <table style="width: 100%; margin-bottom: 10px; font-size: 12px;">
        <tr>
            <td>Date: {{ $sale->created_at->format('d/m/Y H:i') }}</td>
            <td class="text-right">Receipt #: {{ $sale->id }}</td>
        </tr>
        <tr>
            <td>Cashier: {{ $sale->user ? $sale->user->name : '-' }}</td>
            <td class="text-right">Customer: {{ $sale->customer ? $sale->customer->name : 'Guest' }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th style="width: 50%">Item</th>
                <th style="width: 15%" class="text-right">Qty</th>
                <th style="width: 35%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->saleItems as $item)
                <tr>
                    <td>
                        {{ $item->product ? $item->product->name : 'Unknown Item' }}
                        @if($item->price != ($item->total_price / $item->quantity))
                            <br><span style="font-size: 10px; color: #666">@ {{ number_format($item->price, 2) }}</span>
                        @endif
                    </td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->total_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table class="totals-table">
            <tr>
                <td>Subtotal</td>
                <td class="text-right">{{ number_format($sale->saleItems->sum('total_price'), 2) }}</td>
            </tr>
            @if($sale->discount_amount > 0)
                <tr>
                    <td>Discount</td>
                    <td class="text-right">- {{ number_format($sale->discount_amount, 2) }}</td>
                </tr>
            @endif
            @if($sale->tax_amount > 0)
                <tr>
                    <td>Tax ({{ $outletSettings['tax_rate'] ?? 0 }}%)</td>
                    <td class="text-right">{{ number_format($sale->tax_amount, 2) }}</td>
                </tr>
            @endif
            <tr style="font-size: 14px; font-weight: bold;">
                <td style="padding-top: 10px;">Total</td>
                <td class="text-right" style="padding-top: 10px;">{{ number_format($sale->total_amount, 2) }}</td>
            </tr>
            <tr style="color: #666; font-size: 11px;">
                <td style="padding-top: 5px;">Payment Method</td>
                <td class="text-right" style="padding-top: 5px;">
                    @foreach($sale->payments as $payment)
                        {{ ucfirst($payment->payment_method) }}: {{ number_format($payment->amount, 2) }}<br>
                    @endforeach
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Thank you for your purchase!</p>
        <p>Generated by E-POS System</p>
    </div>
</body>

</html>