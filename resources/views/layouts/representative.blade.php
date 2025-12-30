<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.partials/title-meta', ['title' => $title])
    @yield('css')
    @include('layouts.partials/head-css')
    <style>
        /* إخفاء السايد بار بالكامل في واجهة المندوبين */
        .main-nav {
            display: none !important;
            width: 0 !important;
            min-width: 0 !important;
        }
        /* إزالة جميع الفراغات من المحتوى */
        .page-content {
            margin-right: 0 !important;
            margin-left: 0 !important;
            padding-right: 0 !important;
            padding-left: 0 !important;
        }
        .wrapper {
            padding-right: 0 !important;
            padding-left: 0 !important;
        }
        /* إزالة padding من topbar */
        .topbar {
            padding-right: 0 !important;
            padding-left: 0 !important;
        }
        .topbar .container-fluid {
            padding-right: 1rem !important;
            padding-left: 1rem !important;
        }
        /* إخفاء زر القائمة وإزالة الفراغ */
        .button-toggle-menu,
        .topbar-item:has(.button-toggle-menu),
        .button-sm-hover {
            display: none !important;
        }
        /* إزالة أي margin أو padding من زر القائمة */
        .navbar-header .d-flex {
            gap: 0 !important;
        }
    </style>
</head>

<body>

<div class="wrapper">

    @include("layouts.partials/representative-topbar", ['title' => $title])

    <div class="page-content" style="margin-right: 0;">

        <div class="container-fluid">
            @yield('content')
        </div>

        @include("layouts.partials/footer")

    </div>

</div>

@include("layouts.partials/right-sidebar")
@include("layouts.partials/footer-scripts")
@include("layouts.partials/delete-confirm-modal")
@include("layouts.partials/representative-bottom-nav")
@vite(['resources/js/app.js','resources/js/layout.js','resources/js/representative-bottom-nav.js'])
@if(auth()->guard('representative')->check())
    @vite(['resources/js/pages/topbar-notifications.js', 'resources/js/pages/notifications.js'])
    @if(config('services.fcm.vapid_key'))
        <meta name="fcm-vapid-key" content="{{ config('services.fcm.vapid_key') }}">
    @else
        <script>
            console.warn('FCM_VAPID_KEY not found in .env file. Please add it to enable push notifications.');
        </script>
    @endif
@endif

</body>

</html>
