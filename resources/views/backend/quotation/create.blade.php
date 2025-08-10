@extends('admin.admin_master')
@section('admin')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="card-title">إنشاء عرض سعر جديد</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('quotation.store') }}" id="quotation-form">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">رقم عرض السعر</label>
                                        <input type="text" class="form-control" name="quotation_no" 
                                               value="{{ $quotation_no }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">تاريخ العرض</label>
                                        <input type="date" class="form-control" name="date" 
                                               value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">صالح حتى</label>
                                        <input type="date" class="form-control" name="valid_until" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">الزبون</label>
                                        <select class="form-select" name="customer_id" required>
                                            <option value="">اختر الزبون</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5 class="mb-3">تفاصيل المنتجات</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="product-table">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>المنتج</th>
                                                    <th>الفئة</th>
                                                    <th>الكمية</th>
                                                    <th>السعر</th>
                                                    <th>الإجمالي</th>
                                                    <th>الإجراءات</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- سيتم إضافة الصفوف ديناميكياً -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-primary mt-2" id="add-product" onclick="showProductModal">
                                        <i class="fas fa-plus"></i> إضافة منتج
                                    </button>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-6 offset-md-6">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <tr>
                                                <th>المجموع:</th>
                                                <td><span id="subtotal">0.00</span> شيكل</td>
                                            </tr>
                                            <tr>
                                                <th>الخصم:</th>
                                                <td>
                                                    <input type="number" class="form-control" name="discount_amount" 
                                                           id="discount" value="0" min="0" step="0.01">
                                                </td>
                                            </tr>
                                            <tr class="table-active">
                                                <th>الإجمالي النهائي:</th>
                                                <td><span id="total">0.00</span> شيكل</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">ملاحظات</label>
                                        <textarea class="form-control" name="description" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12 text-end">
                                    <a href="{{ route('quotation.index') }}" class="btn btn-secondary me-2">
                                        <i class="fas fa-arrow-left"></i> رجوع
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> حفظ عرض السعر
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- نافذة اختيار المنتج -->
<div class="modal fade" id="product-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">اختر المنتج</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <input type="text" class="form-control" id="product-search" placeholder="ابحث عن منتج...">
                    </div>
                </div>
                <div class="row" id="product-list">
                    @foreach($products as $product)
                    <div class="col-md-4 mb-3 product-item" 
                         data-id="{{ $product->id }}" 
                         data-name="{{ $product->name }}" 
                         data-category="{{ $product->category->name ?? 'غير محدد' }}" 
                         data-category-id="{{ $product->category_id ?? '' }}" 
                         data-price="{{ $product->selling_price ?? $product->price ?? 0 }}">
                        <div class="card h-100 border-0 shadow-sm product-card">
                            <div class="card-body text-center">
                                <h6 class="card-title text-truncate">{{ $product->name }}</h6>
                                <p class="text-muted small">{{ $product->category->name ?? 'غير محدد' }}</p>
                                <p class="text-primary fw-bold">{{ number_format($product->selling_price ?? $product->price ?? 0, 2) }} شيكل</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

function showProductModal() {
    $('#product-modal').modal('show');
}

$(document).ready(function() {
    let productCount = 0;
    let subtotal = 0;
    
    // فتح نافذة اختيار المنتج
    $('#add-product').click(function() {
        $('#product-modal').modal('show');
    });


    
    // البحث عن المنتجات
    $('#product-search').on('keyup', function() {
        const search = $(this).val().toLowerCase();
        $('.product-item').each(function() {
            const text = $(this).find('.card-title').text().toLowerCase();
            const category = $(this).find('.text-muted').text().toLowerCase();
            if (text.indexOf(search) > -1 || category.indexOf(search) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
    
    // إضافة تأثير hover للمنتجات
    $('.product-item').hover(
        function() { $(this).find('.product-card').addClass('border-primary'); },
        function() { $(this).find('.product-card').removeClass('border-primary'); }
    );
    
    // اختيار المنتج
    $(document).on('click', '.product-item', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const category = $(this).data('category');
        const categoryId = $(this).data('category-id');
        const price = parseFloat($(this).data('price')) || 0;
        
        // التحقق من عدم تكرار المنتج
        if ($(`#product-row-${id}`).length > 0) {
            alert('هذا المنتج مضاف بالفعل');
            return;
        }
        
        // إضافة صف جديد للجدول
        const row = `
            <tr id="product-row-${id}">
                <td>
                    ${name}
                    <input type="hidden" name="product_id[]" value="${id}">
                </td>
                <td>
                    ${category}
                    <input type="hidden" name="category_id[]" value="${categoryId}">
                </td>
                <td>
                    <input type="number" class="form-control quantity" name="quantity[]" value="1" min="1" step="1">
                </td>
                <td>
                    <input type="number" class="form-control price" name="unit_price[]" value="${price}" min="0" step="0.01">
                </td>
                <td class="item-total">${price.toFixed(2)}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger remove-item">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        
        $('#product-table tbody').append(row);
        productCount++;
        updateSubtotal();
        $('#product-modal').modal('hide');
        
        // مسح البحث
        $('#product-search').val('');
        $('.product-item').show();
    });
    
    // تحديث المجموع عند تغيير الكمية أو السعر
    $(document).on('input change', '.quantity, .price', function() {
        const row = $(this).closest('tr');
        const quantity = parseFloat(row.find('.quantity').val()) || 0;
        const price = parseFloat(row.find('.price').val()) || 0;
        const total = quantity * price;
        
        row.find('.item-total').text(total.toFixed(2));
        updateSubtotal();
    });
    
    // حذف المنتج من الجدول
    $(document).on('click', '.remove-item', function() {
        if (confirm('هل أنت متأكد من حذف هذا المنتج؟')) {
            $(this).closest('tr').remove();
            productCount--;
            updateSubtotal();
        }
    });
    
    // تحديث الخصم والإجمالي
    $('#discount').on('input change', function() {
        updateTotal();
    });
    
    // تحديث المجموع الفرعي
    function updateSubtotal() {
        subtotal = 0;
        $('.item-total').each(function() {
            subtotal += parseFloat($(this).text()) || 0;
        });
        
        $('#subtotal').text(subtotal.toFixed(2));
        updateTotal();
    }
    
    // تحديث الإجمالي النهائي
    function updateTotal() {
        const discount = parseFloat($('#discount').val()) || 0;
        const total = Math.max(0, subtotal - discount);
        $('#total').text(total.toFixed(2));
    }
    
    // التحقق من صحة النموذج قبل الإرسال
    $('#quotation-form').submit(function(e) {
        if (productCount === 0) {
            e.preventDefault();
            alert('يجب إضافة منتج واحد على الأقل');
            return false;
        }
        
        // التحقق من الكميات والأسعار
        let isValid = true;
        $('.quantity, .price').each(function() {
            if (parseFloat($(this).val()) <= 0) {
                isValid = false;
                $(this).focus();
                return false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('يجب أن تكون جميع الكميات والأسعار أكبر من صفر');
            return false;
        }
        
        // التحقق من تاريخ الصلاحية
        const dateValue = $('input[name="date"]').val();
        const validUntilValue = $('input[name="valid_until"]').val();
        
        if (new Date(validUntilValue) <= new Date(dateValue)) {
            e.preventDefault();
            alert('تاريخ الصلاحية يجب أن يكون بعد تاريخ العرض');
            $('input[name="valid_until"]').focus();
            return false;
        }
    });
});
</script>
@endpush