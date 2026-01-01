@extends('layouts.vertical', ['title' => 'User Details'])

@section('content')

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">تفاصيل المستخدم</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12 mb-4">
                        <div class="text-center">
                            <div class="position-relative d-inline-block">
                                @if($user->image && $user->image_url)
                                    <img src="{{ $user->image_url }}" alt="{{ $user->name }}" 
                                         class="avatar-lg rounded-circle border border-3 border-primary" 
                                         style="width: 120px; height: 120px; object-fit: cover;">
                                @else
                                    <div class="avatar-lg bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center border border-3 border-primary">
                                        <span class="text-primary fw-bold fs-48">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                            <h4 class="mt-3 mb-0">{{ $user->name }}</h4>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">اسم المستخدم</label>
                            <p class="form-control-plaintext">{{ $user->name }}</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">رقم الهاتف</label>
                            <p class="form-control-plaintext">{{ $user->phone }}</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">البريد الإلكتروني</label>
                            <p class="form-control-plaintext">{{ $user->email ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">نوع المستخدم</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-{{ $user->type->value === 'admin' ? 'primary' : 'info' }}-subtle text-{{ $user->type->value === 'admin' ? 'primary' : 'info' }} py-1 px-2">
                                    {{ $user->type->label() }}
                                </span>
                            </p>
                        </div>
                    </div>
                    @if($user->employeeType)
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">نوع الموظف</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-secondary-subtle text-secondary py-1 px-2">
                                    {{ $user->employeeType->name }}
                                </span>
                            </p>
                        </div>
                    </div>
                    @endif
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">الحالة</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}-subtle text-{{ $user->is_active ? 'success' : 'danger' }} py-1 px-2">
                                    {{ $user->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="mb-3">
                            <label class="form-label">الأدوار</label>
                            <div>
                                @if($user->roles->count() > 0)
                                    @foreach($user->roles as $role)
                                        <span class="badge bg-primary-subtle text-primary py-1 px-2 mb-1">{{ $role->name }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">لا توجد أدوار</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="mb-3">
                            <label class="form-label">تاريخ الإنشاء</label>
                            <p class="form-control-plaintext">{{ $user->created_at?->format('d M Y, h:i a') ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="mb-3">
                            <label class="form-label">آخر تحديث</label>
                            <p class="form-control-plaintext">{{ $user->updated_at?->format('d M Y, h:i a') ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer border-top">
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">تعديل</a>
                <a href="{{ route('users.users.list') }}" class="btn btn-secondary">رجوع</a>
            </div>
        </div>
    </div>
</div>

@endsection
