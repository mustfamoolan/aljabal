@extends('layouts.vertical', ['title' => 'Suppliers List'])

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="avatar-md bg-primary bg-opacity-10 rounded">
                        <iconify-icon icon="solar:users-group-two-rounded-bold-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
                    </div>
                    <div>
                        <h4 class="mb-0">All Suppliers</h4>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <p class="text-muted fw-medium fs-22 mb-0">{{ $suppliers->total() ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="avatar-md bg-success bg-opacity-10 rounded">
                        <iconify-icon icon="solar:user-check-rounded-bold-duotone" class="fs-32 text-success avatar-title"></iconify-icon>
                    </div>
                    <div>
                        <h4 class="mb-0">Active Suppliers</h4>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <p class="text-muted fw-medium fs-22 mb-0">{{ $suppliers->where('is_active', true)->count() }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title flex-grow-1">قائمة الموردين</h4>

                <a href="{{ route('inventory.suppliers.create') }}" class="btn btn-sm btn-primary">
                    <iconify-icon icon="solar:add-circle-bold-duotone" class="me-1"></iconify-icon>
                    إضافة مورد
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-centered">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>اسم المورد</th>
                                <th>الهاتف</th>
                                <th>البريد الإلكتروني</th>
                                <th>الشخص المسؤول</th>
                                <th>عدد المنتجات</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($suppliers as $supplier)
                            <tr>
                                <td>
                                    <p class="fs-15 mb-0 fw-semibold">{{ $supplier->name }}</p>
                                </td>
                                <td>
                                    <p class="text-muted mb-0">{{ $supplier->phone ?? '-' }}</p>
                                </td>
                                <td>
                                    <p class="text-muted mb-0">{{ $supplier->email ?? '-' }}</p>
                                </td>
                                <td>
                                    <p class="text-muted mb-0">{{ $supplier->contact_person ?? '-' }}</p>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary py-1 px-2">{{ $supplier->products->count() }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $supplier->is_active ? 'success' : 'danger' }}-subtle text-{{ $supplier->is_active ? 'success' : 'danger' }} py-1 px-2">
                                        {{ $supplier->is_active ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('inventory.suppliers.show', $supplier) }}" class="btn btn-soft-info btn-sm" title="عرض">
                                            <iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon>
                                        </a>
                                        <a href="{{ route('inventory.suppliers.edit', $supplier) }}" class="btn btn-soft-primary btn-sm" title="تعديل">
                                            <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                                        </a>
                                        <button type="button"
                                                class="btn btn-soft-danger btn-sm"
                                                title="حذف"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteConfirmModal"
                                                data-action="{{ route('admin.inventory.suppliers.destroy', $supplier) }}"
                                                data-message="هل أنت متأكد من حذف هذا المورد؟">
                                            <iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <p class="text-muted mb-0">لا توجد موردين</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- end table-responsive -->
            </div>
            @if($suppliers->hasPages())
            <div class="row g-0 align-items-center justify-content-between text-center text-sm-start p-3 border-top">
                <div class="col-sm">
                    <div class="text-muted">
                        عرض <span class="fw-semibold">{{ $suppliers->firstItem() }}</span> إلى <span class="fw-semibold">{{ $suppliers->lastItem() }}</span> من <span class="fw-semibold">{{ $suppliers->total() }}</span> نتيجة
                    </div>
                </div>
                <div class="col-sm-auto mt-3 mt-sm-0">
                    {!! $suppliers->links() !!}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection
