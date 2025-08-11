<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلبية رقم {{ $order->order_no }}</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            src: url("{{ public_path('fonts/DejaVuSans.ttf') }}") format('truetype');
            font-weight: normal;
        }



        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12pt;
            direction: rtl;
            text-align: right;
            margin: 0;
            padding: 0;
            line-height: 1.5;
            color: #000;
        }


        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }

        .document-title {
            font-size: 20px;
            color: #333;
            margin: 10px 0;
        }

        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .info-left,
        .info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .info-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 5px;
        }

        .info-title {
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .info-item {
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 80px;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .products-table th,
        .products-table td {
            border: 1px solid #dee2e6;
            padding: 12px 8px;
            text-align: center;
        }

        .products-table th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        .products-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .totals-section {
            margin-top: 20px;
            float: left;
            width: 300px;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 8px 12px;
            border: 1px solid #dee2e6;
        }

        .totals-table .label {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: right;
            width: 60%;
        }

        .totals-table .value {
            text-align: center;
            width: 40%;
        }

        .total-final {
            background-color: #007bff !important;
            color: white !important;
            font-weight: bold;
            font-size: 16px;
        }

        .notes-section {
            clear: both;
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending {
            background-color: #ffc107;
            color: #212529;
        }

        .status-approved {
            background-color: #28a745;
            color: white;
        }

        .status-rejected {
            background-color: #dc3545;
            color: white;
        }

        .status-converted {
            background-color: #17a2b8;
            color: white;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="company-name">شركة تطوير للحلول التقنية</div>
        <div class="document-title">طلبية</div>
        <div>رقم الطلبية: {{ $order->order_no }}</div>
    </div>

    <div class="info-section">
        <div class="info-right">
            <div class="info-box">
                <div class="info-title">معلومات الطلبية</div>
                <div class="info-item">
                    <span class="info-label">رقم الطلبية:</span>
                    {{ $order->order_no }}
                </div>
                <div class="info-item">
                    <span class="info-label">التاريخ:</span>
                    {{ $order->date }}
                </div>
                <div class="info-item">
                    <span class="info-label">الحالة:</span>
                    @if ($order->status == 'pending')
                        <span class="status-badge status-pending">قيد الانتظار</span>
                    @elseif($order->status == 'approved')
                        <span class="status-badge status-approved">معتمدة</span>
                    @elseif($order->status == 'rejected')
                        <span class="status-badge status-rejected">مرفوضة</span>
                    @elseif($order->status == 'converted')
                        <span class="status-badge status-converted">محولة لفاتورة</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="info-left">
            <div class="info-box">
                <div class="info-title">معلومات الزبون</div>
                <div class="info-item">
                    <span class="info-label">الاسم:</span>
                    {{ $order->customer->name }}
                </div>
                <div class="info-item">
                    <span class="info-label">الهاتف:</span>
                    {{ $order->customer->mobile_no ?? 'غير محدد' }}
                </div>
                <div class="info-item">
                    <span class="info-label">العنوان:</span>
                    {{ $order->customer->address ?? 'غير محدد' }}
                </div>
            </div>
        </div>
    </div>

    <table class="products-table">
        <thead>
            <tr>
                <th>#</th>
                <th>اسم المنتج</th>
                <th>الفئة</th>
                <th>الكمية</th>
                <th>سعر الوحدة</th>
                <th>الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->orderDetails as $index => $detail)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $detail->product->name }}</td>
                    <td>{{ $detail->category->name ?? 'غير محدد' }}</td>
                    <td>{{ $detail->quantity }}</td>
                    <td>{{ number_format($detail->unit_price, 2) }} شيكل</td>
                    <td>{{ number_format($detail->total_price, 2) }} شيكل</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-section">
        <table class="totals-table">
            <tr>
                <td class="label">المجموع الفرعي:</td>
                <td class="value">{{ number_format($order->subtotal, 2) }} شيكل</td>
            </tr>
            @if ($order->discount_amount > 0)
                <tr>
                    <td class="label">الخصم:</td>
                    <td class="value">{{ number_format($order->discount_amount, 2) }} شيكل</td>
                </tr>
            @endif
            <tr class="total-final">
                <td class="label">المجموع النهائي:</td>
                <td class="value">{{ number_format($order->total_amount, 2) }} شيكل</td>
            </tr>
        </table>
    </div>

    @if ($order->description)
        <div class="notes-section">
            <strong>ملاحظات:</strong><br>
            {{ $order->description }}
        </div>
    @endif

    <div class="footer">
        <p>تم إنشاء هذه الطلبية بتاريخ {{ $order->created_at->format('Y-m-d H:i') }}</p>
        <p>شكراً لك على ثقتك بنا</p>
    </div>
</body>

</html>
