<header class="app-header">
    <!-- Collapse icon -->
    <div class="d-flex flex-grow-1 w-100 me-auto align-items-center">

        <!-- App logo -->
        @include('layouts.partials/app-logo')

        <button class="mobile-menu-icon me-2 d-flex d-sm-flex d-md-flex d-lg-none flex-shrink-0"
                data-action="toggle-swap" data-toggleclass="app-mobile-menu-open" aria-label="Toggle Mobile Menu">
            <svg class="sa-icon">
                <use href="/icons/sprite.svg#menu"></use>
            </svg>
        </button>

        <!-- Collapse icon -->
        <button type="button" class="collapse-icon me-3 d-none d-lg-inline-flex d-xl-inline-flex d-xxl-inline-flex"
                data-action="toggle" data-class="set-nav-minified" aria-label="Toggle Navigation Size">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 5 8">
                <polygon fill="#878787" points="4.5,1 3.8,0.2 0,4 3.8,7.8 4.5,7 1.5,4"/>
            </svg>
        </button>
    </div>

    <!-- Notifications dropdown -->
    @include('layouts.partials/app-notifications')

    <!-- Profile -->
    <button type="button" data-bs-toggle="dropdown" class="btn-system bg-transparent d-flex align-items-center px-3">
        <div class="text-end me-3 d-none d-md-block">
            <span class="text-truncate text-truncate-md opacity-80 fs-sm">{{ Auth::user()->name }}</span>
            {{-- <small class="text-truncate text-truncate-md opacity-80 fs-sm">{{ Auth::user()->email }}</small> --}}
        </div>
        <img src="{{url('/img/demo/avatars/avatar-admin.png')}}" class="profile-image profile-image-md rounded-circle" alt="User">
    </button>

    <!-- Profile dropdown -->
    <div class="dropdown-menu dropdown-menu-animated">
        <div class="notification-header rounded-top mb-2">
            <div class="d-flex flex-row align-items-center mt-1 mb-1 color-white">
                <span class="status status-success d-inline-block me-2">
                    <img src="{{url('/img/demo/avatars/avatar-admin.png')}}" class="profile-image rounded-circle" alt="{{ Auth::user()->name }}">
                </span>
                <div class="info-card-text">
                    <div class="fs-lg text-truncate text-truncate-lg">{{ Auth::user()->name }}</div>
                    <span class="text-truncate text-truncate-md opacity-80 fs-sm">{{ Auth::user()->email }}</span>
                </div>
            </div>
        </div>
        

        <div class="dropdown-divider m-0"></div>

        <a href="#" class="dropdown-item" data-action="app-reset" role="button">
            <span data-i18n="drpdwn.reset_layout">Reset Layout</span>
        </a>
        <a href="#" class="dropdown-item" data-action="toggle-swap" data-toggleclass="open"
           data-target="aside.js-drawer-settings" role="button">
            <span data-i18n="drpdwn.settings">Settings</span>
        </a>
        <a href="#" class="dropdown-item d-block d-sm-block d-md-block d-lg-none" data-action="toggle-swap"
           data-toggleclass="open" data-target="aside.js-app-drawer" role="button">
            <span data-i18n="drpdwn.settings">Virtual Assistant</span>
        </a>

        <div class="dropdown-divider m-0"></div>



        <div class="dropdown-divider m-0"></div>

        <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="dropdown-item py-3 fw-500 d-flex justify-content-between">
            <span class="text-danger" data-i18n="drpdwn.page-logout">Logout</span>
        </button>
        </form>
    </div>
    
</header>
