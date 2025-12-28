@extends('layouts.vertical', ['title' => 'تعديل الطلب'])

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">تعديل الطلب #{{ $order->id }}</h4>
                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-secondary">
                        <iconify-icon icon="solar:arrow-left-bold-duotone"></iconify-icon>
                        العودة
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('admin.orders.update', $order) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">معلومات الزبون</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">اسم الزبون <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" class="form-control" value="{{ $order->customer_name }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                            <input type="text" name="customer_phone" class="form-control" value="{{ $order->customer_phone }}" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">عنوان الزبون <span class="text-danger">*</span></label>
                            <textarea name="customer_address" class="form-control" rows="3" required>{{ $order->customer_address }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">رقم الهاتف الثاني</label>
                            <input type="text" name="customer_phone_2" class="form-control" value="{{ $order->customer_phone_2 }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">رابط السوشال ميديا</label>
                            <input type="url" name="customer_social_media" class="form-control" value="{{ $order->customer_social_media }}">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">ملاحظات</label>
                            <textarea name="customer_notes" class="form-control" rows="3">{{ $order->customer_notes }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary">
                        <iconify-icon icon="solar:check-circle-bold-duotone"></iconify-icon>
                        حفظ التعديلات
                    </button>
                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-secondary">إلغاء</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

