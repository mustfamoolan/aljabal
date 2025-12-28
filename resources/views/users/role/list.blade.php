@extends('layouts.vertical', ['title' => 'Roles List'])

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card overflow-hiddenCoupons">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">قائمة الأدوار</h4>
        <a href="{{ route('users.role.create') }}" class="btn btn-primary">
            <iconify-icon icon="solar:add-circle-bold-duotone" class="me-1"></iconify-icon>
            إنشاء دور جديد
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover table-centered">
                <thead class="bg-light-subtle">
                    <tr>
                        <th>Role</th>
                        <th>Permissions</th>
                        <th>Users</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                    <tr>
                        <td>
                            <p class="fs-15 mb-0 fw-semibold">{{ $role->name }}</p>
                        </td>
                        <td>
                            @if($role->permissions->count() > 0)
                                @foreach($role->permissions->take(3) as $permission)
                                    <span class="badge bg-light-subtle text-muted border py-1 px-2 mb-1">{{ $permission->name }}</span>
                                @endforeach
                                @if($role->permissions->count() > 3)
                                    <span class="badge bg-primary-subtle text-primary py-1 px-2 mb-1">+{{ $role->permissions->count() - 3 }}</span>
                                @endif
                            @else
                                <span class="text-muted">لا توجد صلاحيات</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $usersCount = $role->users()->count();
                                $users = $role->users()->take(4)->get();
                            @endphp
                            @if($usersCount > 0)
                                <div class="avatar-group">
                                    @foreach($users as $user)
                                        <div class="avatar">
                                            <span class="avatar-sm d-flex align-items-center justify-content-center bg-{{ ['primary', 'info', 'success', 'warning'][$loop->index % 4] }}-subtle text-{{ ['primary', 'info', 'success', 'warning'][$loop->index % 4] }} rounded-circle fw-bold shadow">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    @endforeach
                                    @if($usersCount > 4)
                                        <div class="avatar">
                                            <span class="avatar-sm d-flex align-items-center justify-content-center bg-dark text-white rounded-circle fw-bold shadow">
                                                +{{ $usersCount - 4 }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted">لا يوجد مستخدمين</span>
                            @endif
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" checked disabled>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('users.role.edit', $role) }}" class="btn btn-soft-primary btn-sm" title="تعديل">
                                    <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                                </a>
                                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الدور؟');">
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
                        <td colspan="5" class="text-center py-4">
                            <p class="text-muted mb-0">لا توجد أدوار</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- end table-responsive -->
    </div>
    @if($roles->hasPages())
    <div class="row g-0 align-items-center justify-content-between text-center text-sm-start p-3 border-top">
        <div class="col-sm">
            <div class="text-muted">
                عرض <span class="fw-semibold">{{ $roles->firstItem() }}</span> إلى <span class="fw-semibold">{{ $roles->lastItem() }}</span> من <span class="fw-semibold">{{ $roles->total() }}</span> نتيجة
            </div>
        </div>
        <div class="col-sm-auto mt-3 mt-sm-0">
            {!! $roles->links() !!}
        </div>
    </div>
    @endif
</div> <!-- end card -->

@endsection
