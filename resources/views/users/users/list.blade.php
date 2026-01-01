@extends('layouts.vertical', ['title' => 'Users List'])

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
                        <h4 class="mb-0">All Users</h4>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <p class="text-muted fw-medium fs-22 mb-0">{{ $totalUsers ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="avatar-md bg-primary bg-opacity-10 rounded">
                        <iconify-icon icon="solar:user-bold-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
                    </div>
                    <div>
                        <h4 class="mb-0">Managers</h4>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <p class="text-muted fw-medium fs-22 mb-0">{{ $totalAdmins ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="avatar-md bg-primary bg-opacity-10 rounded">
                        <iconify-icon icon="solar:user-speak-rounded-bold-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
                    </div>
                    <div>
                        <h4 class="mb-0">Employees</h4>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <p class="text-muted fw-medium fs-22 mb-0">{{ $totalEmployees ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="avatar-md bg-primary bg-opacity-10 rounded">
                        <iconify-icon icon="solar:user-check-rounded-bold-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
                    </div>
                    <div>
                        <h4 class="mb-0">Active Users</h4>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <p class="text-muted fw-medium fs-22 mb-0">{{ $totalActive ?? 0 }}</p>
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
                    <h4 class="card-title">All Users List</h4>
                </div>
                <div>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                        <iconify-icon icon="solar:add-circle-bold-duotone" class="me-1"></iconify-icon>
                        إنشاء مستخدم جديد
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
                                <th>اسم المستخدم</th>
                                <th>رقم الهاتف</th>
                                <th>البريد الإلكتروني</th>
                                <th>النوع</th>
                                <th>نوع الموظف</th>
                                <th>الحالة</th>
                                <th>الأدوار</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input user-checkbox" id="user-{{ $user->id }}" value="{{ $user->id }}">
                                        <label class="form-check-label" for="user-{{ $user->id }}">&nbsp;</label>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2">
                                            @if($user->image && $user->image_url)
                                                <img src="{{ $user->image_url }}" alt="{{ $user->name }}" 
                                                     class="avatar-sm rounded-circle border border-2 border-primary"
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                            <span class="avatar-sm d-flex align-items-center justify-content-center bg-{{ ['primary', 'info', 'success', 'warning'][$loop->index % 4] }}-subtle text-{{ ['primary', 'info', 'success', 'warning'][$loop->index % 4] }} rounded-circle fw-bold">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </span>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="mb-0 fw-semibold">{{ $user->name }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->phone }}</td>
                                <td>{{ $user->email ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $user->type->value === 'admin' ? 'primary' : 'info' }}-subtle text-{{ $user->type->value === 'admin' ? 'primary' : 'info' }} py-1 px-2">
                                        {{ $user->type->label() }}
                                    </span>
                                </td>
                                <td>
                                    @if($user->employeeType)
                                        <span class="badge bg-secondary-subtle text-secondary py-1 px-2">
                                            {{ $user->employeeType->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}-subtle text-{{ $user->is_active ? 'success' : 'danger' }} py-1 px-2">
                                        {{ $user->is_active ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </td>
                                <td>
                                    @if($user->roles->count() > 0)
                                        @foreach($user->roles->take(3) as $role)
                                            <span class="badge bg-light-subtle text-muted border py-1 px-2 mb-1">{{ $role->name }}</span>
                                        @endforeach
                                        @if($user->roles->count() > 3)
                                            <span class="badge bg-primary-subtle text-primary py-1 px-2 mb-1">+{{ $user->roles->count() - 3 }}</span>
                                        @endif
                                    @else
                                        <span class="text-muted">لا توجد أدوار</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-light btn-sm" title="عرض">
                                            <iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-soft-primary btn-sm" title="تعديل">
                                            <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                                        </a>
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟');">
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
                                <td colspan="9" class="text-center py-4">
                                    <p class="text-muted mb-0">لا يوجد مستخدمين</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- end table-responsive -->
            </div>
            @if($users->hasPages())
            <div class="card-footer border-top">
                <nav aria-label="Page navigation example">
                    {!! $users->links() !!}
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
        const checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
</script>
@endsection
