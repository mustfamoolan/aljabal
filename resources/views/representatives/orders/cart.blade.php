@extends('layouts.representative', ['title' => 'سلة الطلبات'])

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">سلة الطلبات</h4>
                    <a href="{{ route('representative.orders.create') }}" class="btn btn-primary">
                        <iconify-icon icon="solar:arrow-left-bold-duotone"></iconify-icon>
                        العودة للمنتجات
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">المنتجات المضافة</h5>
            </div>
            <div class="card-body">
                <div id="cart-items-container">
                    <div class="text-center py-5">
                        <iconify-icon icon="solar:cart-large-3-bold-duotone" class="fs-64 text-muted"></iconify-icon>
                        <p class="text-muted mt-3">السلة فارغة</p>
                        <a href="{{ route('representative.orders.create') }}" class="btn btn-primary">إضافة منتجات</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">ملخص الطلب</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>إجمالي المبلغ:</span>
                    <strong id="cart-total">0.00 د.ع</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>إجمالي الربح:</span>
                    <strong class="text-success" id="cart-profit">0.00 د.ع</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <span class="fw-bold">الربح الصافي:</span>
                    <strong class="text-primary fs-18" id="cart-net-profit">0.00 د.ع</strong>
                </div>
                <a href="{{ route('representative.orders.checkout') }}" class="btn btn-success w-100" id="checkout-btn" style="display: none;">
                    <iconify-icon icon="solar:check-circle-bold-duotone"></iconify-icon>
                    إكمال الطلب
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
@vite(['resources/js/components/order-cart.js'])
@endsection

