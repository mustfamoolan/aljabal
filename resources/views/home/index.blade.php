@extends('layouts.vertical', ['title' => 'الصفحة الرئيسية'])

@section('css')
    <style>
        .dashboard-card {
            border: none;
            border-radius: 28px !important;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            background: #fff;
            height: 100%;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08) !important;
        }

        .dark .dashboard-card {
            background: #1e1e2d;
        }

        .card-decoration-circle {
            position: absolute;
            right: -20px;
            top: -20px;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            z-index: 0;
        }

        .card-background-icon {
            position: absolute;
            right: 10px;
            bottom: 10px;
            font-size: 80px;
            opacity: 0.12;
            z-index: 0;
        }

        .card-icon-container {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            margin-bottom: 12px;
            z-index: 1;
            position: relative;
        }

        .card-content {
            position: relative;
            z-index: 1;
            padding: 24px;
        }

        .card-title-text {
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 4px;
            color: #333;
            display: block;
        }

        .dark .card-title-text {
            color: #fff;
        }

        .card-subtitle-text {
            font-size: 11px;
            color: #888;
            display: block;
        }

        .dashboard-badge {
            position: absolute;
            left: 20px;
            top: 20px;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 700;
            color: #fff;
            z-index: 2;
        }

        .quick-action-bar {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            border-radius: 28px;
            padding: 20px;
            color: #fff;
            display: flex;
            align-items: center;
            transition: transform 0.3s ease;
            text-decoration: none !important;
            box-shadow: 0 8px 15px rgba(78, 115, 223, 0.3);
        }

        .quick-action-bar:hover {
            transform: scale(1.015);
            color: #fff;
        }

        .action-icon-circle {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 16px;
        }
    </style>
@endsection

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-transparent border-0 shadow-none">
                <div class="card-body p-0">
                    <div class="d-flex align-items-center">
                        <div>
                            <h2 class="fw-black mb-1" style="letter-spacing: -0.5px;">لوحة التحكم</h2>
                            <div style="width: 40px; height: 4px; background: #4e73df; border-radius: 2px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- الطلبات -->
        @if(auth()->user()->isAdmin() || auth()->user()->can('admin.access'))
            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('admin.orders.index') }}" class="card dashboard-card text-decoration-none">
                    <div class="card-decoration-circle" style="background: rgba(54, 162, 235, 0.15);"></div>
                    <iconify-icon icon="solar:bag-bold-duotone" class="card-background-icon"
                        style="color: #36a2eb;"></iconify-icon>
                    <div class="card-content">
                        <div class="card-icon-container" style="background: rgba(54, 162, 235, 0.15);">
                            <iconify-icon icon="solar:bag-bold-duotone" class="fs-28" style="color: #36a2eb;"></iconify-icon>
                        </div>
                        <span class="card-title-text">الطلبات</span>
                        <span class="card-subtitle-text">إدارة وتتبع الطلبات</span>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('admin.settings.order-commission.index') }}" class="card dashboard-card text-decoration-none">
                    <div class="card-decoration-circle" style="background: rgba(54, 162, 235, 0.15);"></div>
                    <iconify-icon icon="solar:percent-bold-duotone" class="card-background-icon"
                        style="color: #36a2eb;"></iconify-icon>
                    <div class="card-content">
                        <div class="card-icon-container" style="background: rgba(54, 162, 235, 0.15);">
                            <iconify-icon icon="solar:percent-bold-duotone" class="fs-28"
                                style="color: #36a2eb;"></iconify-icon>
                        </div>
                        <span class="card-title-text">عمولة التجهيز</span>
                        <span class="card-subtitle-text">إعدادات العمولات</span>
                    </div>
                </a>
            </div>
        @endif

        <!-- المخزن -->
        @if(auth()->user()->isAdmin() || auth()->user()->can('inventory.products.view'))
            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('inventory.products.index') }}" class="card dashboard-card text-decoration-none">
                    <div class="card-decoration-circle" style="background: rgba(255, 159, 64, 0.15);"></div>
                    <iconify-icon icon="solar:box-bold-duotone" class="card-background-icon"
                        style="color: #ff9f40;"></iconify-icon>
                    <div class="card-content">
                        <div class="card-icon-container" style="background: rgba(255, 159, 64, 0.15);">
                            <iconify-icon icon="solar:box-bold-duotone" class="fs-28" style="color: #ff9f40;"></iconify-icon>
                        </div>
                        <span class="card-title-text">المنتجات والكتب</span>
                        <span class="card-subtitle-text">إدارة المخزون</span>
                    </div>
                </a>
            </div>
        @endif

        @if(auth()->user()->isAdmin() || auth()->user()->can('inventory.categories.view'))
            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('inventory.categories.index') }}" class="card dashboard-card text-decoration-none">
                    <div class="card-decoration-circle" style="background: rgba(255, 159, 64, 0.15);"></div>
                    <iconify-icon icon="solar:clipboard-list-bold-duotone" class="card-background-icon"
                        style="color: #ff9f40;"></iconify-icon>
                    <div class="card-content">
                        <div class="card-icon-container" style="background: rgba(255, 159, 64, 0.15);">
                            <iconify-icon icon="solar:clipboard-list-bold-duotone" class="fs-28"
                                style="color: #ff9f40;"></iconify-icon>
                        </div>
                        <span class="card-title-text">الفئات</span>
                        <span class="card-subtitle-text">تصنيفات المنتجات</span>
                    </div>
                </a>
            </div>
        @endif

        @if(auth()->user()->isAdmin() || auth()->user()->can('inventory.suppliers.view'))
            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('inventory.suppliers.index') }}" class="card dashboard-card text-decoration-none">
                    <div class="card-decoration-circle" style="background: rgba(255, 159, 64, 0.15);"></div>
                    <iconify-icon icon="solar:users-group-two-rounded-bold-duotone" class="card-background-icon"
                        style="color: #ff9f40;"></iconify-icon>
                    <div class="card-content">
                        <div class="card-icon-container" style="background: rgba(255, 159, 64, 0.15);">
                            <iconify-icon icon="solar:users-group-two-rounded-bold-duotone" class="fs-28"
                                style="color: #ff9f40;"></iconify-icon>
                        </div>
                        <span class="card-title-text">الموردين</span>
                        <span class="card-subtitle-text">إدارة الموردين</span>
                    </div>
                </a>
            </div>
        @endif

        <!-- التاغات -->
        @if(auth()->user()->isAdmin() || auth()->user()->can('admin.access') || auth()->user()->can('tags.view'))
            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('admin.tags.index') }}" class="card dashboard-card text-decoration-none">
                    <div class="card-decoration-circle" style="background: rgba(13, 110, 253, 0.15);"></div>
                    <iconify-icon icon="solar:tag-bold-duotone" class="card-background-icon"
                        style="color: #0d6efd;"></iconify-icon>
                    <div class="card-content">
                        <div class="card-icon-container" style="background: rgba(13, 110, 253, 0.15);">
                            <iconify-icon icon="solar:tag-bold-duotone" class="fs-28" style="color: #0d6efd;"></iconify-icon>
                        </div>
                        <span class="card-title-text">التاغات</span>
                        <span class="card-subtitle-text">إدارة التوسيم</span>
                    </div>
                </a>
            </div>
        @endif

        <!-- المناديب -->
        @if(auth()->user()->isAdmin() || auth()->user()->can('representatives.view'))
            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('admin.representatives.index') }}" class="card dashboard-card text-decoration-none">
                    <div class="card-decoration-circle" style="background: rgba(25, 135, 84, 0.15);"></div>
                    <iconify-icon icon="solar:user-speak-rounded-bold-duotone" class="card-background-icon"
                        style="color: #198754;"></iconify-icon>
                    <div class="card-content">
                        <div class="card-icon-container" style="background: rgba(25, 135, 84, 0.15);">
                            <iconify-icon icon="solar:user-speak-rounded-bold-duotone" class="fs-28"
                                style="color: #198754;"></iconify-icon>
                        </div>
                        <span class="card-title-text">المندوبين</span>
                        <span class="card-subtitle-text">إدارة المحافظ الربحية</span>
                    </div>
                </a>
            </div>

            @if(auth()->user()->isAdmin() || auth()->user()->can('admin.access'))
                <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                    <a href="{{ route('admin.accounts.index') }}" class="card dashboard-card text-decoration-none">
                        <div class="card-decoration-circle" style="background: rgba(25, 135, 84, 0.15);"></div>
                        <iconify-icon icon="solar:card-bold-duotone" class="card-background-icon"
                            style="color: #198754;"></iconify-icon>
                        <div class="card-content">
                            <div class="card-icon-container" style="background: rgba(25, 135, 84, 0.15);">
                                <iconify-icon icon="solar:card-bold-duotone" class="fs-28" style="color: #198754;"></iconify-icon>
                            </div>
                            <span class="card-title-text">حسابات المندوبين</span>
                            <span class="card-subtitle-text">السجلات المالية</span>
                        </div>
                    </a>
                </div>
            @endif
        @endif

        <!-- المالية -->
        @if(auth()->user()->isAdmin() || auth()->user()->can('admin.access'))
            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('admin.withdrawals.index') }}" class="card dashboard-card text-decoration-none">
                    <div class="card-decoration-circle" style="background: rgba(220, 53, 69, 0.15);"></div>
                    <iconify-icon icon="solar:wallet-money-bold-duotone" class="card-background-icon"
                        style="color: #dc3545;"></iconify-icon>
                    <div class="card-content">
                        <div class="card-icon-container" style="background: rgba(220, 53, 69, 0.15);">
                            <iconify-icon icon="solar:wallet-money-bold-duotone" class="fs-28"
                                style="color: #dc3545;"></iconify-icon>
                        </div>
                        <span class="card-title-text">طلبات السحب</span>
                        <span class="card-subtitle-text">إدارة التحويلات</span>
                    </div>
                </a>
            </div>
        @endif

        <!-- المستخدمين والصلاحيات -->
        @if(auth()->user()->isAdmin() || auth()->user()->can('users.view'))
            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('users.users.list') }}" class="card dashboard-card text-decoration-none">
                    <div class="card-decoration-circle" style="background: rgba(13, 202, 240, 0.15);"></div>
                    <iconify-icon icon="solar:user-bold-duotone" class="card-background-icon"
                        style="color: #0dcaf0;"></iconify-icon>
                    <div class="card-content">
                        <div class="card-icon-container" style="background: rgba(13, 202, 240, 0.15);">
                            <iconify-icon icon="solar:user-bold-duotone" class="fs-28" style="color: #0dcaf0;"></iconify-icon>
                        </div>
                        <span class="card-title-text">المستخدمين</span>
                        <span class="card-subtitle-text">المديرين والموظفين</span>
                    </div>
                </a>
            </div>
        @endif

        @if(auth()->user()->isAdmin() || auth()->user()->can('roles.view'))
            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('users.role.list') }}" class="card dashboard-card text-decoration-none">
                    <div class="card-decoration-circle" style="background: rgba(13, 202, 240, 0.15);"></div>
                    <iconify-icon icon="solar:shield-keyhole-bold-duotone" class="card-background-icon"
                        style="color: #0dcaf0;"></iconify-icon>
                    <div class="card-content">
                        <div class="card-icon-container" style="background: rgba(13, 202, 240, 0.15);">
                            <iconify-icon icon="solar:shield-keyhole-bold-duotone" class="fs-28"
                                style="color: #0dcaf0;"></iconify-icon>
                        </div>
                        <span class="card-title-text">الأدوار</span>
                        <span class="card-subtitle-text">توزيع المسؤوليات</span>
                    </div>
                </a>
            </div>
        @endif

        @if(auth()->user()->isAdmin() || auth()->user()->can('permissions.view'))
            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('users.pages-permission') }}" class="card dashboard-card text-decoration-none">
                    <div class="card-decoration-circle" style="background: rgba(13, 202, 240, 0.15);"></div>
                    <iconify-icon icon="solar:checklist-minimalistic-bold-duotone" class="card-background-icon"
                        style="color: #0dcaf0;"></iconify-icon>
                    <div class="card-content">
                        <div class="card-icon-container" style="background: rgba(13, 202, 240, 0.15);">
                            <iconify-icon icon="solar:checklist-minimalistic-bold-duotone" class="fs-28"
                                style="color: #0dcaf0;"></iconify-icon>
                        </div>
                        <span class="card-title-text">الصلاحيات</span>
                        <span class="card-subtitle-text">التحكم في الوصول</span>
                    </div>
                </a>
            </div>
        @endif

        @if(auth()->user()->isAdmin() || auth()->user()->can('admin.access'))
            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('admin.employee-types.index') }}" class="card dashboard-card text-decoration-none">
                    <div class="card-decoration-circle" style="background: rgba(13, 202, 240, 0.15);"></div>
                    <iconify-icon icon="solar:user-id-bold-duotone" class="card-background-icon"
                        style="color: #0dcaf0;"></iconify-icon>
                    <div class="card-content">
                        <div class="card-icon-container" style="background: rgba(13, 202, 240, 0.15);">
                            <iconify-icon icon="solar:user-id-bold-duotone" class="fs-28"
                                style="color: #0dcaf0;"></iconify-icon>
                        </div>
                        <span class="card-title-text">أنواع الموظفين</span>
                        <span class="card-subtitle-text">تصنيف الكادر</span>
                    </div>
                </a>
            </div>
        @endif

        <!-- الإعدادات -->
        @if(auth()->user()->isAdmin() || auth()->user()->can('admin.access'))
            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('general.settings.index') }}" class="card dashboard-card text-decoration-none">
                    <div class="card-decoration-circle" style="background: rgba(108, 117, 125, 0.15);"></div>
                    <iconify-icon icon="solar:settings-bold-duotone" class="card-background-icon"
                        style="color: #6c757d;"></iconify-icon>
                    <div class="card-content">
                        <div class="card-icon-container" style="background: rgba(108, 117, 125, 0.15);">
                            <iconify-icon icon="solar:settings-bold-duotone" class="fs-28"
                                style="color: #6c757d;"></iconify-icon>
                        </div>
                        <span class="card-title-text">إعدادات النظام</span>
                        <span class="card-subtitle-text">تكوين المنصة</span>
                    </div>
                </a>
            </div>
        @endif

        <!-- التقارير والإشعارات -->
        <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
            <a href="{{ route('admin.expenses.index') }}" class="card dashboard-card text-decoration-none">
                <div class="card-decoration-circle" style="background: rgba(220, 53, 69, 0.15);"></div>
                <iconify-icon icon="solar:bill-list-bold-duotone" class="card-background-icon"
                    style="color: #dc3545;"></iconify-icon>
                <div class="card-content">
                    <div class="card-icon-container" style="background: rgba(220, 53, 69, 0.15);">
                        <iconify-icon icon="solar:bill-list-bold-duotone" class="fs-28"
                            style="color: #dc3545;"></iconify-icon>
                    </div>
                    <span class="card-title-text">المصروفات</span>
                    <span class="card-subtitle-text">تكاليف التشغيل</span>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
            <a href="{{ route('admin.reports.index') }}" class="card dashboard-card text-decoration-none">
                <div class="card-decoration-circle" style="background: rgba(255, 193, 7, 0.15);"></div>
                <iconify-icon icon="solar:chart-bold-duotone" class="card-background-icon"
                    style="color: #ffc107;"></iconify-icon>
                <div class="card-content">
                    <div class="card-icon-container" style="background: rgba(255, 193, 7, 0.15);">
                        <iconify-icon icon="solar:chart-bold-duotone" class="fs-28" style="color: #ffc107;"></iconify-icon>
                    </div>
                    <span class="card-title-text">التقارير</span>
                    <span class="card-subtitle-text">تحليلات النمو</span>
                </div>
            </a>
        </div>

        @if(auth()->user()->isAdmin() || auth()->user()->can('notifications.send'))
            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('admin.notifications.send') }}" class="card dashboard-card text-decoration-none">
                    <div class="card-decoration-circle" style="background: rgba(78, 115, 223, 0.15);"></div>
                    <iconify-icon icon="solar:bell-bing-bold-duotone" class="card-background-icon"
                        style="color: #4e73df;"></iconify-icon>
                    <div class="card-content">
                        <div class="card-icon-container" style="background: rgba(78, 115, 223, 0.15);">
                            <iconify-icon icon="solar:bell-bing-bold-duotone" class="fs-28"
                                style="color: #4e73df;"></iconify-icon>
                        </div>
                        <span class="card-title-text">إرسال إشعار</span>
                        <span class="card-subtitle-text">تواصل مع المستخدمين</span>
                    </div>
                </a>
            </div>
        @endif

        <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
            <a href="{{ route('notifications.index') }}" class="card dashboard-card text-decoration-none">
                <div class="card-decoration-circle" style="background: rgba(13, 202, 240, 0.15);"></div>
                <iconify-icon icon="solar:bell-bing-bold-duotone" class="card-background-icon"
                    style="color: #0dcaf0;"></iconify-icon>
                <div class="card-content">
                    <div class="card-icon-container" style="background: rgba(13, 202, 240, 0.15);">
                        <iconify-icon icon="solar:bell-bing-bold-duotone" class="fs-28"
                            style="color: #0dcaf0;"></iconify-icon>
                    </div>
                    <span class="card-title-text">الإشعارات</span>
                    <span class="card-subtitle-text">التنبيهات البرمجية</span>
                </div>
            </a>
        </div>
    </div>

    <!-- Quick Action -->
    @if(auth()->user()->isAdmin() || auth()->user()->can('inventory.products.create'))
        <div class="row mt-2">
            <div class="col-12">
                <a href="{{ route('inventory.products.create') }}" class="quick-action-bar">
                    <div class="action-icon-circle">
                        <iconify-icon icon="solar:add-circle-bold" class="fs-32"></iconify-icon>
                    </div>
                    <div class="ms-3 flex-grow-1">
                        <h5 class="mb-1 fw-bold text-white">إضافة منتج جديد</h5>
                        <p class="mb-0 text-white-50 fs-12">أسرع وسيلة لإضافة المنتجات للمخزن</p>
                    </div>
                    <iconify-icon icon="solar:alt-arrow-left-linear" class="fs-20"></iconify-icon>
                </a>
            </div>
        </div>
    @endif
@endsection