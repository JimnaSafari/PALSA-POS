<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - {{ $receipt_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .customer-info {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .totals {
            margin-top: 20px;
            border-top: 2px solid #000;
            padding-top: 10px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .grand-total {
            font-weight: bold;
            font-size: 16px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">PALSA POS</div>
        <div>Point of Sale System</div>
        <div>Thank you for your business!</div>
    </div>

    <div class="receipt-info">
        <div>
            <strong>Receipt #:</strong> {{ $receipt_number }}<br>
            <strong>Order Code:</strong> {{ $order_code }}<br>
            <strong>Date:</strong> {{ $date->format('Y-m-d H:i:s') }}
        </div>
    </div>

    <div class="customer-info">
        <strong>Customer Information:</strong><br>
        Name: {{ $customer->name }}<br>
        @if($customer->phone)
        Phone: {{ $customer->phone }}<br>
        @endif
        @if($customer->email)
        Email: {{ $customer->email }}<br>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>SKU</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item['name'] }}</td>
                <td>{{ $item['sku'] }}</td>
                <td class="text-right">{{ $item['quantity'] }}</td>
                <td class="text-right">@kes($item['unit_price'])</td>
                <td class="text-right">@kes($item['total_price'])</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="total-row">
            <span>Subtotal:</span>
            <span>{{ $totals['subtotal_formatted'] }}</span>
        </div>
        @if($totals['discount_amount'] > 0)
        <div class="total-row">
            <span>Discount:</span>
            <span>-{{ $totals['discount_formatted'] }}</span>
        </div>
        @endif
        <div class="total-row">
            <span>Tax (VAT 16%):</span>
            <span>{{ $totals['tax_formatted'] }}</span>
        </div>
        <div class="total-row grand-total">
            <span>TOTAL:</span>
            <span>{{ $totals['total_formatted'] }}</span>
        </div>
        <div class="total-row">
            <span>Total Items:</span>
            <span>{{ $totals['items_count'] }}</span>
        </div>
    </div>

    <div class="footer">
        <p>This is a computer-generated receipt.</p>
        <p>For any queries, please contact our support team.</p>
        <p>Generated on {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>