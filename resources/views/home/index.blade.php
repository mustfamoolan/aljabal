@extends('layouts.vertical', ['title' => 'الصفحة الرئيسية'])

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="card-title mb-2">مرحباً بك في الصفحة الرئيسية</h4>
                            <p class="text-muted mb-0">اختر الصفحة التي تريد الوصول إليها</p>
                        </div>
                        <div>
                            <div class="avatar-md bg-primary bg-opacity-10 rounded">
                                <iconify-icon icon="solar:home-smile-bold-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- المنتجات -->
        <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar-md bg-soft-primary rounded-circle mx-auto mb-3">
                        <iconify-icon icon="solar:t-shirt-bold-duotone" class="avatar-title fs-32 text-primary"></iconify-icon>
                    </div>
                    <h5 class="card-title mb-2">المنتجات</h5>
                    <p class="text-muted mb-3">إدارة المنتجات والمخزون</p>
                    <a href="{{ route('inventory.products.index') }}" class="btn btn-primary btn-sm">
                        <iconify-icon icon="solar:arrow-right-linear" class="fs-16"></iconify-icon>
                        الانتقال
                    </a>
                </div>
            </div>
        </div>

        <!-- الفئات -->
        <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar-md bg-soft-success rounded-circle mx-auto mb-3">
                        <iconify-icon icon="solar:clipboard-list-bold-duotone" class="avatar-title fs-32 text-success"></iconify-icon>
                    </div>
                    <h5 class="card-title mb-2">الفئات</h5>
                    <p class="text-muted mb-3">إدارة فئات المنتجات</p>
                    <a href="{{ route('inventory.categories.index') }}" class="btn btn-success btn-sm">
                        <iconify-icon icon="solar:arrow-right-linear" class="fs-16"></iconify-icon>
                        الانتقال
                    </a>
                </div>
            </div>
        </div>

        <!-- الموردين -->
        <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar-md bg-soft-info rounded-circle mx-auto mb-3">
                        <iconify-icon icon="solar:users-group-two-rounded-bold-duotone" class="avatar-title fs-32 text-info"></iconify-icon>
                    </div>
                    <h5 class="card-title mb-2">الموردين</h5>
                    <p class="text-muted mb-3">إدارة الموردين</p>
                    <a href="{{ route('inventory.suppliers.index') }}" class="btn btn-info btn-sm">
                        <iconify-icon icon="solar:arrow-right-linear" class="fs-16"></iconify-icon>
                        الانتقال
                    </a>
                </div>
            </div>
        </div>

        <!-- الطلبات -->
        <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar-md bg-soft-warning rounded-circle mx-auto mb-3">
                        <iconify-icon icon="solar:bag-smile-bold-duotone" class="avatar-title fs-32 text-warning"></iconify-icon>
                    </div>
                    <h5 class="card-title mb-2">الطلبات</h5>
                    <p class="text-muted mb-3">إدارة الطلبات</p>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-warning btn-sm">
                        <iconify-icon icon="solar:arrow-right-linear" class="fs-16"></iconify-icon>
                        الانتقال
                    </a>
                </div>
            </div>
        </div>

        <!-- طلبات السحب -->
        <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar-md bg-soft-danger rounded-circle mx-auto mb-3">
                        <iconify-icon icon="solar:wallet-money-bold-duotone" class="avatar-title fs-32 text-danger"></iconify-icon>
                    </div>
                    <h5 class="card-title mb-2">طلبات السحب</h5>
                    <p class="text-muted mb-3">إدارة طلبات السحب</p>
                    <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-danger btn-sm">
                        <iconify-icon icon="solar:arrow-right-linear" class="fs-16"></iconify-icon>
                        الانتقال
                    </a>
                </div>
            </div>
        </div>

        <!-- الإعدادات -->
        <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar-md bg-soft-secondary rounded-circle mx-auto mb-3">
                        <iconify-icon icon="solar:settings-bold-duotone" class="avatar-title fs-32 text-secondary"></iconify-icon>
                    </div>
                    <h5 class="card-title mb-2">الإعدادات</h5>
                    <p class="text-muted mb-3">إعدادات النظام العامة</p>
                    <a href="{{ route('general.settings.index') }}" class="btn btn-secondary btn-sm">
                        <iconify-icon icon="solar:arrow-right-linear" class="fs-16"></iconify-icon>
                        الانتقال
                    </a>
                </div>
            </div>
        </div>

        <!-- التاغات -->
        <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar-md bg-soft-primary rounded-circle mx-auto mb-3">
                        <iconify-icon icon="solar:tag-bold-duotone" class="avatar-title fs-32 text-primary"></iconify-icon>
                    </div>
                    <h5 class="card-title mb-2">التاغات</h5>
                    <p class="text-muted mb-3">إدارة التاغات</p>
                    <a href="{{ route('admin.tags.index') }}" class="btn btn-primary btn-sm">
                        <iconify-icon icon="solar:arrow-right-linear" class="fs-16"></iconify-icon>
                        الانتقال
                    </a>
                </div>
            </div>
        </div>

        <!-- المندوبين -->
        <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar-md bg-soft-success rounded-circle mx-auto mb-3">
                        <iconify-icon icon="solar:user-speak-rounded-bold-duotone" class="avatar-title fs-32 text-success"></iconify-icon>
                    </div>
                    <h5 class="card-title mb-2">المندوبين</h5>
                    <p class="text-muted mb-3">إدارة المندوبين</p>
                    <a href="{{ route('admin.representatives.index') }}" class="btn btn-success btn-sm">
                        <iconify-icon icon="solar:arrow-right-linear" class="fs-16"></iconify-icon>
                        الانتقال
                    </a>
                </div>
            </div>
        </div>

        <!-- الحسابات -->
        <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar-md bg-soft-info rounded-circle mx-auto mb-3">
                        <iconify-icon icon="solar:card-bold-duotone" class="avatar-title fs-32 text-info"></iconify-icon>
                    </div>
                    <h5 class="card-title mb-2">الحسابات</h5>
                    <p class="text-muted mb-3">إدارة حسابات المندوبين</p>
                    <a href="{{ route('admin.accounts.index') }}" class="btn btn-info btn-sm">
                        <iconify-icon icon="solar:arrow-right-linear" class="fs-16"></iconify-icon>
                        الانتقال
                    </a>
                </div>
            </div>
        </div>

        <!-- المستخدمين -->
        <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar-md bg-soft-warning rounded-circle mx-auto mb-3">
                        <iconify-icon icon="solar:user-bold-duotone" class="avatar-title fs-32 text-warning"></iconify-icon>
                    </div>
                    <h5 class="card-title mb-2">المستخدمين</h5>
                    <p class="text-muted mb-3">إدارة المديرين والموظفين</p>
                    <a href="{{ route('users.users.list') }}" class="btn btn-warning btn-sm">
                        <iconify-icon icon="solar:arrow-right-linear" class="fs-16"></iconify-icon>
                        الانتقال
                    </a>
                </div>
            </div>
        </div>

        <!-- الأدوار -->
        <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar-md bg-soft-danger rounded-circle mx-auto mb-3">
                        <iconify-icon icon="solar:user-speak-rounded-bold-duotone" class="avatar-title fs-32 text-danger"></iconify-icon>
                    </div>
                    <h5 class="card-title mb-2">الأدوار</h5>
                    <p class="text-muted mb-3">إدارة الأدوار</p>
                    <a href="{{ route('users.role.list') }}" class="btn btn-danger btn-sm">
                        <iconify-icon icon="solar:arrow-right-linear" class="fs-16"></iconify-icon>
                        الانتقال
                    </a>
                </div>
            </div>
        </div>

        <!-- الصلاحيات -->
        <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar-md bg-soft-secondary rounded-circle mx-auto mb-3">
                        <iconify-icon icon="solar:checklist-minimalistic-bold-duotone" class="avatar-title fs-32 text-secondary"></iconify-icon>
                    </div>
                    <h5 class="card-title mb-2">الصلاحيات</h5>
                    <p class="text-muted mb-3">إدارة الصلاحيات</p>
                    <a href="{{ route('users.pages-permission') }}" class="btn btn-secondary btn-sm">
                        <iconify-icon icon="solar:arrow-right-linear" class="fs-16"></iconify-icon>
                        الانتقال
                    </a>
                </div>
            </div>
        </div>

        <!-- أنواع الموظفين -->
        <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar-md bg-soft-primary rounded-circle mx-auto mb-3">
                        <iconify-icon icon="solar:user-id-bold-duotone" class="avatar-title fs-32 text-primary"></iconify-icon>
                    </div>
                    <h5 class="card-title mb-2">أنواع الموظفين</h5>
                    <p class="text-muted mb-3">إدارة أنواع الموظفين</p>
                    <a href="{{ route('admin.employee-types.index') }}" class="btn btn-primary btn-sm">
                        <iconify-icon icon="solar:arrow-right-linear" class="fs-16"></iconify-icon>
                        الانتقال
                    </a>
                </div>
            </div>
        </div>

        <!-- إعدادات عمولة التجهيز -->
        <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar-md bg-soft-success rounded-circle mx-auto mb-3">
                        <iconify-icon icon="solar:percent-bold-duotone" class="avatar-title fs-32 text-success"></iconify-icon>
                    </div>
                    <h5 class="card-title mb-2">إعدادات عمولة التجهيز</h5>
                    <p class="text-muted mb-3">إعدادات عمولة تجهيز الطلبات</p>
                    <a href="{{ route('admin.settings.order-commission.index') }}" class="btn btn-success btn-sm">
                        <iconify-icon icon="solar:arrow-right-linear" class="fs-16"></iconify-icon>
                        الانتقال
                    </a>
                </div>
            </div>
        </div>

        <!-- الإشعارات -->
        <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar-md bg-soft-info rounded-circle mx-auto mb-3">
                        <iconify-icon icon="solar:bell-bold-duotone" class="avatar-title fs-32 text-info"></iconify-icon>
                    </div>
                    <h5 class="card-title mb-2">الإشعارات</h5>
                    <p class="text-muted mb-3">عرض وإدارة الإشعارات</p>
                    <a href="{{ route('notifications.index') }}" class="btn btn-info btn-sm">
                        <iconify-icon icon="solar:arrow-right-linear" class="fs-16"></iconify-icon>
                        الانتقال
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

