<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>عرض سعر #{{ $quotation->quotation_no }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            direction: rtl;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .company-info {
            margin-bottom: 30px;
        }
        .quotation-info {
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }
        th {
            background-color: #f2f2f2;
        }
        .total-row {
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            border-top: 1px solid #333;
            padding-top: 10px;
        }
        .signature {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>عرض سعر</h1>
        <h3>{{ config('app.name') }}</h3>
    </div>

    <div class="company-info">
        <p><strong>العنوان:</strong> عنوان الشركة هنا</p>
        <p><strong>الهاتف:</strong> 123456789</p>
        <p><strong>البريد الإلكتروني:</strong> info@company.com</p>
    </div>

    <div class="quotation-info">
        <div style="display: flex; justify-content: space-between;">
            <div>
                <p><strong>رقم العرض:</strong> {{ $quotation->quotation_no }}</p>
                <p><strong>التاريخ:</strong> {{ $quotation->date }}</p>
                <p><strong>صالح حتى:</strong> {{ $quotation->valid_until }}</p>
            </div>
            <div>
                <p><strong>الزبون:</strong> {{ $quotation->customer->name }}</p>
                <p><strong>هاتف الزبون:</strong> {{ $quotation->customer->mobile_no }}</p>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>المنتج</th>
                <th>الكمية</th>
                <th>السعر</th>
                <th>الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotation->quotationDetails as $detail)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $detail->product->name }}</td>
                <td>{{ $detail->quantity }}</td>
                <td>{{ number_format($detail->unit_price, 2) }}</td>
                <td>{{ number_format($detail->total_price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" style="text-align: left;">المجموع</td>
                <td>{{ number_format($quotation->total_amount + $quotation->discount_amount, 2) }}</td>
            </tr>
            @if($quotation->discount_amount > 0)
            <tr class="total-row">
                <td colspan="4" style="text-align: left;">الخصم</td>
                <td>{{ number_format($quotation->discount_amount, 2) }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td colspan="4" style="text-align: left;">الإجمالي النهائي</td>
                <td>{{ number_format($quotation->total_amount, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div>
        <p><strong>ملاحظات:</strong></p>
        <p>{{ $quotation->description ?? 'لا توجد ملاحظات' }}</p>
    </div>

    <div class="signature">
        <div>
            <p>توقيع الزبون</p>
            <p>.............................</p>
        </div>
        <div>
            <p>توقيع المسؤول</p>
            <p>.............................</p>
        </div>
    </div>

    <div class="footer">
        <p>شكراً لتعاملكم معنا</p>
        <p>هذا العرض ساري حتى {{ $quotation->valid_until }}</p>
    </div>
</body>
</html>