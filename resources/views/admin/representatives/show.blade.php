@extends('layouts.vertical', ['title' => 'Representative Details'])

@section('content')

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">تفاصيل المندوب</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12 text-center mb-4">
                        @if($representative->image)
                            <img src="{{ asset('storage/' . $representative->image) }}" alt="{{ $representative->name }}" class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="avatar-lg d-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle fw-bold mx-auto" style="width: 150px; height: 150px;">
                                <span style="font-size: 48px;">{{ strtoupper(substr($representative->name, 0, 1)) }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">اسم المندوب</label>
                            <p class="form-control-plaintext">{{ $representative->name }}</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">رقم الهاتف</label>
                            <p class="form-control-plaintext">{{ $representative->phone }}</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">البريد الإلكتروني</label>
                            <p class="form-control-plaintext">{{ $representative->email ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">الحالة</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-{{ $representative->is_active ? 'success' : 'danger' }}-subtle text-{{ $representative->is_active ? 'success' : 'danger' }} py-1 px-2">
                                    {{ $representative->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </p>
                        </div>
                    </div>
                    @if($representative->address)
                    <div class="col-lg-12">
                        <div class="mb-3">
                            <label class="form-label">العنوان</label>
                            <p class="form-control-plaintext">{{ $representative->address }}</p>
                        </div>
                    </div>
                    @endif
                    <div class="col-lg-12">
                        <div class="mb-3">
                            <label class="form-label">تاريخ الإنشاء</label>
                            <p class="form-control-plaintext">{{ $representative->created_at?->format('d M Y, h:i a') ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="mb-3">
                            <label class="form-label">آخر تحديث</label>
                            <p class="form-control-plaintext">{{ $representative->updated_at?->format('d M Y, h:i a') ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer border-top">
                <a href="{{ route('admin.representatives.edit', $representative) }}" class="btn btn-primary">تعديل</a>
                <a href="{{ route('admin.representatives.index') }}" class="btn btn-secondary">رجوع</a>
            </div>
        </div>
    </div>
</div>

@endsection

