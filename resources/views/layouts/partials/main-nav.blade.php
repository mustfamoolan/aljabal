<div class="main-nav">
    <!-- Sidebar Logo -->
    <div class="logo-box">
        <a href="{{ route('home.index') }}" class="logo-dark">
            <img src="/images/logo-sm.png" class="logo-sm" alt="logo sm">
            <img src="/images/logo-dark.png" class="logo-lg" alt="logo dark">
        </a>

        <a href="{{ route('home.index') }}" class="logo-light">
            <img src="/images/logo-sm.png" class="logo-sm" alt="logo sm">
            <img src="/images/logo-light.png" class="logo-lg" alt="logo light">
        </a>
    </div>

    <!-- Menu Toggle Button (sm-hover) -->
    <button type="button" class="button-sm-hover" aria-label="Show Full Sidebar">
        <iconify-icon icon="solar:double-alt-arrow-right-bold-duotone" class="button-sm-hover-icon"></iconify-icon>
    </button>

    <div class="scrollbar" data-simplebar>
        <ul class="navbar-nav" id="navbar-nav">

            <li class="menu-title">عام</li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('home.index') }}">
                         <span class="nav-icon">
                              <iconify-icon icon="solar:home-smile-bold-duotone"></iconify-icon>
                         </span>
                    <span class="nav-text"> الصفحة الرئيسية </span>
                </a>
            </li>



            @if(auth()->user()->isAdmin() || auth()->user()->can('admin.access'))
            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarOrders" data-bs-toggle="collapse" role="button"
                   aria-expanded="false" aria-controls="sidebarOrders">
                         <span class="nav-icon">
                              <iconify-icon icon="solar:bag-smile-bold-duotone"></iconify-icon>
                         </span>
                    <span class="nav-text"> الطلبات </span>
                </a>
                <div class="collapse" id="sidebarOrders">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.orders.index') }}">قائمة الطلبات</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.settings.order-commission.index') }}">إعدادات عمولة التجهيز</a>
                        </li>
                    </ul>
                </div>
            </li>
            @endif


            @if(auth()->user()->isAdmin() || auth()->user()->can('admin.access'))
            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarWithdrawals" data-bs-toggle="collapse" role="button"
                   aria-expanded="false" aria-controls="sidebarWithdrawals">
                         <span class="nav-icon">
                              <iconify-icon icon="solar:wallet-money-bold-duotone"></iconify-icon>
                         </span>
                    <span class="nav-text"> طلبات السحب </span>
                </a>
                <div class="collapse" id="sidebarWithdrawals">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.withdrawals.index') }}">قائمة الطلبات</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('general.settings.index') }}">
                         <span class="nav-icon">
                              <iconify-icon icon="solar:settings-bold-duotone"></iconify-icon>
                         </span>
                    <span class="nav-text"> الإعدادات </span>
                </a>
            </li>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->can('admin.access') || auth()->user()->can('tags.view'))
            <li class="menu-title mt-2">التاغات</li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.tags.index') }}">
                         <span class="nav-icon">
                              <iconify-icon icon="solar:tag-bold-duotone"></iconify-icon>
                         </span>
                    <span class="nav-text"> إدارة التاغات </span>
                </a>
            </li>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->can('inventory.products.view') || auth()->user()->can('inventory.categories.view') || auth()->user()->can('inventory.suppliers.view'))
            <li class="menu-title mt-2">المخزون</li>

            @if(auth()->user()->isAdmin() || auth()->user()->can('inventory.products.view'))
            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarInventoryProducts" data-bs-toggle="collapse" role="button"
                   aria-expanded="false" aria-controls="sidebarInventoryProducts">
                         <span class="nav-icon">
                              <iconify-icon icon="solar:box-bold-duotone"></iconify-icon>
                         </span>
                    <span class="nav-text"> المنتجات </span>
                </a>
                <div class="collapse" id="sidebarInventoryProducts">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('inventory.products.index') }}">القائمة</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('inventory.products.grid') }}">الشبكة</a>
                        </li>
                        @if(auth()->user()->isAdmin() || auth()->user()->can('inventory.products.create'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('inventory.products.create') }}">إنشاء</a>
                        </li>
                        @endif
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('inventory.products.low-stock') }}">مخزون منخفض</a>
                        </li>
                    </ul>
                </div>
            </li>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->can('inventory.categories.view'))
            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarInventoryCategories" data-bs-toggle="collapse" role="button"
                   aria-expanded="false" aria-controls="sidebarInventoryCategories">
                         <span class="nav-icon">
                              <iconify-icon icon="solar:clipboard-list-bold-duotone"></iconify-icon>
                         </span>
                    <span class="nav-text"> الفئات </span>
                </a>
                <div class="collapse" id="sidebarInventoryCategories">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('inventory.categories.index') }}">القائمة</a>
                        </li>
                        @if(auth()->user()->isAdmin() || auth()->user()->can('inventory.categories.create'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('inventory.categories.create') }}">إنشاء</a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->can('inventory.suppliers.view'))
            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarInventorySuppliers" data-bs-toggle="collapse" role="button"
                   aria-expanded="false" aria-controls="sidebarInventorySuppliers">
                         <span class="nav-icon">
                              <iconify-icon icon="solar:users-group-two-rounded-bold-duotone"></iconify-icon>
                         </span>
                    <span class="nav-text"> الموردين </span>
                </a>
                <div class="collapse" id="sidebarInventorySuppliers">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('inventory.suppliers.index') }}">القائمة</a>
                        </li>
                        @if(auth()->user()->isAdmin() || auth()->user()->can('inventory.suppliers.create'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('inventory.suppliers.create') }}">إنشاء</a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->can('users.view') || auth()->user()->can('roles.view') || auth()->user()->can('permissions.view') || auth()->user()->can('representatives.view'))
            <li class="menu-title mt-2">المستخدمين</li>

            @if(auth()->user()->isAdmin() || auth()->user()->can('roles.view'))
            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarRoles" data-bs-toggle="collapse" role="button"
                   aria-expanded="false" aria-controls="sidebarRoles">
                         <span class="nav-icon">
                              <iconify-icon icon="solar:user-speak-rounded-bold-duotone"></iconify-icon>
                         </span>
                    <span class="nav-text"> الأدوار </span>
                </a>
                <div class="collapse" id="sidebarRoles">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('users.role.list')}}">القائمة</a>
                        </li>
                        @if(auth()->user()->isAdmin() || auth()->user()->can('roles.create'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('users.role.create')}}">إنشاء</a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->can('permissions.view'))
            <li class="nav-item">
                <a class="nav-link" href="{{ route('users.pages-permission')}}">
                         <span class="nav-icon">
                              <iconify-icon icon="solar:checklist-minimalistic-bold-duotone"></iconify-icon>
                         </span>
                    <span class="nav-text"> الصلاحيات </span>
                </a>
            </li>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->can('admin.access'))
            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarEmployeeTypes" data-bs-toggle="collapse" role="button"
                   aria-expanded="false" aria-controls="sidebarEmployeeTypes">
                         <span class="nav-icon">
                              <iconify-icon icon="solar:user-id-bold-duotone"></iconify-icon>
                         </span>
                    <span class="nav-text"> أنواع الموظفين </span>
                </a>
                <div class="collapse" id="sidebarEmployeeTypes">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.employee-types.index') }}">القائمة</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.employee-types.create') }}">إنشاء</a>
                        </li>
                    </ul>
                </div>
            </li>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->can('users.view'))
            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarUsers" data-bs-toggle="collapse" role="button"
                   aria-expanded="false" aria-controls="sidebarUsers">
                         <span class="nav-icon">
                              <iconify-icon icon="solar:user-bold-duotone"></iconify-icon>
                         </span>
                    <span class="nav-text"> المديرين والموظفين </span>
                </a>
                <div class="collapse" id="sidebarUsers">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('users.users.list') }}">القائمة</a>
                        </li>
                        @if(auth()->user()->isAdmin() || auth()->user()->can('users.create'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.users.create') }}">إنشاء</a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->can('representatives.view'))
            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarRepresentatives" data-bs-toggle="collapse" role="button"
                   aria-expanded="false" aria-controls="sidebarRepresentatives">
                         <span class="nav-icon">
                              <iconify-icon icon="solar:user-speak-rounded-bold-duotone"></iconify-icon>
                         </span>
                    <span class="nav-text"> المندوبين </span>
                </a>
                <div class="collapse" id="sidebarRepresentatives">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.representatives.index') }}">القائمة</a>
                        </li>
                        @if(auth()->user()->isAdmin() || auth()->user()->can('representatives.create'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.representatives.create') }}">إنشاء</a>
                        </li>
                        @endif
                        @if(auth()->user()->isAdmin() || auth()->user()->can('admin.access'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.accounts.index') }}">الحسابات</a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif
            @endif

        </ul>
    </div>
</div>
