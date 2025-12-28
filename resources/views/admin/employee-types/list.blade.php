@extends('layouts.vertical', ['title' => 'Employee Types List'])

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card overflow-hiddenCoupons">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">قائمة أنواع الموظفين</h4>
        <a href="{{ route('admin.employee-types.create') }}" class="btn btn-primary">
            <iconify-icon icon="solar:add-circle-bold-duotone" class="me-1"></iconify-icon>
            إضافة نوع جديد
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover table-centered">
                <thead class="bg-light-subtle">
                    <tr>
                        <th>اسم النوع</th>
                        <th>الوصف</th>
                        <th>الأدوار</th>
                        <th>الحالة</th>
                        <th>عدد المستخدمين</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employeeTypes as $employeeType)
                    <tr>
                        <td>
                            <p class="fs-15 mb-0 fw-semibold">{{ $employeeType->name }}</p>
                        </td>
                        <td>
                            <p class="text-muted mb-0">{{ $employeeType->description ?? '-' }}</p>
                        </td>
                        <td>
                            @if($employeeType->roles->count() > 0)
                                @foreach($employeeType->roles->take(3) as $role)
                                    <span class="badge bg-primary-subtle text-primary py-1 px-2 mb-1">{{ $role->name }}</span>
                                @endforeach
                                @if($employeeType->roles->count() > 3)
                                    <span class="badge bg-info-subtle text-info py-1 px-2 mb-1">+{{ $employeeType->roles->count() - 3 }}</span>
                                @endif
                            @else
                                <span class="text-muted">لا توجد أدوار</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $employeeType->is_active ? 'success' : 'danger' }}-subtle text-{{ $employeeType->is_active ? 'success' : 'danger' }} py-1 px-2">
                                {{ $employeeType->is_active ? 'نشط' : 'غير نشط' }}
                            </span>
                        </td>
                        <td>
                            <span class="text-muted">{{ $employeeType->users()->count() }}</span>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.employee-types.edit', $employeeType) }}" class="btn btn-soft-primary btn-sm" title="تعديل">
                                    <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                                </a>
                                <button type="button"
                                        class="btn btn-soft-danger btn-sm"
                                        title="حذف"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteConfirmModal"
                                        data-action="{{ route('admin.employee-types.destroy', $employeeType) }}"
                                        data-message="هل أنت متأكد من حذف هذا النوع؟">
                                    <iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <p class="text-muted mb-0">لا توجد أنواع موظفين</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- end table-responsive -->
    </div>
    @if($employeeTypes->hasPages())
    <div class="row g-0 align-items-center justify-content-between text-center text-sm-start p-3 border-top">
        <div class="col-sm">
            <div class="text-muted">
                عرض <span class="fw-semibold">{{ $employeeTypes->firstItem() }}</span> إلى <span class="fw-semibold">{{ $employeeTypes->lastItem() }}</span> من <span class="fw-semibold">{{ $employeeTypes->total() }}</span> نتيجة
            </div>
        </div>
        <div class="col-sm-auto mt-3 mt-sm-0">
            {!! $employeeTypes->links() !!}
        </div>
    </div>
    @endif
</div> <!-- end card -->

@endsection
