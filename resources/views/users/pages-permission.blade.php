@extends('layouts.vertical', ['title' => 'Permissions'])

@section('css')
@vite(['node_modules/gridjs/dist/theme/mermaid.min.css'])
@endsection

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title mb-2 d-flex align-items-center gap-2">الصلاحيات</h4>
                        <p class="text-muted fw-medium fs-22 mb-0">{{ $totalPermissions ?? 0 }}</p>
                    </div>
                    <div>
                        <div class="avatar-md bg-primary bg-opacity-10 rounded">
                            <iconify-icon icon="solar:checklist-minimalistic-bold-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title mb-2 d-flex align-items-center gap-2">الأدوار</h4>
                        <p class="text-muted fw-medium fs-22 mb-0">{{ $totalRoles ?? 0 }}</p>
                    </div>
                    <div>
                        <div class="avatar-md bg-info bg-opacity-10 rounded">
                            <iconify-icon icon="solar:user-speak-rounded-bold-duotone" class="fs-32 text-info avatar-title"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title mb-2 d-flex align-items-center gap-2">المستخدمين</h4>
                        <p class="text-muted fw-medium fs-22 mb-0">{{ $totalUsers ?? 0 }}</p>
                    </div>
                    <div>
                        <div class="avatar-md bg-success bg-opacity-10 rounded">
                            <iconify-icon icon="solar:users-group-two-rounded-bold-duotone" class="fs-32 text-success avatar-title"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title mb-2 d-flex align-items-center gap-2">المستخدمين النشطين</h4>
                        <p class="text-muted fw-medium fs-22 mb-0">{{ \App\Models\User::where('is_active', true)->whereIn('type', ['admin', 'employee'])->count() }}</p>
                    </div>
                    <div>
                        <div class="avatar-md bg-warning bg-opacity-10 rounded">
                            <iconify-icon icon="solar:user-check-rounded-bold-duotone" class="fs-32 text-warning avatar-title"></iconify-icon>
                        </div>
                    </div>
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
                    <h4 class="card-title">قائمة الصلاحيات</h4>
                </div>
                <div>
                    <a href="{{ route('users.permissions.create') }}" class="btn btn-primary btn-sm">
                        <iconify-icon icon="solar:add-circle-bold-duotone" class="me-1"></iconify-icon>
                        إنشاء صلاحية جديدة
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div id="permissions-table"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script-bottom')
@vite(['resources/js/components/permissions-table.js'])
@php
    $permissionsData = $permissions->map(function($permission) {
        $parts = explode('.', $permission->name);
        $module = $parts[0] ?? '';
        $action = $parts[1] ?? '';

        $moduleLabels = [
            'users' => 'المستخدمين',
            'roles' => 'الأدوار',
            'permissions' => 'الصلاحيات',
            'employees' => 'الموظفين',
            'representatives' => 'المندوبين',
            'admin' => 'لوحة التحكم',
            'inventory' => 'المخزون',
        ];

        $actionLabels = [
            'view' => 'عرض',
            'create' => 'إنشاء',
            'update' => 'تحديث',
            'delete' => 'حذف',
            'access' => 'الوصول',
        ];

        $moduleLabel = $moduleLabels[$module] ?? $module;
        $actionLabel = $actionLabels[$action] ?? $action;

        if ($module === 'inventory' && isset($parts[2])) {
            $subModule = $parts[1];
            $subAction = $parts[2];
            $subModuleLabels = [
                'products' => 'المنتجات',
                'categories' => 'الفئات',
                'suppliers' => 'الموردين',
            ];
            $moduleLabel = $subModuleLabels[$subModule] ?? $subModule;
            $actionLabel = $actionLabels[$subAction] ?? $subAction;
        }

        return [
            'id' => $permission->id,
            'name' => $permission->name,
            'name_ar' => permission_label($permission->name),
            'description' => $moduleLabel . ' - ' . $actionLabel,
            'roles' => $permission->roles->pluck('name')->toArray(),
            'edit_url' => route('users.permissions.edit', $permission),
            'delete_url' => route('admin.permissions.destroy', $permission),
        ];
    })->values();
@endphp
<script>
    // Pass permissions data to JavaScript
    window.permissionsData = @json($permissionsData);
</script>
@endsection
