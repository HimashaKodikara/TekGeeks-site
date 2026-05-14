<!DOCTYPE html>
<html lang="en" data-bs-theme="light" class="set-nav-dark">

<head>
    @include('layouts.partials/app-meta-title')
    {{-- @vite(['resources/js/app.js']) --}}

    @include('layouts.partials/app-head-css')
</head>

<body>
<div class="app-wrap">
    @include('layouts.partials/app-header')

    @include('layouts.partials/app-sidebar')

    <main class="app-body">

        <div class="app-content">
            <div class="content-wrapper">

                @yield('page-title')

                <div class="main-content {{$extraClass ?? ''}}">

                    @yield('content')

                </div>

            </div>

            @yield('extra-content')

        </div>

        @include('layouts.partials/app-footer')

    </main>

    @include('layouts.partials/app-drawer')

    @include('layouts.partials/app-settings')

</div>
{{-- @vite(['resources/js/app.js']) --}}
@include('layouts.partials/app-scripts')
</body>

</html>
