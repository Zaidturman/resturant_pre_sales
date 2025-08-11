@extends('admin.admin_master')
@section('admin')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h4 class="card-title text-white">تفاصيل عرض السعر #{{ $quotation->quotation_no }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h5>معلومات الزبون</h5>
                                    <hr>
                                    <p><strong>الاسم:</strong> {{ $quotation->customer->name }}</p>
                                    <p><strong>الهاتف:</strong> {{ $quotation->customer->mobile_no }}</p>
                                    <p><strong>البريد الإلكتروني:</strong> {{ $quotation->customer->email ?? 'غير متوفر' }}
                                    </p>
                                </div>
                                <div class="col-md-6 text-end">
                                    <h5>معلومات العرض</h5>
                                    <hr>
                                    <p><strong>رقم العرض:</strong> {{ $quotation->quotation_no }}</p>
                                    <p><strong>تاريخ الإنشاء:</strong> {{ $quotation->date }}</p>
                                    <p><strong>صالح حتى:</strong> {{ $quotation->valid_until }}</p>
                                    <p><strong>الحالة:</strong>
                                        @if ($quotation->status == 'pending')
                                            <span class="badge bg-warning">قيد الانتظار</span>
                                        @elseif($quotation->status == 'approved')
                                            <span class="badge bg-success">معتمد</span>
                                        @elseif($quotation->status == 'rejected')
                                            <span class="badge bg-danger">مرفوض</span>
                                        @elseif($quotation->status == 'converted')
                                            <span class="badge bg-info">محول لطلبية</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="table-responsive mb-4">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>المنتج</th>
                                            <th>الكمية</th>
                                            <th>السعر</th>
                                            <th>الإجمالي</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($quotation->quotationDetails as $key => $detail)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $detail->product->name }}</td>
                                                <td>{{ $detail->quantity }}</td>
                                                <td>{{ number_format($detail->unit_price, 2) }} شيكل</td>
                                                <td>{{ number_format($detail->quantity * $detail->unit_price, 2) }} شيكل
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" class="text-end"><strong>المجموع:</strong></td>
                                            <td>{{ number_format($quotation->total_amount + $quotation->discount_amount, 2) }}
                                                شيكل</td>
                                        </tr>
                                        @if ($quotation->discount_amount > 0)
                                            <tr>
                                                <td colspan="4" class="text-end"><strong>الخصم:</strong></td>
                                                <td>{{ number_format($quotation->discount_amount, 2) }} شيكل</td>
                                            </tr>
                                        @endif
                                        <tr class="table-active">
                                            <td colspan="4" class="text-end"><strong>الإجمالي النهائي:</strong></td>
                                            <td>{{ number_format($quotation->total_amount, 2) }} شيكل</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <h5>ملاحظات</h5>
                                    <hr>
                                    <p>{{ $quotation->description ?? 'لا توجد ملاحظات' }}</p>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12 d-flex justify-content-between">
                                    <a href="{{ route('quotation.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-right me-2"></i> رجوع
                                    </a>

                                    <div class="btn-group">
                                        @if ($quotation->status == 'pending')
                                            <form action="" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success me-2">
                                                    <i class="fas fa-check-circle me-2"></i> اعتماد
                                                </button>
                                            </form>
                                            <form action="" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-danger me-2">
                                                    <i class="fas fa-times-circle me-2"></i> رفض
                                                </button>
                                            </form>
                                        @endif

                                        @if (in_array($quotation->status, ['pending', 'approved']))
                                            <a href="" class="btn btn-info me-2">
                                                <i class="fas fa-exchange-alt me-2"></i> تحويل لطلبية
                                            </a>
                                        @endif

                                        <a href="" class="btn btn-primary">
                                            <i class="fas fa-download me-2"></i> تحميل PDF
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
