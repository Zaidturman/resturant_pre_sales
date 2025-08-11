@extends('admin.admin_master')
@section('admin')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header  d-flex justify-content-between align-items-center">
                            <h4 class="card-title">قائمة الطلبيات</h4>
                            <a href="{{ route('order.add') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> إنشاء جديد
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>رقم الطلبية</th>
                                            <th>الزبون</th>
                                            <th>التاريخ</th>
                                            <th>المجموع</th>
                                            <th>الحالة</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($orders as $order)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $order->order_no }}</td>
                                                <td>{{ $order->customer->name }}</td>
                                                <td>{{ $order->date }}</td>
                                                <td>{{ number_format($order->total_amount, 2) }}</td>
                                                <td>
                                                    @if ($order->status == 'pending')
                                                        <span class="badge bg-warning">قيد الانتظار</span>
                                                    @elseif($order->status == 'approved')
                                                        <span class="badge bg-success">معتمد</span>
                                                    @elseif($order->status == 'rejected')
                                                        <span class="badge bg-danger">مرفوض</span>
                                                    @elseif($order->status == 'converted')
                                                        <span class="badge bg-info">محول لفاتورة</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <!-- عرض -->
                                                        <a href="{{ route('order.show', $order->id) }}"
                                                            class="btn btn-sm btn-info" title="عرض">
                                                            <i class="fas fa-eye"></i>
                                                        </a>

                                                        <!-- تعديل -->
                                                        <a href="{{ route('order.edit', $order->id) }}"
                                                            class="btn btn-sm btn-primary" title="تعديل">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <!-- طباعة -->
                                                        <a href="{{ route('order.print', $order->id) }}"
                                                            class="btn btn-sm btn-secondary" title="طباعة" target="_blank">
                                                            <i class="fas fa-print"></i>
                                                        </a>

                                                        <!-- تحويل لفاتورة (فقط إذا كان معتمدًا) -->
                                                        @if ($order->status == 'approved')
                                                            <a href="{{ route('order.convert.invoice', $order->id) }}"
                                                                class="btn btn-sm btn-success" title="تحويل لفاتورة">
                                                                <i class="fas fa-exchange-alt"></i>
                                                            </a>
                                                        @endif

                                                        <!-- حذف -->
                                                        <form action="{{ route('order.delete', $order->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger"
                                                                title="حذف" onclick="return confirm('هل أنت متأكد؟')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
