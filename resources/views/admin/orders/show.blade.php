@extends('layouts.vertical', ['title' => 'تفاصيل الطلب'])

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">تفاصيل الطلب #{{ $order->id }}</h4>
                    <div>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                            <iconify-icon icon="solar:arrow-left-bold-duotone"></iconify-icon>
                            العودة للقائمة
                        </a>
                        <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-primary">
                            <iconify-icon icon="solar:pen-bold-duotone"></iconify-icon>
                            تعديل
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Customer Information -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">معلومات الزبون</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>الاسم:</strong> {{ $order->customer_name }}</p>
                        <p><strong>العنوان:</strong> {{ $order->customer_address }}</p>
                        <p><strong>رقم الهاتف:</strong> {{ $order->customer_phone }}</p>
                    </div>
                    <div class="col-md-6">
                        @if($order->customer_phone_2)
                            <p><strong>رقم الهاتف الثاني:</strong> {{ $order->customer_phone_2 }}</p>
                        @endif
                        @if($order->customer_social_media)
                            <p><strong>رابط السوشال ميديا:</strong> <a href="{{ $order->customer_social_media }}" target="_blank">{{ $order->customer_social_media }}</a></p>
                        @endif
                        @if($order->customer_notes)
                            <p><strong>ملاحظات:</strong> {{ $order->customer_notes }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Items -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">المنتجات</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>المنتج</th>
                                <th>الكمية</th>
                                <th>سعر الجملة</th>
                                <th>سعر البيع</th>
                                <th>الربح للوحدة</th>
                                <th>الإجمالي</th>
                                <th>الربح الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderItems as $item)
                                <tr>
                                    <td>
                                        {{ $item->product->name }}
                                        @if(!$item->product_id)
                                            <span class="badge bg-warning-subtle text-warning ms-2">محذوف</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ format_currency($item->wholesale_price) }}</td>
                                    <td>{{ format_currency($item->customer_price) }}</td>
                                    <td class="text-success">{{ format_currency($item->profit_per_item) }}</td>
                                    <td>{{ format_currency($item->subtotal) }}</td>
                                    <td class="text-success fw-bold">{{ format_currency($item->profit_subtotal) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Summary and Actions -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">ملخص الطلب</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>إجمالي المبلغ:</span>
                    <strong>{{ format_currency($order->total_amount) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>إجمالي الربح:</span>
                    <strong class="text-success">{{ format_currency($order->total_profit) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>عمولة التجهيز:</span>
                    <strong class="text-warning">{{ format_currency($order->preparation_commission) }}</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-0">
                    <span class="fw-bold">الربح الصافي:</span>
                    <strong class="text-primary fs-18">{{ format_currency($order->final_profit) }}</strong>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">معلومات الطلب</h5>
            </div>
            <div class="card-body">
                <p><strong>الحالة:</strong> 
                    <span class="badge {{ $order->status->badgeClass() }}">
                        {{ $order->status->label() }}
                    </span>
                </p>
                <p><strong>المندوب:</strong> 
                    @if($order->representative)
                        {{ $order->representative->name }}
                    @elseif($order->createdBy)
                        {{ $order->createdBy->name }}
                    @else
                        -
                    @endif
                </p>
                <p><strong>تاريخ الإنشاء:</strong> {{ $order->created_at->format('Y-m-d H:i') }}</p>
                @if($order->completed_at)
                    <p><strong>تاريخ الإكمال:</strong> {{ $order->completed_at->format('Y-m-d H:i') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Change Status -->
@if($order->status !== \App\Enums\OrderStatus::COMPLETED)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">تغيير حالة الطلب</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <select name="status" class="form-select" required>
                                    @foreach(\App\Enums\OrderStatus::cases() as $status)
                                        <option value="{{ $status->value }}" {{ $order->status === $status ? 'selected' : '' }}>
                                            {{ $status->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary w-100">تحديث الحالة</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

