@extends('layouts.vertical', ['title' => 'Representatives List'])

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
                        <h4 class="mb-0">All Representatives</h4>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <p class="text-muted fw-medium fs-22 mb-0">{{ $totalRepresentatives ?? 0 }}</p>
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
                        <h4 class="mb-0">Active</h4>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <p class="text-muted fw-medium fs-22 mb-0">{{ $totalActive ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="avatar-md bg-danger bg-opacity-10 rounded">
                        <iconify-icon icon="solar:user-block-rounded-bold-duotone" class="fs-32 text-danger avatar-title"></iconify-icon>
                    </div>
                    <div>
                        <h4 class="mb-0">Inactive</h4>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <p class="text-muted fw-medium fs-22 mb-0">{{ $totalInactive ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="d-flex card-header justify-content-between align-items-center">
                <div>
                    <h4 class="card-title">قائمة المندوبين</h4>
                </div>
                <div>
                    <a href="{{ route('admin.representatives.create') }}" class="btn btn-primary btn-sm">
                        <iconify-icon icon="solar:add-circle-bold-duotone" class="me-1"></iconify-icon>
                        إنشاء مندوب جديد
                    </a>
                </div>
            </div>
            <div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-centered">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th style="width: 20px;">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="customCheckAll">
                                        <label class="form-check-label" for="customCheckAll"></label>
                                    </div>
                                </th>
                                <th>الصورة</th>
                                <th>اسم المندوب</th>
                                <th>رقم الهاتف</th>
                                <th>البريد الإلكتروني</th>
                                <th>العنوان</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($representatives as $representative)
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input representative-checkbox" id="representative-{{ $representative->id }}" value="{{ $representative->id }}">
                                        <label class="form-check-label" for="representative-{{ $representative->id }}">&nbsp;</label>
                                    </div>
                                </td>
                                <td>
                                    @if($representative->image)
                                        <img src="{{ asset('storage/' . $representative->image) }}" alt="{{ $representative->name }}" class="avatar-sm rounded-circle">
                                    @else
                                        <div class="avatar-sm d-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle fw-bold">
                                            {{ strtoupper(substr($representative->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <p class="mb-0 fw-semibold">{{ $representative->name }}</p>
                                    </div>
                                </td>
                                <td>{{ $representative->phone }}</td>
                                <td>{{ $representative->email ?? '-' }}</td>
                                <td>{{ $representative->address ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $representative->is_active ? 'success' : 'danger' }}-subtle text-{{ $representative->is_active ? 'success' : 'danger' }} py-1 px-2">
                                        {{ $representative->is_active ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.representatives.show', $representative) }}" class="btn btn-light btn-sm" title="عرض">
                                            <iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon>
                                        </a>
                                        <a href="{{ route('admin.representatives.edit', $representative) }}" class="btn btn-soft-primary btn-sm" title="تعديل">
                                            <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                                        </a>
                                        <form action="{{ route('admin.representatives.destroy', $representative) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المندوب؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-soft-danger btn-sm" title="حذف">
                                                <iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <p class="text-muted mb-0">لا يوجد مندوبين</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- end table-responsive -->
            </div>
            @if($representatives->hasPages())
            <div class="card-footer border-top">
                <nav aria-label="Page navigation example">
                    {!! $representatives->links() !!}
                </nav>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('script-bottom')
<script>
    // Select all checkbox
    document.getElementById('customCheckAll')?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.representative-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
</script>
@endsection

