<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Receipt #{{ $sale->id }}</title>
    <style>
        @page {
            margin: 0;
            size: 80mm 297mm;
            /* Simulate roll paper width, A4 height fallback */
        }

        body {
            font-family: 'Courier New', monospace;
            /* Thermal style */
            font-size: 10px;
            margin: 0;
            padding: 5px;
            width: 78mm;
            /* Fit within 80mm */
            background: #fff;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 14px;
            margin: 0;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header p {
            margin: 2px 0;
            font-size: 10px;
        }

        .divider {
            border-bottom: 1px dashed #000;
            margin: 5px 0;
            width: 100%;
        }

        .meta {
            font-size: 9px;
            margin-bottom: 5px;
        }

        .meta p {
            margin: 2px 0;
            display: flex;
            justify-content: space-between;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        th {
            text-align: left;
            border-bottom: 1px dashed #000;
            padding: 2px 0;
        }

        td {
            padding: 2px 0;
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .totals {
            margin-top: 5px;
            border-top: 1px dashed #000;
            padding-top: 5px;
        }

        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 9px;
        }

        .big-total {
            font-size: 16px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ $outletSettings['name'] ?? 'POS Outlet' }}</h1>
        <p>{{ $outletSettings['address'] ?? 'Store Address' }}</p>
        <p>Tel: {{ $outletSettings['phone'] ?? '-' }}</p>
    </div>

    <div class="divider"></div>

    <div class="meta">
        <p><span>Receipt #:</span> <span style="float:right">{{ $sale->id }}</span></p>
        <p><span>Date:</span> <span style="float:right">{{ $sale->created_at->format('d/m/Y H:i') }}</span></p>
        <p><span>Cashier:</span> <span style="float:right">{{ $sale->user ? $sale->user->name : '-' }}</span></p>
        <p><span>Customer:</span> <span
                style="float:right">{{ $sale->customer ? $sale->customer->name : 'Guest' }}</span></p>
    </div>

    <div class="divider"></div>

    <table>
        <thead>
            <tr>
                <th width="55%">Item</th>
                <th width="15%" class="text-right">Qty</th>
                <th width="30%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->saleItems as $item)
                <tr>
                    <td>
                        {{ $item->product ? $item->product->name : 'Item' }}
                        @if($item->quantity > 1)
                            <br><span style="font-size: 8px;">@ {{ number_format($item->price, 2) }}</span>
                        @endif
                    </td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->total_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table style="width: 100%">
            <tr>
                <td>Subtotal</td>
                <td class="text-right">{{ number_format($sale->saleItems->sum('total_price'), 2) }}</td>
            </tr>
            @if($sale->tax_amount > 0)
                <tr>
                    <td>Tax ({{ $outletSettings['tax_rate'] ?? 0 }}%)</td>
                    <td class="text-right">{{ number_format($sale->tax_amount, 2) }}</td>
                </tr>
            @endif
            @if($sale->discount_amount > 0)
                <tr>
                    <td>Discount</td>
                    <td class="text-right">-{{ number_format($sale->discount_amount, 2) }}</td>
                </tr>
            @endif
            <tr>
                <td class="big-total" style="padding-top: 5px;">Total</td>
                <td class="text-right big-total" style="padding-top: 5px;">{{ number_format($sale->total_amount, 2) }}
                </td>
            </tr>

            <tr>
                <td colspan="2" class="divider"></td>
            </tr>

            @foreach($sale->payments as $payment)
                <tr>
                    <td>{{ ucfirst($payment->payment_method) }}</td>
                    <td class="text-right">{{ number_format($payment->amount, 2) }}</td>
                </tr>
            @endforeach

            @php 
                                $totalPaid = $sale->payments->sum('amount');
                $change = $totalPaid - $sale->total_amount;
            @endphp
            @if($change > 0)
                <tr>
                    <td>Change</td>
                    <td class="text-right">{{ number_format($change, 2) }}</td>
                </tr>
            @endif
        </table>
    </div>

    <div class="footer">
        <p>THANK YOU</p>
       
 <p>Please come again!</p>
        <p style="margin-top: 5px; font-size: 8px;">Powered by E-POS</p>
    </div>
</body>
</html>