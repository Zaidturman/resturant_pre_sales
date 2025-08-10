@extends('admin.admin_master')
@section('admin')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="card-title">تفاصيل الطلبية #{{ $order->order_no }}</h4>
                        <div>
                            <span class="badge bg-{{ $order->status == 'approved' ? 'success' : ($order->status == 'rejected' ? 'danger' : 'warning') }}">
                                {{ $order->status == 'pending' ? 'قيد الانتظار' : ($order->status == 'approved' ? 'معتمد' : 'مرفوض') }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>معلومات الزبون</h5>
                                <p><strong>الاسم:</strong> {{ $order->customer->name }}</p>
                                <p><strong>الهاتف:</strong> {{ $order->customer->mobile_no }}</p>
                                <p><strong>البريد الإلكتروني:</strong> {{ $order->customer->email ?? 'غير محدد' }}</p>
                            </div>
                            <div class="col-md-6 text-end">
                                <h5>معلومات الطلبية</h5>
                                <p><strong>رقم الطلبية:</strong> {{ $order->order_no }}</p>
                                <p><strong>التاريخ:</strong> {{ $order->date }}</p>
                                <p><strong>أنشئت بواسطة:</strong> {{ $order->createdBy->name }}</p>
                            </div>
                        </div>

                        <div class="table-responsive mb-4">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>المنتج</th>
                                        <th>الفئة</th>
                                        <th>الكمية</th>
                                        <th>السعر</th>
                                        <th>الإجمالي</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->orderDetails as $detail)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $detail->product->name }}</td>
                                        <td>{{ $detail->category->name ?? 'غير محدد' }}</td>
                                        <td>{{ $detail->quantity }}</td>
                                        <td>{{ number_format($detail->unit_price, 2) }}</td>
                                        <td>{{ number_format($detail->total_price, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-end">المجموع:</th>
                                        <th>{{ number_format($order->total_amount + $order->discount_amount, 2) }}</th>
                                    </tr>
                                    @if($order->discount_amount > 0)
                                    <tr>
                                        <th colspan="5" class="text-end">الخصم:</th>
                                        <th>{{ number_format($order->discount_amount, 2) }}</th>
                                    </tr>
                                    @endif
                                    <tr class="table-active">
                                        <th colspan="5" class="text-end">الإجمالي النهائي:</th>
                                        <th>{{ number_format($order->total_amount, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <h5>ملاحظات</h5>
                                <p>{{ $order->description ?? 'لا توجد ملاحظات' }}</p>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12 d-flex justify-content-between">
                                <a href="{{ route('order.print', $order->id) }}" class="btn btn-secondary">
                                    <i class="fas fa-print"></i> طباعة
                                </a>
                                <div class="d-flex gap-2">
                                    @if($order->status == 'pending')
                                       <a href="{{ route('order.approve', $order->id) }}" 
   class="btn btn-success"
   onclick="return confirm('هل تريد اعتماد هذه الطلبية؟')">
    <i class="fas fa-check"></i> اعتماد
</a>
                                        <a href="{{ route('order.reject', $order->id) }}" 
   class="btn btn-danger"
   onclick="return confirm('هل تريد رفض هذه الطلبية؟')">
    <i class="fas fa-times"></i> رفض
</a>
                                    @endif
                                    @if($order->status == 'approved')
<a href="{{ route('order.convert.invoice', $order->id) }}" class="btn btn-primary">
                                            <i class="fas fa-exchange-alt"></i> تحويل لفاتورة
                                        </a>
                                    @endif
                                    <a href="{{ route('order.index') }}" class="btn btn-light">
                                        <i class="fas fa-arrow-left"></i> رجوع
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection