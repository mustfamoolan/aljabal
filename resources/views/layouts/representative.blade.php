<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.partials/title-meta', ['title' => $title])
    @yield('css')
    @include('layouts.partials/head-css')
</head>

<body>

<div class="wrapper">

    @include("layouts.partials/representative-topbar", ['title' => $title])
    @include('layouts.partials/representative-nav')

    <div class="page-content">

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
