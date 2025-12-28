@extends('layouts.vertical', ['title' => 'Categories List'])

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title flex-grow-1">قائمة الفئات</h4>

                <a href="{{ route('inventory.categories.create') }}" class="btn btn-sm btn-primary">
                    <iconify-icon icon="solar:add-circle-bold-duotone" class="me-1"></iconify-icon>
                    إضافة فئة
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-centered">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>اسم الفئة</th>
                                <th>الفئة الأصل</th>
                                <th>الوصف</th>
                                <th>عدد المنتجات</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                            <tr>
                                <td>
                                    <p class="fs-15 mb-0 fw-semibold">{{ $category->name }}</p>
                                    @if($category->children->isNotEmpty())
                                        <small class="text-muted">({{ $category->children->count() }} فئة فرعية)</small>
                                    @endif
                                </td>
                                <td>
                                    @if($category->parent)
                                        <span class="badge bg-info-subtle text-info py-1 px-2">{{ $category->parent->name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <p class="text-muted mb-0">{{ Str::limit($category->description ?? '-', 50) }}</p>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary py-1 px-2">{{ $category->products->count() }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $category->is_active ? 'success' : 'danger' }}-subtle text-{{ $category->is_active ? 'success' : 'danger' }} py-1 px-2">
                                        {{ $category->is_active ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('inventory.categories.show', $category) }}" class="btn btn-soft-info btn-sm" title="عرض">
                                            <iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon>
                                        </a>
                                        <a href="{{ route('inventory.categories.edit', $category) }}" class="btn btn-soft-primary btn-sm" title="تعديل">
                                            <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                                        </a>
                                        <button type="button"
                                                class="btn btn-soft-danger btn-sm"
                                                title="حذف"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteConfirmModal"
                                                data-action="{{ route('admin.inventory.categories.destroy', $category) }}"
                                                data-message="هل أنت متأكد من حذف هذه الفئة؟">
                                            <iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <p class="text-muted mb-0">لا توجد فئات</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- end table-responsive -->
            </div>
            @if($categories->hasPages())
            <div class="row g-0 align-items-center justify-content-between text-center text-sm-start p-3 border-top">
                <div class="col-sm">
                    <div class="text-muted">
                        عرض <span class="fw-semibold">{{ $categories->firstItem() }}</span> إلى <span class="fw-semibold">{{ $categories->lastItem() }}</span> من <span class="fw-semibold">{{ $categories->total() }}</span> نتيجة
                    </div>
                </div>
                <div class="col-sm-auto mt-3 mt-sm-0">
                    {!! $categories->links() !!}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection
