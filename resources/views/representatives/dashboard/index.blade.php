@extends('layouts.representative', ['title' => 'لوحة التحكم - المندوبين'])

@section('content')
    <!-- Welcome Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="card-title mb-2">مرحباً، {{ $representative->name }}</h4>
                            <p class="text-muted mb-0">لوحة التحكم الخاصة بالمندوبين</p>
                        </div>
                        <div>
                            <div class="avatar-md bg-primary bg-opacity-10 rounded">
                                <iconify-icon icon="solar:user-speak-rounded-bold-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-3">
                    <div class="input-group">
                        <button class="btn btn-primary" type="button" id="search-btn">
                            <iconify-icon icon="solar:magnifer-linear" class="fs-18"></iconify-icon>
                        </button>
                        <input type="search" class="form-control" placeholder="ابحث عن طلب..." id="search-input">
                        <button class="btn btn-primary" type="button" id="filter-btn">
                            <iconify-icon icon="solar:filter-bold-duotone" class="fs-18"></iconify-icon>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Cards -->
    <div class="row mb-4">
        <div class="col-12 col-md-6 mb-3 mb-md-3">
            <a href="{{ route('representative.orders.create') }}" class="text-decoration-none">
                <div class="card h-100 action-card action-card-primary">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <div class="avatar-lg bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center">
                                <iconify-icon icon="solar:document-add-bold-duotone" class="fs-48 text-primary"></iconify-icon>
                            </div>
                        </div>
                        <h5 class="card-title mb-2">طلب جديد</h5>
                        <p class="text-muted mb-0 small">إنشاء طلب جديد</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-md-6 mb-3 mb-md-3">
            <a href="{{ route('representative.orders.index') }}" class="text-decoration-none">
                <div class="card h-100 action-card action-card-success">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <div class="avatar-lg bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center">
                                <iconify-icon icon="solar:document-text-bold-duotone" class="fs-48 text-success"></iconify-icon>
                            </div>
                        </div>
                        <h5 class="card-title mb-2">طلباتي</h5>
                        <p class="text-muted mb-0 small">عرض جميع الطلبات</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-md-6 mb-3 mb-md-3">
            <a href="{{ route('representative.account.index') }}" class="text-decoration-none">
                <div class="card h-100 action-card action-card-info">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <div class="avatar-lg bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center">
                                <iconify-icon icon="solar:wallet-money-bold-duotone" class="fs-48 text-info"></iconify-icon>
                            </div>
                        </div>
                        <h5 class="card-title mb-2">حسابات</h5>
                        <p class="text-muted mb-0 small">إدارة الحسابات</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-md-6 mb-3 mb-md-3">
            <a href="#" class="text-decoration-none">
                <div class="card h-100 action-card action-card-warning">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <div class="avatar-lg bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center">
                                <iconify-icon icon="solar:box-bold-duotone" class="fs-48 text-warning"></iconify-icon>
                            </div>
                        </div>
                        <h5 class="card-title mb-2">المنتجات</h5>
                        <p class="text-muted mb-0 small">عرض جميع المنتجات</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
@endsection

@section('css')
<style>
    .action-card {
        transition: all 0.3s ease;
        border: 1px solid transparent;
    }

    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .action-card-primary:hover {
        border-color: var(--bs-primary);
    }

    .action-card-success:hover {
        border-color: var(--bs-success);
    }

    .action-card-info:hover {
        border-color: var(--bs-info);
    }

    .action-card-warning:hover {
        border-color: var(--bs-warning);
    }

    [data-bs-theme="dark"] .action-card:hover {
        box-shadow: 0 10px 25px rgba(255, 255, 255, 0.1);
    }
</style>
@endsection
