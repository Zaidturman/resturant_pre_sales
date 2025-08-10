@extends('admin.admin_new_invoice')
@section('admin')
    <style>
        /* تخصيصات الطلبية */
        :root {
            --order-primary: #198754;
            --order-primary-light: #e6f3ed;
            --order-secondary: #6c757d;
            --order-danger: #dc3545;
            --order-light: #f8f9fa;
            --order-dark: #212529;
        }
        
        .order-step-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 30px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }
        
        .order-step-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--order-primary);
        }
        
        .order-nav-tabs {
            border-bottom: 2px solid #eee;
            margin-bottom: 25px;
        }
        
        .order-nav-tabs .nav-link {
            border: none;
            color: var(--order-secondary);
            font-weight: 600;
            padding: 12px 25px;
            position: relative;
            transition: all 0.3s ease;
            font-size: 15px;
            border-radius: 0;
        }
        
        .order-nav-tabs .nav-link:hover {
            color: var(--order-primary);
            background: var(--order-primary-light);
        }
        
        .order-nav-tabs .nav-link.active {
            color: var(--order-primary);
            background: transparent;
        }
        
        .order-nav-tabs .nav-link.active:after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--order-primary);
            border-radius: 3px 3px 0 0;
        }
        
        .order-nav-tabs .nav-link i {
            margin-left: 8px;
            font-size: 16px;
        }
        
        /* بطاقات الزبائن */
        .customer-card {
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            height: 100%;
        }
        
        .customer-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            border-color: var(--order-primary);
        }
        
        .customer-card.selected {
            border: 2px solid var(--order-primary);
            background: var(--order-primary-light);
        }
        
        .customer-avatar {
            transition: all 0.3s ease;
        }
        
        .customer-card:hover .customer-avatar {
            transform: scale(1.1);
        }
        
        /* بطاقات المنتجات */
        .product-card {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            border-color: var(--order-primary);
        }
        
        .product-image {
            width: 100%;
            height: 120px;
            object-fit: contain;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        
        .product-quantity-control {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: auto;
            padding-top: 10px;
        }
        
        .quantity-btn {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--order-primary-light);
            color: var(--order-primary);
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .quantity-btn:hover {
            background: var(--order-primary);
            color: white;
        }
        
        .quantity-display {
            min-width: 40px;
            text-align: center;
            font-weight: 600;
            margin: 0 5px;
        }
        
        /* جدول الطلبية */
        .invoice-item {
            transition: all 0.3s ease;
        }
        
        .invoice-item:hover {
            background: #f8f9fa;
        }
        
        .input-group-quantity {
            max-width: 120px;
        }
        
        .quantity-input {
            text-align: center;
            font-weight: 600;
        }
        
        .remove-item {
            transition: all 0.2s ease;
        }
        
        .remove-item:hover {
            transform: scale(1.1);
            color: var(--order-danger) !important;
        }
        
        /* ملخص الطلبية */
        .invoice-summary {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 15px;
        }
        
        .summary-total {
            font-weight: 700;
            font-size: 18px;
            color: var(--order-primary);
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #dee2e6;
        }
        
        /* الزر العائم */
        .floating-order-btn {
            position: fixed;
            bottom: 30px;
            left: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--order-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 5px 20px rgba(25, 135, 84, 0.3);
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 1000;
            opacity: 0;
            transform: translateY(20px);
        }
        
        .floating-order-btn.show {
            opacity: 1;
            transform: translateY(0);
        }
        
        .floating-order-btn:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 8px 25px rgba(25, 135, 84, 0.4);
        }
        
        .order-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            background: var(--order-danger);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }
        
        /* تأثيرات إضافية */
        .category-card {
            padding: 15px;
            border-radius: 8px;
            background: #f8f9fa;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 15px;
            border: 1px solid #e9ecef;
        }
        
        .category-card:hover, .category-card.active {
            background: var(--order-primary-light);
            color: var(--order-primary);
            border-color: var(--order-primary);
        }
        
        .category-card h6 {
            margin-bottom: 0;
            font-weight: 600;
        }
        
        /* مودال المنتج */
        .product-image-container {
            border: 1px solid #eee;
            border-radius: 10px;
            overflow: hidden;
        }
        
        @media (max-width: 768px) {
            .order-nav-tabs .nav-link {
                padding: 10px 15px;
                font-size: 14px;
            }
            
            .floating-order-btn {
                width: 50px;
                height: 50px;
                font-size: 20px;
                bottom: 20px;
                left: 20px;
            }
        }
    </style>

    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="order-step-container">
                        <h4 class="card-title mb-4">
                            <i class="fas fa-cart-plus me-2"></i>إنشاء طلبية جديدة
                        </h4>

                        <!-- خطوات الطلبية -->
                        <ul class="nav order-nav-tabs mb-4">
                            <li class="nav-item">
                                <a class="nav-link active" id="order-step1-tab" data-bs-toggle="tab" href="#order-step1">
                                    <i class="fas fa-user"></i> الزبون
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="order-step2-tab" data-bs-toggle="tab" href="#order-step2">
                                    <i class="fas fa-boxes"></i> المنتجات
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="order-step3-tab" data-bs-toggle="tab" href="#order-step3">
                                    <i class="fas fa-file-invoice"></i> تفاصيل الطلبية
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <!-- الخطوة 1: اختيار الزبون -->
                            <div class="tab-pane fade show active" id="order-step1">
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- نموذج البحث -->
                                        <form method="GET" action="{{ route('order.index') }}" class="mb-4">
                                            <div class="input-group shadow-sm rounded-pill overflow-hidden">
                                                <input type="text" name="customer_query"
                                                    class="form-control form-control-lg border-0"
                                                    placeholder="ابحث عن زبون بالاسم أو رقم الهاتف..."
                                                    value="{{ request('customer_query') }}">
                                                <button class="btn btn-success px-4" type="submit">
                                                    <i class="fas fa-search me-2"></i> بحث
                                                </button>
                                                @if (request('customer_query'))
                                                    <a href="{{ route('order.index') }}"
                                                        class="btn btn-outline-secondary">
                                                        <i class="fas fa-times me-2"></i>إعادة تعيين
                                                    </a>
                                                @endif
                                            </div>
                                        </form>

                                        <!-- عرض الزبائن -->
                                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4" id="customers-list">
                                            @forelse($customers as $cust)
                                                <div class="col">
                                                    <div class="customer-card card h-100"
                                                        onclick="selectOrderCustomer({{ $cust->id }}, '{{ $cust->name }}', '{{ $cust->mobile_no }}', this)">
                                                        <div class="card-body text-center py-4">
                                                            <div class="customer-avatar mb-3 mx-auto rounded-circle d-flex align-items-center justify-content-center"
                                                                style="width: 60px; height: 60px; background: rgba(25, 135, 84, 0.1);">
                                                                <i class="fas fa-user text-success"
                                                                    style="font-size: 24px;"></i>
                                                            </div>
                                                            <h5 class="card-title text-dark mb-1">{{ $cust->name }}</h5>
                                                            <p class="card-text text-muted mb-2">
                                                                <i class="fas fa-phone-alt me-2"></i>{{ $cust->mobile_no }}
                                                            </p>
                                                            <span class="badge bg-success-light text-success small">اختر هذا الزبون</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="col-12">
                                                    <div class="text-center p-5 border rounded bg-light">
                                                        <i class="fas fa-user-slash fa-2x text-muted mb-3"></i>
                                                        <h5 class="text-muted">لا يوجد زبائن مطابقة للبحث</h5>
                                                        <p class="text-muted small">حاول استخدام كلمات بحث مختلفة</p>
                                                    </div>
                                                </div>
                                            @endforelse
                                        </div>

                                        <div class="text-center mt-4">
                                            <button class="btn btn-success btn-next px-4" onclick="nextOrderStep(2)">
                                                <i class="fas fa-arrow-left me-2"></i> التالي إلى المنتجات
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- الخطوة 2: اختيار المنتجات -->
                            <div class="tab-pane fade" id="order-step2">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5 class="mb-3"><i class="fas fa-tags me-2"></i>اختر الفئة</h5>
                                        <div class="row mb-4">
                                            @foreach ($categories as $cat)
                                                <div class="col-md-2 col-4">
                                                    <div class="category-card"
                                                        onclick="showOrderCategoryProducts({{ $cat->id }}, this)">
                                                        <h6>{{ $cat->name }}</h6>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="search-box mb-4">
                                            <div class="position-relative">
                                                <input type="text" id="order_product_search" class="form-control ps-5"
                                                    placeholder="ابحث عن منتج...">
                                                <i class="fas fa-search position-absolute" style="top: 12px; left: 15px;"></i>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="mb-0"><i class="fas fa-box-open me-2"></i>المنتجات</h5>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-outline-secondary btn-grid active"
                                                    data-cols="6">6</button>
                                                <button type="button" class="btn btn-outline-secondary btn-grid"
                                                    data-cols="4">4</button>
                                                <button type="button" class="btn btn-outline-secondary btn-grid"
                                                    data-cols="3">3</button>
                                            </div>
                                        </div>
                                        
                                        <div class="row g-3" id="order-products-list">
                                            @foreach ($products as $product)
                                                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6 order-product-item grid-col"
                                                    data-category-id="{{ $product->category_id }}"
                                                    style="display: none;">
                                                    <div class="product-card">
                                                        @if ($product->image_url)
                                                            <img src="{{ asset($product->image_url) }}"
                                                                class="product-image" style="cursor: pointer;"
                                                                onclick="showOrderProductDetailsAndOpenModal(
                                                                    '{{ $product->id }}',
                                                                    '{{ $product->name }}',
                                                                    '{{ $product->category->name ?? 'غير مصنف' }}',
                                                                    '{{ $product->unit->name ?? 'غير محدد' }}',
                                                                    '{{ $product->price }}',
                                                                    '{{ $product->quantity ?? 0 }}',
                                                                    `{!! addslashes($product->descr ?? 'لا يوجد وصف') !!}`,
                                                                    '{{ $product->image_url ? asset($product->image_url) : asset('upload/no_image.jpg') }}',
                                                                    '{{ $product->special_price }}',
                                                                    '{{ $product->capacity }}',
                                                                    '{{ $product->weight }}',
                                                                    '{{ $product->unique_code }}'
                                                                )">
                                                        @else
                                                            <img src="{{ asset('upload/no_image.jpg') }}"
                                                                class="product-image" style="cursor: pointer;"
                                                                onclick="showOrderProductDetailsAndOpenModal(
                                                                    '{{ $product->id }}',
                                                                    '{{ $product->name }}',
                                                                    '{{ $product->category->name ?? 'غير مصنف' }}',
                                                                    '{{ $product->unit->name ?? 'غير محدد' }}',
                                                                    '{{ $product->price }}',
                                                                    '{{ $product->quantity ?? 0 }}',
                                                                    `{!! addslashes($product->descr ?? 'لا يوجد وصف') !!}`,
                                                                    '{{ $product->image_url ? asset($product->image_url) : asset('upload/no_image.jpg') }}',
                                                                    '{{ $product->special_price }}',
                                                                    '{{ $product->capacity }}',
                                                                    '{{ $product->weight }}',
                                                                    '{{ $product->unique_code }}'
                                                                )">
                                                        @endif
                                                        <h6 class="mt-2 mb-1">{{ $product->name }}</h6>
                                                        <p class="text-success mb-2">
                                                            <strong>{{ $product->price }} شيكل</strong>
                                                            @if($product->special_price)
                                                                <small class="text-danger text-decoration-line-through ms-2">{{ $product->special_price }} شيكل</small>
                                                            @endif
                                                        </p>
                                                        <div class="product-quantity-control">
                                                            <div class="quantity-btn"
                                                                onclick="event.stopPropagation(); changeOrderProductQuantity({{ $product->id }}, -1)">
                                                                <i class="fas fa-minus"></i>
                                                            </div>
                                                            <div class="quantity-display"
                                                                id="order-product-qty-{{ $product->id }}">0</div>
                                                            <div class="quantity-btn"
                                                                onclick="event.stopPropagation(); changeOrderProductQuantity({{ $product->id }}, 1)">
                                                                <i class="fas fa-plus"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        
                                        <!-- Modal عرض تفاصيل المنتج للطلب -->
                                        <div class="modal fade" id="orderProductModal" tabindex="-1"
                                            aria-labelledby="orderProductModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-light">
                                                        <h5 class="modal-title fw-bold" id="orderProductModalLabel">تفاصيل المنتج</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-5 mb-3 mb-md-0">
                                                                <div class="product-image-container rounded-3 p-3 text-center"
                                                                    style="height: 300px; background: #f8f9fa;">
                                                                    <img id="order-modal-product-image" src=""
                                                                        alt="صورة المنتج"
                                                                        class="img-fluid h-100 object-fit-contain">
                                                                </div>
                                                            </div>

                                                            <div class="col-md-7">
                                                                <ul class="list-group list-group-flush">
                                                                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                                                        <strong class="text-muted">الاسم:</strong>
                                                                        <span id="order-modal-product-name" class="fw-bold text-dark"></span>
                                                                    </li>
                                                                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                                                        <strong class="text-muted">الرمز الخاص:</strong>
                                                                        <span id="order-modal-unique-code" class="badge bg-info-light text-info"></span>
                                                                    </li>
                                                                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                                                        <strong class="text-muted">الفئة:</strong>
                                                                        <span id="order-modal-category" class="badge bg-success-light text-success"></span>
                                                                    </li>
                                                                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                                                        <strong class="text-muted">الوحدة:</strong>
                                                                        <span id="order-modal-unit" class="fw-bold"></span>
                                                                    </li>
                                                                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                                                        <strong class="text-muted">السعر:</strong>
                                                                        <span id="order-modal-price" class="fw-bold text-success"></span>
                                                                    </li>
                                                                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                                                        <strong class="text-muted">السعر الخاص:</strong>
                                                                        <span id="order-modal-special-price" class="fw-bold text-danger"></span>
                                                                    </li>
                                                                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                                                        <strong class="text-muted">الكمية المتاحة:</strong>
                                                                        <span id="order-modal-quantity" class="badge bg-success-light text-success"></span>
                                                                    </li>
                                                                    <li class="list-group-item py-3">
                                                                        <strong class="text-muted d-block mb-2">الوصف:</strong>
                                                                        <div id="order-modal-descr" class="p-3 bg-light rounded-2 text-dark"></div>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top-0">
                                                        <button type="button" class="btn btn-outline-secondary px-4"
                                                            data-bs-dismiss="modal">
                                                            <i class="fas fa-times me-2"></i>إغلاق
                                                        </button>
                                                        <button type="button" class="btn btn-success px-4"
                                                            onclick="addOrderProductToOrderFromModal()">
                                                            <i class="fas fa-cart-plus me-2"></i>إضافة إلى الطلبية
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-center mt-4">
                                            <button class="btn btn-secondary btn-prev me-2 px-4" onclick="prevOrderStep(1)">
                                                <i class="fas fa-arrow-right me-2"></i> السابق
                                            </button>
                                            <button class="btn btn-success btn-next px-4" onclick="nextOrderStep(3)">
                                                <i class="fas fa-arrow-left me-2"></i> التالي إلى الطلبية
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- الخطوة 3: تفاصيل الطلبية -->
                            <div class="tab-pane fade" id="order-step3">
                                <form method="post" action="{{ route('order.store') }}" id="order-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-body">
                                                    <h5 class="card-title mb-4">
                                                        <i class="fas fa-file-invoice me-2"></i>تفاصيل الطلبية
                                                    </h5>

                                                    <input type="hidden" name="customer_id" id="selected_order_customer_id">
                                                    <input type="hidden" name="date" value="{{ date('Y-m-d') }}">
                                                    <input type="hidden" name="order_no" value="{{ $order_no }}">

                                                    <div class="mb-4">
                                                        <label class="form-label">الزبون</label>
                                                        <input type="text" class="form-control bg-light"
                                                            id="selected_order_customer_name" readonly>
                                                    </div>

                                                    <div class="table-responsive">
                                                        <table class="table">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th width="35%">المنتج</th>
                                                                    <th width="20%">الكمية</th>
                                                                    <th width="20%">السعر</th>
                                                                    <th width="20%">الإجمالي</th>
                                                                    <th width="5%"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="order-items">
                                                                <!-- سيتم إضافة العناصر هنا -->
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                    <div class="row mt-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label">الخصم</label>
                                                            <div class="input-group">
                                                                <input type="number" name="discount_amount"
                                                                    id="order_discount_amount" class="form-control"
                                                                    placeholder="مبلغ الخصم" value="0"
                                                                    min="0">
                                                                <span class="input-group-text">شيكل</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">طريقة الدفع</label>
                                                            <select name="payment_method" class="form-select">
                                                                <option value="cash">نقدي</option>
                                                                <option value="credit">آجل</option>
                                                                <option value="bank">تحويل بنكي</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="invoice-summary mt-4">
                                                        <div class="summary-row">
                                                            <span>المجموع:</span>
                                                            <span id="order-subtotal-amount">0.00 شيكل</span>
                                                        </div>
                                                        <div class="summary-row">
                                                            <span>الخصم:</span>
                                                            <span id="order-discount-display">0.00 شيكل</span>
                                                        </div>
                                                        <hr>
                                                        <div class="summary-row summary-total">
                                                            <span>الإجمالي النهائي:</span>
                                                            <span id="order-total-amount">0.00 شيكل</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-body">
                                                    <h5 class="card-title mb-4">
                                                        <i class="fas fa-info-circle me-2"></i>تفاصيل إضافية
                                                    </h5>

                                                    <div class="mb-3">
                                                        <label class="form-label">تاريخ التوصيل</label>
                                                        <input type="date" name="delivery_date" class="form-control" 
                                                               min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}">
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">ملاحظات</label>
                                                        <textarea name="description" class="form-control" rows="3" placeholder="أي ملاحظات خاصة بالطلبية..."></textarea>
                                                    </div>

                                                    <input type="hidden" name="total_amount" id="order_estimated_amount"
                                                        value="0">

                                                    <div class="d-grid gap-2 mt-4">
                                                        <button type="submit" class="btn btn-success btn-lg py-3">
                                                            <i class="fas fa-check-circle me-2"></i> حفظ الطلبية
                                                        </button>
                                                        <button type="button" class="btn btn-secondary py-3"
                                                            onclick="prevOrderStep(2)">
                                                            <i class="fas fa-arrow-right me-2"></i> رجوع
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- الزر العائم للطلب -->
    <button class="floating-order-btn" id="floating-order-btn" onclick="goToOrder()">
        <i class="fas fa-shopping-cart"></i>
        <span class="order-badge" id="order-badge">0</span>
    </button>

    <script>
        // تخزين كميات المنتجات للطلب
        let orderProductQuantities = {};
        let orderItems = {};
        let selectedProductId = null;

        // عرض تفاصيل المنتج في المودال للطلب
        function showOrderProductDetailsAndOpenModal(productId, name, category, unit, price, quantity, descr, imageUrl,
            specialPrice = null, capacity = null, weight = null, uniqueCode = null) {

            selectedProductId = productId;
            
            // تعبئة بيانات المنتج الأساسية
            document.getElementById('order-modal-product-name').textContent = name;
            document.getElementById('order-modal-category').textContent = category;
            document.getElementById('order-modal-unit').textContent = unit;
            document.getElementById('order-modal-price').textContent = price + ' شيكل';
            document.getElementById('order-modal-quantity').textContent = quantity;
            document.getElementById('order-modal-descr').innerHTML = descr;
            document.getElementById('order-modal-product-image').src = imageUrl;

            // تعبئة الحقول الجديدة
            document.getElementById('order-modal-special-price').textContent = specialPrice ? specialPrice + ' شيكل' : 'غير محدد';
            document.getElementById('order-modal-unique-code').textContent = uniqueCode || 'غير محدد';

            // افتح الـ Modal
            var modal = new bootstrap.Modal(document.getElementById('orderProductModal'));
            modal.show();
        }

        // إضافة المنتج إلى الطلبية من الـ Modal
        function addOrderProductToOrderFromModal() {
            if (selectedProductId) {
                // زيادة الكمية بمقدار 1
                changeOrderProductQuantity(parseInt(selectedProductId), 1);

                // إغلاق الـ Modal
                var modal = bootstrap.Modal.getInstance(document.getElementById('orderProductModal'));
                modal.hide();

                // عرض رسالة تأكيد
                Toastify({
                    text: "تمت إضافة المنتج إلى الطلبية",
                    duration: 2000,
                    gravity: "top",
                    position: "left",
                    backgroundColor: "#198754",
                }).showToast();
            }
        }

        // اختيار الزبون للطلب
        function selectOrderCustomer(id, name, phone, element) {
            $('#selected_order_customer_id').val(id);
            $('#selected_order_customer_name').val(name + ' - ' + phone);

            // إزالة التحديد من جميع البطاقات
            $('.customer-card').removeClass('selected');

            // إضافة التحديد للبطاقة المختارة
            if (element) {
                $(element).addClass('selected');
            }

            // إظهار رسالة تأكيد
            Toastify({
                text: "تم اختيار الزبون: " + name,
                duration: 3000,
                gravity: "top",
                position: "left",
                backgroundColor: "#198754",
            }).showToast();
        }

        // التنقل بين خطوات الطلب
        function nextOrderStep(step) {
            // التحقق من اختيار زبون قبل الانتقال للخطوة 2
            if (step === 2 && !$('#selected_order_customer_id').val()) {
                Swal.fire({
                    title: 'تحذير',
                    text: 'يجب اختيار زبون قبل المتابعة',
                    icon: 'warning',
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#198754'
                });
                return;
            }

            // التحقق من وجود منتجات قبل الانتقال للخطوة 3
            if (step === 3 && Object.keys(orderItems).length === 0) {
                Swal.fire({
                    title: 'تحذير',
                    text: 'يجب إضافة منتجات على الأقل قبل المتابعة',
                    icon: 'warning',
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#198754'
                });
                return;
            }

            $('.order-nav-tabs .nav-link').removeClass('active');
            $('.tab-pane').removeClass('show active');

            $('#order-step' + step + '-tab').addClass('active');
            $('#order-step' + step).addClass('show active');

            // التمرير لأعلى الصفحة
            $('html, body').animate({
                scrollTop: 0
            }, 'slow');
        }

        function prevOrderStep(step) {
            $('.order-nav-tabs .nav-link').removeClass('active');
            $('.tab-pane').removeClass('show active');

            $('#order-step' + step + '-tab').addClass('active');
            $('#order-step' + step).addClass('show active');

            // التمرير لأعلى الصفحة
            $('html, body').animate({
                scrollTop: 0
            }, 'slow');
        }

        // عرض منتجات الفئة المحددة للطلب
        function showOrderCategoryProducts(categoryId, element) {
            $('.order-product-item').hide();
            $('.order-product-item[data-category-id="' + categoryId + '"]').show();

            // إزالة التحديد من جميع فئات البطاقات
            $('.category-card').removeClass('active');

            // إضافة التحديد للفئة المختارة
            if (element) {
                $(element).addClass('active');
            }
        }

        // تغيير كمية المنتج في الطلب
        function changeOrderProductQuantity(productId, change) {
            const currentQty = orderProductQuantities[productId] || 0;
            const newQty = Math.max(0, currentQty + change);

            orderProductQuantities[productId] = newQty;
            document.getElementById('order-product-qty-' + productId).textContent = newQty;

            // تحديث الطلبية
            if (newQty > 0) {
                addToOrder(productId, newQty);
            } else {
                removeFromOrder(productId);
            }

            updateOrderFloatingButton();
        }

        // إضافة منتج للطلبية
        function addToOrder(productId, quantity) {
            // الحصول على معلومات المنتج
            let product = null;
            @foreach ($products as $product)
                if ({{ $product->id }} == productId) {
                    product = {
                        id: {{ $product->id }},
                        name: '{{ $product->name }}',
                        category_id: {{ $product->category_id }},
                        price: parseFloat({{ $product->price }}),
                        quantity: quantity
                    };
                }
            @endforeach

            if (!product) return;

            // التحقق من وجود المنتج في الطلبية
            if (orderItems[productId]) {
                orderItems[productId].quantity = quantity;
            } else {
                orderItems[productId] = product;
            }

            rebuildOrderTable();
            updateOrderTotal();
        }

        // إزالة منتج من الطلبية
        function removeFromOrder(productId) {
            delete orderItems[productId];
            rebuildOrderTable();
            updateOrderTotal();
            updateOrderFloatingButton();
        }

        // إعادة بناء جدول الطلبية
        function rebuildOrderTable() {
            const tbody = document.getElementById('order-items');
            tbody.innerHTML = '';

            Object.values(orderItems).forEach(function(item) {
                const tr = document.createElement('tr');
                tr.className = 'invoice-item';
                tr.setAttribute('data-product-id', item.id);

                const total = (item.quantity * item.price).toFixed(2);

                tr.innerHTML = `
                    <input type="hidden" name="product_id[]" value="${item.id}">
                    <input type="hidden" name="category_id[]" value="${item.category_id}">
                    <td>${item.name}</td>
                    <td>
                        <div class="input-group input-group-quantity">
                            <button type="button" class="btn quantity-minus" onclick="updateOrderQuantity(${item.id}, -1)">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" min="1" class="form-control quantity-input quantity" 
                                   name="quantity[]" value="${item.quantity}" readonly>
                            <button type="button" class="btn quantity-plus" onclick="updateOrderQuantity(${item.id}, 1)">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </td>
                    <td>
                        <input type="number" step="0.01" class="form-control form-control-sm price" 
                               name="unit_price[]" value="${item.price}" 
                               onchange="updateOrderItemPrice(${item.id}, this.value)">
                    </td>
                    <td class="item-total">${total}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger remove-item" onclick="removeOrderItemFromOrder(${item.id})">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                `;

                tbody.appendChild(tr);
            });
        }

        // تحديث كمية المنتج في الطلبية
        function updateOrderQuantity(productId, change) {
            if (orderItems[productId]) {
                const newQty = Math.max(1, orderItems[productId].quantity + change);
                orderItems[productId].quantity = newQty;
                orderProductQuantities[productId] = newQty;

                // تحديث العرض في صفحة المنتجات
                document.getElementById('order-product-qty-' + productId).textContent = newQty;

                // إعادة بناء الجدول
                rebuildOrderTable();
                updateOrderTotal();
                updateOrderFloatingButton();
            }
        }

        // تحديث سعر المنتج في الطلبية
        function updateOrderItemPrice(productId, newPrice) {
            if (orderItems[productId]) {
                orderItems[productId].price = parseFloat(newPrice) || orderItems[productId].price;
                rebuildOrderTable();
                updateOrderTotal();
            }
        }

        // إزالة عنصر من الطلبية
        function removeOrderItemFromOrder(productId) {
            delete orderItems[productId];
            orderProductQuantities[productId] = 0;

            // تحديث العرض في صفحة المنتجات
            document.getElementById('order-product-qty-' + productId).textContent = 0;

            rebuildOrderTable();
            updateOrderTotal();
            updateOrderFloatingButton();

            Toastify({
                text: "تم حذف المنتج من الطلبية",
                duration: 2000,
                gravity: "top",
                position: "left",
                backgroundColor: "#dc3545",
            }).showToast();
        }

        // تحديث الطلبية الكلية
        function updateOrderTotal() {
            let subtotal = 0;

            Object.values(orderItems).forEach(function(item) {
                subtotal += item.quantity * item.price;
            });

            const discount = parseFloat($('#order_discount_amount').val()) || 0;
            const total = subtotal - discount;

            $('#order-subtotal-amount').text(subtotal.toFixed(2) + ' شيكل');
            $('#order-discount-display').text(discount.toFixed(2) + ' شيكل');
            $('#order-total-amount').text(total.toFixed(2) + ' شيكل');
            $('#order_estimated_amount').val(total.toFixed(2));
        }

        // تحديث الزر العائم للطلب
        function updateOrderFloatingButton() {
            const itemCount = Object.keys(orderItems).length;
            const badge = document.getElementById('order-badge');
            const button = document.getElementById('floating-order-btn');

            badge.textContent = itemCount;

            if (itemCount > 0) {
                button.classList.add('show');
            } else {
                button.classList.remove('show');
            }
        }

        // الانتقال للطلب
        function goToOrder() {
            nextOrderStep(3);
        }

        // أحداث إضافية
        $(document).on('input', '#order_discount_amount', function() {
            updateOrderTotal();
        });

        // البحث عن المنتجات
        $(document).on('input', '#order_product_search', function() {
            const searchTerm = $(this).val().toLowerCase();
            
            $('.order-product-item').each(function() {
                const productName = $(this).find('h6').text().toLowerCase();
                if (productName.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // تغيير عدد أعمدة عرض المنتجات
        $(document).on('click', '.btn-grid', function() {
            const cols = $(this).data('cols');
            $('.btn-grid').removeClass('active');
            $(this).addClass('active');
            
            $('.grid-col').removeClass('col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6');
            
            if (cols === 6) {
                $('.grid-col').addClass('col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6');
            } else if (cols === 4) {
                $('.grid-col').addClass('col-xl-3 col-lg-4 col-md-6 col-sm-6 col-6');
            } else if (cols === 3) {
                $('.grid-col').addClass('col-xl-4 col-lg-6 col-md-6 col-sm-6 col-6');
            }
        });

        // التحقق قبل إرسال الطلبية
        $('#order-form').on('submit', function(e) {
            e.preventDefault();

            // التحقق من وجود منتجات في الطلبية
            if (Object.keys(orderItems).length === 0) {
                Swal.fire({
                    title: 'خطأ',
                    text: 'يجب إضافة منتجات على الأقل',
                    icon: 'error',
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#198754'
                });
                return;
            }

            // التحقق من اختيار زبون
            if (!$('#selected_order_customer_id').val()) {
                Swal.fire({
                    title: 'خطأ',
                    text: 'يجب اختيار زبون',
                    icon: 'error',
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#198754'
                });
                return;
            }

            // التحقق من صحة الأسعار والكميات
            let isValid = true;
            $('.price').each(function() {
                if ($(this).val() === '' || parseFloat($(this).val()) <= 0) {
                    isValid = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            if (!isValid) {
                Swal.fire({
                    title: 'خطأ',
                    text: 'يجب إدخال أسعار صحيحة لجميع المنتجات',
                    icon: 'error',
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#198754'
                });
                return;
            }

            // إذا كان كل شيء صحيحاً، قم بإرسال النموذج
            this.submit();
        });

        // إعادة تعيين النموذج
        function resetOrderForm() {
            // إعادة تعيين البيانات
            orderItems = {};
            orderProductQuantities = {};

            // إعادة تعيين كميات المنتجات
            @foreach ($products as $product)
                orderProductQuantities[{{ $product->id }}] = 0;
                document.getElementById('order-product-qty-{{ $product->id }}').textContent = 0;
            @endforeach

            // مسح الطلبية
            document.getElementById('order-items').innerHTML = '';

            // تحديث المجاميع
            updateOrderTotal();
            updateOrderFloatingButton();
        }

        // عند التحميل: تهيئة كميات المنتجات
        document.addEventListener('DOMContentLoaded', function() {
            @foreach ($products as $product)
                orderProductQuantities[{{ $product->id }}] = 0;
            @endforeach
            
            // عرض جميع المنتجات افتراضياً
            $('.order-product-item').show();
            
            // تنشيط زر الشبكة الافتراضي
            $('.btn-grid[data-cols="6"]').addClass('active');
        });
    </script>
@endsection