@extends('layouts.vertical', ['title' => 'تفاصيل الإشعار'])

@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">تفاصيل الإشعار</h4>
                <div class="d-flex gap-2">
                    @if($notification->read_at)
                        <form action="{{ route('notifications.unread', $notification) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-warning">
                                <iconify-icon icon="solar:bell-off-bold-duotone" class="me-1"></iconify-icon>
                                تحديد كغير مقروء
                            </button>
                        </form>
                    @else
                        <form action="{{ route('notifications.read', $notification) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">
                                <iconify-icon icon="solar:check-circle-bold-duotone" class="me-1"></iconify-icon>
                                تحديد كمقروء
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-light">
                        <iconify-icon icon="solar:arrow-left-bold-duotone" class="me-1"></iconify-icon>
                        رجوع
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">النوع</label>
                            <div>
                                @if($notification->type === 'low_stock')
                                    <span class="badge bg-danger-subtle text-danger fs-14 py-2 px-3">
                                        <iconify-icon icon="solar:box-bold-duotone" class="me-1"></iconify-icon>
                                        مخزون منخفض
                                    </span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary fs-14 py-2 px-3">
                                        <iconify-icon icon="solar:bell-bing-bold-duotone" class="me-1"></iconify-icon>
                                        {{ $notification->type }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">الحالة</label>
                            <div>
                                @if($notification->read_at)
                                    <span class="badge bg-success-subtle text-success fs-14 py-2 px-3">مقروء</span>
                                    <small class="text-muted ms-2">تم القراءة في: {{ $notification->read_at->format('Y-m-d H:i') }}</small>
                                @else
                                    <span class="badge bg-warning-subtle text-warning fs-14 py-2 px-3">غير مقروء</span>
                                @endif
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">العنوان</label>
                            <p class="fs-16 mb-0">{{ $notification->title }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">الوصف</label>
                            <p class="text-muted mb-0">{{ $notification->body }}</p>
                        </div>

                        @if($notification->data)
                            <div class="mb-3">
                                <label class="form-label fw-semibold">البيانات الإضافية</label>
                                <div class="card bg-light">
                                    <div class="card-body">
                                        @if(isset($notification->data['product_id']))
                                            <p class="mb-2">
                                                <strong>معرف المنتج:</strong> {{ $notification->data['product_id'] }}
                                            </p>
                                        @endif
                                        @if(isset($notification->data['product_name']))
                                            <p class="mb-2">
                                                <strong>اسم المنتج:</strong> {{ $notification->data['product_name'] }}
                                            </p>
                                        @endif
                                        @if(isset($notification->data['quantity']))
                                            <p class="mb-2">
                                                <strong>الكمية:</strong> {{ $notification->data['quantity'] }}
                                            </p>
                                        @endif
                                        @if(isset($notification->data['min_quantity']))
                                            <p class="mb-2">
                                                <strong>الحد الأدنى:</strong> {{ $notification->data['min_quantity'] }}
                                            </p>
                                        @endif
                                        @if(isset($notification->data['url']))
                                            <p class="mb-0">
                                                <a href="{{ $notification->data['url'] }}" class="btn btn-sm btn-primary">
                                                    <iconify-icon icon="solar:eye-bold-duotone" class="me-1"></iconify-icon>
                                                    عرض المنتج
                                                </a>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label fw-semibold">تاريخ الإنشاء</label>
                            <p class="text-muted mb-0">
                                {{ $notification->created_at->format('Y-m-d H:i:s') }}
                                <small class="ms-2">({{ $notification->created_at->diffForHumans() }})</small>
                            </p>
                        </div>

                        @if($notification->updated_at && $notification->updated_at->ne($notification->created_at))
                            <div class="mb-3">
                                <label class="form-label fw-semibold">آخر تحديث</label>
                                <p class="text-muted mb-0">
                                    {{ $notification->updated_at->format('Y-m-d H:i:s') }}
                                    <small class="ms-2">({{ $notification->updated_at->diffForHumans() }})</small>
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
