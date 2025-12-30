@extends('layouts.representative', ['title' => 'تفاصيل الطلب'])

@section('content')
<!-- Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h4 class="card-title mb-1">تفاصيل الطلب #{{ $order->id }}</h4>
                        <span class="badge {{ $order->status->badgeClass() }} fs-14">
                            {{ $order->status->label() }}
                        </span>
                    </div>
                    <a href="{{ route('representative.orders.index') }}" class="btn btn-secondary">
                        <iconify-icon icon="solar:arrow-right-bold-duotone"></iconify-icon>
                        العودة للقائمة
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <!-- Right Column -->
    <div class="col-lg-8 order-2 order-lg-1">
        <!-- Products -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:cart-large-2-bold-duotone" class="text-primary fs-20"></iconify-icon>
                    المنتجات
                </h5>
            </div>
            <div class="card-body">
                <!-- Desktop View: Table -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>المنتج</th>
                                <th class="text-center">الكمية</th>
                                <th class="text-end">سعر الجملة</th>
                                <th class="text-end">سعر البيع</th>
                                <th class="text-end">الربح/وحدة</th>
                                <th class="text-end">الإجمالي</th>
                                <th class="text-end">ربح الصنف</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderItems as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            @if($item->product && $item->product->images && $item->product->images->count() > 0)
                                                <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}"
                                                     alt="{{ $item->product->name ?? 'منتج' }}"
                                                     class="rounded"
                                                     style="width: 60px; height: 60px; object-fit: cover; border: 1px solid #dee2e6;">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                     style="width: 60px; height: 60px;">
                                                    <iconify-icon icon="solar:box-bold-duotone" class="text-muted fs-24"></iconify-icon>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-medium">{{ $item->product->name ?? 'منتج محذوف' }}</div>
                                                @if(!$item->product_id || !$item->product)
                                                    <span class="badge bg-warning-subtle text-warning">محذوف</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary-subtle text-primary">{{ $item->quantity }}</span>
                                    </td>
                                    <td class="text-end">{{ format_currency($item->wholesale_price) }}</td>
                                    <td class="text-end fw-medium">{{ format_currency($item->customer_price) }}</td>
                                    <td class="text-end text-success">{{ format_currency($item->profit_per_item) }}</td>
                                    <td class="text-end fw-bold">{{ format_currency($item->subtotal) }}</td>
                                    <td class="text-end text-success fw-bold">{{ format_currency($item->profit_subtotal) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile View: Cards -->
                <div class="d-md-none">
                    @foreach($order->orderItems as $item)
                        <div class="card mb-3 border">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-start gap-3 mb-3">
                                    @if($item->product && $item->product->images && $item->product->images->count() > 0)
                                        <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}"
                                             alt="{{ $item->product->name ?? 'منتج' }}"
                                             class="rounded"
                                             style="width: 80px; height: 80px; object-fit: cover; border: 1px solid #dee2e6; flex-shrink: 0;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                             style="width: 80px; height: 80px; flex-shrink: 0;">
                                            <iconify-icon icon="solar:box-bold-duotone" class="text-muted fs-32"></iconify-icon>
                                        </div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-start justify-content-between mb-2">
                                            <h6 class="mb-0">{{ $item->product->name ?? 'منتج محذوف' }}</h6>
                                            <span class="badge bg-primary">{{ $item->quantity }} × </span>
                                        </div>
                                        @if(!$item->product_id || !$item->product)
                                            <span class="badge bg-warning-subtle text-warning">محذوف</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="row g-2 small">
                                    <div class="col-6">
                                        <div class="p-2 bg-light rounded">
                                            <div class="text-muted small">سعر الجملة</div>
                                            <div class="fw-medium">{{ format_currency($item->wholesale_price) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-2 bg-light rounded">
                                            <div class="text-muted small">سعر البيع</div>
                                            <div class="fw-medium">{{ format_currency($item->customer_price) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-2 bg-success-subtle rounded">
                                            <div class="text-muted small">الربح/وحدة</div>
                                            <div class="fw-bold text-success">{{ format_currency($item->profit_per_item) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-2 bg-success-subtle rounded">
                                            <div class="text-muted small">ربح الصنف</div>
                                            <div class="fw-bold text-success">{{ format_currency($item->profit_subtotal) }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                                    <span class="text-muted">الإجمالي:</span>
                                    <strong class="fs-16">{{ format_currency($item->subtotal) }}</strong>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Left Column -->
    <div class="col-lg-4 order-1 order-lg-2">
        <!-- Customer Information -->
        <div class="card mb-4">
            <div class="card-header bg-primary-subtle">
                <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:user-bold-duotone" class="text-primary fs-20"></iconify-icon>
                    معلومات الزبون
                </h5>
            </div>
            <div class="card-body" dir="rtl">
                <!-- Desktop: Grid Layout | Mobile: Stack Layout -->
                <div class="row g-3">
                    <!-- الاسم -->
                    <div class="col-12 col-md-6">
                        <div class="mb-3 mb-md-0">
                            <div class="text-muted small mb-2 d-flex align-items-center gap-1">
                                <iconify-icon icon="solar:user-bold-duotone" class="text-primary"></iconify-icon>
                                الاسم
                            </div>
                            <div class="fw-bold fs-16">{{ $order->customer_name }}</div>
                        </div>
                    </div>

                    <!-- رقم الهاتف -->
                    <div class="col-12 col-md-6">
                        <div class="mb-3 mb-md-0">
                            <div class="text-muted small mb-2 d-flex align-items-center gap-1">
                                <iconify-icon icon="solar:phone-bold-duotone" class="text-success"></iconify-icon>
                                رقم الهاتف
                            </div>
                            <div class="fw-medium" dir="ltr" style="text-align: right;">
                                <a href="tel:{{ $order->customer_phone }}" class="text-decoration-none">
                                    {{ $order->customer_phone }}
                                </a>
                            </div>
                        </div>
                    </div>

                    @if($order->customer_phone_2)
                        <!-- رقم الهاتف الثاني -->
                        <div class="col-12 col-md-6">
                            <div class="mb-3 mb-md-0">
                                <div class="text-muted small mb-2 d-flex align-items-center gap-1">
                                    <iconify-icon icon="solar:phone-calling-bold-duotone" class="text-info"></iconify-icon>
                                    رقم الهاتف الثاني
                                </div>
                                <div class="fw-medium" dir="ltr" style="text-align: right;">
                                    <a href="tel:{{ $order->customer_phone_2 }}" class="text-decoration-none">
                                        {{ $order->customer_phone_2 }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <hr class="my-3">

                <!-- Desktop: Grid Layout | Mobile: Stack Layout -->
                <div class="row g-3">
                    <!-- العنوان -->
                    <div class="col-12 col-md-6">
                        <div class="mb-3 mb-md-0">
                            <div class="text-muted small mb-2 d-flex align-items-center gap-1">
                                <iconify-icon icon="solar:home-2-bold-duotone" class="text-warning"></iconify-icon>
                                العنوان
                            </div>
                            <div class="fw-medium">{{ $order->customer_address }}</div>
                        </div>
                    </div>

                    @if($order->governorate || $order->district)
                        <!-- الموقع -->
                        <div class="col-12 col-md-6">
                            <div class="mb-3 mb-md-0">
                                <div class="text-muted small mb-2 d-flex align-items-center gap-1">
                                    <iconify-icon icon="solar:map-point-bold-duotone" class="text-danger"></iconify-icon>
                                    الموقع
                                </div>
                                <div class="fw-medium">
                                    @if($order->governorate && $order->district)
                                        {{ $order->governorate->name }} - {{ $order->district->name }}
                                    @elseif($order->governorate)
                                        {{ $order->governorate->name }}
                                    @elseif($order->district)
                                        {{ $order->district->name }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                @if($order->customer_social_media || $order->customer_notes)
                    <hr class="my-3">
                @endif

                <!-- Desktop: Grid Layout | Mobile: Stack Layout -->
                <div class="row g-3">
                    @if($order->customer_social_media)
                        <!-- السوشال ميديا -->
                        <div class="col-12 col-md-6">
                            <div class="mb-3 mb-md-0">
                                <div class="text-muted small mb-2 d-flex align-items-center gap-1">
                                    <iconify-icon icon="solar:link-bold-duotone" class="text-primary"></iconify-icon>
                                    السوشال ميديا
                                </div>
                                <div>
                                    <a href="{{ $order->customer_social_media }}" target="_blank" class="text-decoration-none d-inline-flex align-items-center gap-1">
                                        <iconify-icon icon="solar:external-link-bold-duotone"></iconify-icon>
                                        <span>رابط الحساب</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($order->customer_notes)
                        <!-- الملاحظات -->
                        <div class="col-12 {{ $order->customer_social_media ? 'col-md-6' : '' }}">
                            <div class="mb-0">
                                <div class="text-muted small mb-2 d-flex align-items-center gap-1">
                                    <iconify-icon icon="solar:notes-bold-duotone" class="text-secondary"></iconify-icon>
                                    ملاحظات
                                </div>
                                <div class="fw-medium bg-light-subtle p-2 rounded" dir="rtl">{{ $order->customer_notes }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="card mb-4">
            <div class="card-header bg-info-subtle">
                <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:document-text-bold-duotone" class="text-info fs-20"></iconify-icon>
                    ملخص الطلب
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">إجمالي المنتجات:</span>
                    <strong>{{ format_currency($order->orderItems->sum('subtotal')) }}</strong>
                </div>

                @if($order->delivery_fee > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">
                            <iconify-icon icon="solar:delivery-bold-duotone" class="text-primary"></iconify-icon>
                            سعر التوصيل:
                        </span>
                        <strong>{{ format_currency($order->delivery_fee) }}</strong>
                    </div>
                @endif

                @if($order->gift_price > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">
                            <iconify-icon icon="solar:gift-bold-duotone" class="text-warning"></iconify-icon>
                            سعر الهدايا:
                        </span>
                        <strong>{{ format_currency($order->gift_price) }}</strong>
                    </div>

                    @if($order->gift)
                        <div class="ms-4 mb-2 small text-muted">
                            • {{ $order->gift->name }}
                        </div>
                    @endif

                    @if($order->giftBox)
                        <div class="ms-4 mb-2 small text-muted">
                            • {{ $order->giftBox->name }}
                        </div>
                    @endif
                @endif

                <hr class="my-3">

                <div class="d-flex justify-content-between mb-3">
                    <span class="fw-bold fs-16">المبلغ الكلي:</span>
                    <strong class="text-dark fs-18">{{ format_currency($order->total_amount) }}</strong>
                </div>

                <div class="bg-success-subtle p-3 rounded">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">إجمالي الربح:</span>
                        <span class="text-success fw-bold">{{ format_currency($order->total_profit) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">عمولة التجهيز:</span>
                        <span class="text-warning">{{ format_currency($order->preparation_commission) }}</span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between mb-0">
                        <span class="fw-bold">الربح الصافي:</span>
                        <strong class="text-primary fs-18">{{ format_currency($order->final_profit) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Info -->
        <div class="card">
            <div class="card-header bg-secondary-subtle">
                <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:info-circle-bold-duotone" class="text-secondary fs-20"></iconify-icon>
                    معلومات إضافية
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted small mb-1">رقم الطلب</div>
                    <div class="fw-bold">#{{ $order->id }}</div>
                </div>

                <div class="mb-3">
                    <div class="text-muted small mb-1">تاريخ الإنشاء</div>
                    <div class="fw-medium">
                        <iconify-icon icon="solar:calendar-bold-duotone" class="text-muted"></iconify-icon>
                        {{ $order->created_at->format('Y-m-d') }}
                        <span class="text-muted small">{{ $order->created_at->format('h:i A') }}</span>
                    </div>
                </div>

                @if($order->completed_at)
                    <div class="mb-3">
                        <div class="text-muted small mb-1">تاريخ الإكمال</div>
                        <div class="fw-medium">
                            <iconify-icon icon="solar:check-circle-bold-duotone" class="text-success"></iconify-icon>
                            {{ $order->completed_at->format('Y-m-d') }}
                            <span class="text-muted small">{{ $order->completed_at->format('h:i A') }}</span>
                        </div>
                    </div>
                @endif

                <div class="mb-3">
                    <div class="text-muted small mb-1">الحالة</div>
                    <span class="badge {{ $order->status->badgeClass() }} fs-14">
                        {{ $order->status->label() }}
                    </span>
                </div>

                <div class="mb-0">
                    <div class="text-muted small mb-1">عدد المنتجات</div>
                    <div class="fw-bold text-primary">{{ $order->orderItems->sum('quantity') }} منتج</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
