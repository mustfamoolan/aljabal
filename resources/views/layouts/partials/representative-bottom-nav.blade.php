@php
    $currentRoute = request()->route()->getName();
@endphp
<nav class="representative-bottom-nav">
    <div class="bottom-nav-container">
        <a href="{{ route('representative.dashboard') }}" class="bottom-nav-item {{ $currentRoute === 'representative.dashboard' ? 'active' : '' }}" data-route="representative.dashboard">
            <div class="bottom-nav-icon">
                <iconify-icon icon="solar:home-2-bold-duotone" class="fs-24"></iconify-icon>
            </div>
            <span class="bottom-nav-label">الرئيسية</span>
        </a>
        <a href="#" class="bottom-nav-item" data-route="representative.chat">
            <div class="bottom-nav-icon">
                <iconify-icon icon="solar:chat-round-bold-duotone" class="fs-24"></iconify-icon>
            </div>
            <span class="bottom-nav-label">المحادثة</span>
        </a>
        <a href="#" class="bottom-nav-item" data-route="representative.menu" id="bottom-nav-menu-toggle">
            <div class="bottom-nav-icon">
                <iconify-icon icon="solar:menu-dots-circle-bold-duotone" class="fs-24"></iconify-icon>
            </div>
            <span class="bottom-nav-label">القائمة</span>
        </a>
        <a href="#" class="bottom-nav-item" data-route="representative.orders">
            <div class="bottom-nav-icon">
                <iconify-icon icon="solar:document-text-bold-duotone" class="fs-24"></iconify-icon>
            </div>
            <span class="bottom-nav-label">المستندات</span>
        </a>
        <a href="{{ route('representative.profile') }}" class="bottom-nav-item {{ $currentRoute === 'representative.profile' ? 'active' : '' }}" data-route="representative.profile">
            <div class="bottom-nav-icon">
                <iconify-icon icon="solar:user-bold-duotone" class="fs-24"></iconify-icon>
            </div>
            <span class="bottom-nav-label">الملف الشخصي</span>
        </a>
    </div>
</nav>

