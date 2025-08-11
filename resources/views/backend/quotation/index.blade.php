@extends('admin.admin_master')
@section('admin')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">قائمة عروض الأسعار</h4>
                            <a href="{{ route('quotation.add') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> إنشاء جديد
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>رقم العرض</th>
                                            <th>الزبون</th>
                                            <th>التاريخ</th>
                                            <th>صالح حتى</th>
                                            <th>المجموع</th>
                                            <th>الحالة</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($quotations as $quotation)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $quotation->quotation_no }}</td>
                                                <td>{{ $quotation->customer->name }}</td>
                                                <td>{{ $quotation->date }}</td>
                                                <td>{{ $quotation->valid_until }}</td>
                                                <td>{{ number_format($quotation->total_amount, 2) }}</td>
                                                <td>
                                                    @if ($quotation->status == 'pending')
                                                        <span class="badge bg-warning">قيد الانتظار</span>
                                                    @elseif($quotation->status == 'approved')
                                                        <span class="badge bg-success">معتمد</span>
                                                    @elseif($quotation->status == 'rejected')
                                                        <span class="badge bg-danger">مرفوض</span>
                                                    @elseif($quotation->status == 'converted')
                                                        <span class="badge bg-info">محول لطلبية</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <a href="{{ route('quotation.show', $quotation->id) }}"
                                                            class="btn btn-sm btn-info" title="عرض">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="" class="btn btn-sm btn-primary" title="تعديل">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="" class="btn btn-sm btn-secondary" title="طباعة"
                                                            target="_blank">
                                                            <i class="fas fa-print"></i>
                                                        </a>
                                                        @if ($quotation->status == 'approved')
                                                            <a href="" class="btn btn-sm btn-success"
                                                                title="تحويل لطلبية">
                                                                <i class="fas fa-exchange-alt"></i>
                                                            </a>
                                                        @endif
                                                        <form action="" method="POST" class="d-inline">
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
