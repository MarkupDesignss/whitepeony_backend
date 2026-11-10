<!doctype html>
<html lang="en">
<!--begin::Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>@yield('title')</title>
    <!--begin::Primary Meta Tags-->
    <link rel="icon" href="{{ asset('assets/admin/images/logo.png') }}" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="author" content="ColorlibHQ" />
    <meta name="description"
        content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS." />
    <meta name="keywords"
        content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard" />


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!--end::Primary Meta Tags-->
    <!--begin::Fonts-->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        integrity="sha256-9kPW/n5nn53j4WMRYAxe9c1rCY96Oogo/MKSVdKzPmI=" crossorigin="anonymous" />
    <link rel="stylesheet" href="{{asset('assets/admin/css/adminlte.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/css/custom.css')}}">


    <style>
    .sidebar-brand {
        height: 7.5rem;
        padding-top: 0rem;
    }

    .sidebar-brand .brand-link .brand-image {
        max-height: unset;
    }

    /* .app-main{
            background: #b5a78cc4;
        }
        .app-footer{
            background: #c5baa6;
        } */
    /* Custom styling for better visual feedback */
    .app-sidebar .nav-item.menu-open>.nav-link {
        background-color: rgba(255, 255, 255, 0.1);
        border-left: 3px solid #fff;
    }

    .app-sidebar .nav-link.active {
        background-color: rgba(255, 255, 255, 0.2) !important;
        font-weight: 600;
        position: relative;
        /* Highlight bar on the left for active item */
        border-left: 4px solid #0d6efd;
        color: #0d6efd !important;
    }

    .app-sidebar .nav-link.active>i.nav-icon {
        color: #0d6efd !important;
    }

    .app-sidebar .nav-link.active::after {
        content: '●';
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #0d6efd;
        font-size: 0.8rem;
        opacity: 0.7;
    }

    .app-sidebar .nav-treeview .nav-link.active {
        background-color: rgba(255, 255, 255, 0.15) !important;
    }

    .app-sidebar .nav-treeview .nav-link {
        padding-left: 2rem !important;
    }

    .app-sidebar .nav-treeview .nav-link i.nav-icon {
        font-size: 0.6rem;
        margin-right: 0.5rem;
    }

    .app-sidebar .nav-item>.nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    /* Sticky sidebar and scrollable sidebar content */
    .app-sidebar {
        position: sticky !important;
        top: 0;
        height: 100vh;
        z-index: 1020;
    }

    .sidebar-wrapper {
        overflow-y: auto;
        max-height: calc(100vh - 150px);
    }

    .navbar-nav.flex-row>.nav-item {
        display: flex;
        align-items: center;
    }

    .dropdown-menu-lg {
        min-width: 220px;
    }

    .user-header {
        border-radius: 12px 12px 0 0;
    }

    .user-footer {
        border-radius: 0 0 12px 12px;
    }

    /* Chart card improvements */
    .card .apexcharts-canvas {
        border-radius: 14px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.07);
        background: #fff;
        padding: 12px;
    }

    .modal-backdrop.show {
        z-index: 1100;
        background: rgba(44, 62, 80, 0.15);
    }

    .modal.fade {
        z-index: 1200;
    }

    /* Smooth toggle styles */
    :root {
        --sidebar-width: 250px;
        --sidebar-anim: 300ms;
    }

    .app-sidebar {
        transition: transform var(--sidebar-anim) ease, box-shadow var(--sidebar-anim) ease;
        will-change: transform;
    }

    /* Desktop: collapsed (hidden) */
    .app-wrapper.sidebar-collapsed .app-sidebar {
        transform: translateX(-260px);
        box-shadow: none;
    }

    /* Desktop: when collapsed, remove left spacing if you use content margins */
    .app-wrapper.sidebar-collapsed .content-wrapper,
    .app-wrapper.sidebar-collapsed main {
        margin-left: 0 !important;
    }

    /* Mobile: default sidebar hidden via transform; .sidebar-open shows it */
    @media (max-width: 991.98px) {
        .app-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1050;
            transform: translateX(-110%);
            width: var(--sidebar-width);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }

        .app-wrapper.sidebar-open .app-sidebar {
            transform: translateX(0);
        }
    }

    /* Overlay (mobile) */
    .sidebar-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.45);
        opacity: 0;
        visibility: hidden;
        transition: opacity 180ms ease, visibility 180ms ease;
        z-index: 1040;
    }

    .app-wrapper.sidebar-open .sidebar-overlay {
        opacity: 1;
        visibility: visible;
    }

    /* Add smooth squeeze behavior */
    :root {
        --sidebar-width: 250px;
        --sidebar-mini-width: 80px;
        --sidebar-anim: 280ms;
    }

    /* Sidebar transitions (existing rules keep) */
    .app-sidebar {
        width: var(--sidebar-width);
        transition: width var(--sidebar-anim) ease, transform var(--sidebar-anim) ease, box-shadow var(--sidebar-anim) ease;
        will-change: width, transform;
    }

    /* Content area(s) that should "squeeze" — target common wrappers */
    .app-main,
    .content-wrapper,
    main,
    .main-content,
    .page-content {
        transition: margin-left var(--sidebar-anim) ease, width var(--sidebar-anim) ease;
        margin-left: var(--sidebar-width);
        will-change: margin-left, width;
    }

    /* Desktop collapsed (mini) state: smaller sidebar and content margin reduced */
    .app-wrapper.sidebar-collapsed .app-sidebar {
        width: var(--sidebar-mini-width);
        /* keep position; don't slide offscreen on desktop */
        transform: translateX(0);
    }

    .app-wrapper.sidebar-collapsed .app-main,
    .app-wrapper.sidebar-collapsed .content-wrapper,
    .app-wrapper.sidebar-collapsed main,
    .app-wrapper.sidebar-collapsed .main-content,
    .app-wrapper.sidebar-collapsed .page-content {
        margin-left: var(--sidebar-mini-width);
    }

    /* Mobile: sidebar slides over content; content does not squeeze on mobile */
    @media (max-width: 991.98px) {
        .app-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            transform: translateX(-110%);
            width: var(--sidebar-width);
            z-index: 1050;
        }

        .app-wrapper.sidebar-open .app-sidebar {
            transform: translateX(0);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }

        /* prevent content margin shift on mobile; overlay will be used */
        .app-main,
        .content-wrapper,
        main,
        .main-content,
        .page-content {
            margin-left: 0 !important;
        }
    }

    /* Overlay for mobile */
    .sidebar-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.45);
        opacity: 0;
        visibility: hidden;
        transition: opacity 180ms ease, visibility 180ms ease;
        z-index: 1040;
    }

    .app-wrapper.sidebar-open .sidebar-overlay {
        opacity: 1;
        visibility: visible;
    }
    </style>
</head>
<!--end::Head-->
<!--begin::Body-->

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
        <!--begin::Header-->
        <nav class="app-header navbar navbar-expand shadow-sm">
            <div class="container-fluid">
                <div class="d-flex align-items-center w-100 justify-content-between">

                    <!-- Left side links -->
                    <ul class="navbar-nav flex-row align-items-center gap-2 mb-0">
                        <li class="nav-item d-lg-none">
                            <button id="sidebarToggle" class="nav-link btn btn-sm btn-light px-2"
                                aria-label="Toggle sidebar" title="Toggle sidebar">
                                <i class="bi bi-list fs-4"></i>
                            </button>
                        </li>
                        <!-- Language Selector -->
                        {{-- <li class="nav-item">
                            <form method="post" action="#" id="language-form" class="mb-0">
                                @csrf
                                <select class="form-select form-select-sm bg-white bg-opacity-25 border-0" 
                                        name="language" 
                                        onchange="document.getElementById('language-form').submit()"
                                        style="appearance: none; -webkit-appearance: none; -moz-appearance: none; cursor: pointer; min-width: 110px;">
                                    <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>English
                        </option>
                        <option value="es" {{ app()->getLocale() == 'es' ? 'selected' : '' }}>Español</option>
                        <option value="fr" {{ app()->getLocale() == 'fr' ? 'selected' : '' }}>Français</option>
                        </select>
                        </form>
                        </li> --}}
                    </ul>

                    <!-- Right side links -->
                    <ul class="navbar-nav flex-row align-items-center gap-2 ms-auto mb-0">

                        <!-- Fullscreen Toggle -->
                        <!--<li class="nav-item">-->
                        <!--    <a class="nav-link px-2" href="#" data-lte-toggle="fullscreen" title="Toggle Fullscreen">-->
                        <!--        <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen fs-5"></i>-->
                        <!--        <i data-lte-icon="minimize" class="bi bi-fullscreen-exit fs-5" style="display: none;"></i>-->
                        <!--    </a>-->
                        <!--</li>-->

                        <!-- User Menu Dropdown -->
                        <li class="nav-item dropdown user-menu">
                            <a href="#" class="nav-link dropdown-toggle d-flex align-items-center px-2"
                                data-bs-toggle="dropdown">
                                <div class="position-relative">
                                    <img src="{{ asset('assets/admin/images/admin.jpg') }}"
                                        class="user-image rounded-circle shadow border border-2 border-white"
                                        alt="User Image" style="width: 36px; height: 36px; object-fit: cover;">
                                    <span
                                        class="position-absolute bottom-0 end-0 bg-success rounded-circle border border-2 border-white"
                                        style="width: 10px; height: 10px;"></span>
                                </div>
                                <span class="d-none d-md-inline ms-2">{{ auth()->user()->name }}</span>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end shadow-lg border-0 p-0"
                                style="min-width: 220px;">
                                <!-- User Header -->
                                <li class="user-header bg-gradient-primary text-center py-4 rounded-top">
                                    <img src="{{ asset('assets/admin/images/admin.jpg') }}"
                                        class="rounded-circle shadow border border-3 border-white mb-2" alt="User Image"
                                        style="width: 80px; height: 80px; object-fit: cover;">
                                    <p class="mb-0 fw-bold">{{ auth()->user()->name }}</p>
                                    <small class="opacity-75">Administrator</small>
                                </li>

                                <!-- Menu Footer -->
                                <li
                                    class="user-footer d-flex justify-content-between px-3 py-2 rounded-bottom bg-light">
                                    <a href="#" class="btn btn-outline-primary btn-sm rounded-pill"
                                        data-bs-toggle="modal" data-bs-target="#profileModal">
                                        Profile
                                    </a>
                                    <a href="javascript:void(0);" id="twoFaBtn"
                                        class="btn btn-outline-success btn-sm rounded-pill"
                                        onclick="toggle2FA({{ auth()->user()->id }})">
                                        {{ auth()->user()->two_factor_enabled ? '2FA Enabled' : '2FA Disabled' }}
                                    </a>
                                    <a href="{{ route('admin.logout') }}"
                                        class="btn btn-outline-danger btn-sm rounded-pill">
                                        <i class="bi bi-box-arrow-right me-1"></i> Sign out
                                    </a>
                                </li>
                            </ul>
                        </li>

                    </ul>
                </div>
            </div>
        </nav>

        <!--end::Header-->
        <!--begin::Sidebar-->
        <aside id="appSidebar" class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark"
            style="background:#343a40 !important; position: sticky; top: 0; height: 100vh;">
            <!-- Sidebar Brand -->
            <div class="sidebar-brand top-3 mt-2" style="background: #343a40; ">
                <a href="{{ route('admin.dashboard') }}" class="brand-link w-100"
                    style="display:flex;flex-direction:column;justify-content:center;align-items:center;">
                    <img src="{{ asset('assets/admin/images/logo.png') }}"
                        style="width: 30%; height:50px; opacity:0.95;border-radius:50%; filter:brightness(0.8);"
                        alt="Admin Logo" class="brand-image shadow">
                    <h3 class=" text-center text-white fw-bolder">White Peony</h3>
                </a>
            </div>

            <!-- Sidebar Wrapper -->
            <div class="sidebar-wrapper" style="overflow-y: auto; max-height: calc(100vh - 150px);">
                <nav class="mt-2">
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu"
                        data-accordion="false">

                        <!-- Dashboard -->
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}"
                                class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-speedometer2"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <!-- Category Management -->
                        <li class="nav-item">
                            <a href="{{ route('admin.categories.index') }}"
                                class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-bar-chart-line"></i>
                                <p>Category Management</p>
                            </a>
                        </li>

                        <!-- Product Management -->
                        <li class="nav-item">
                            <a href="#" class="nav-link" data-bs-toggle="collapse" data-bs-target="#productMenu"
                                role="button"
                                aria-expanded="{{ request()->routeIs('admin.products.*') ? 'true' : 'false' }}"
                                aria-controls="productMenu">
                                <i class="nav-icon bi bi-box-seam"></i>
                                <p>
                                    Product Management
                                    <i class="right bi bi-caret-down-fill"></i>
                                </p>
                            </a>
                            <ul class="collapse nav {{ request()->routeIs('admin.products.*') ? 'show' : '' }}"
                                id="productMenu">
                                <li class="nav-item">
                                    <a href="{{ route('admin.products.b2b.index') }}"
                                        class="nav-link {{ request()->routeIs('admin.products.b2b.*') ? 'active' : '' }}">
                                        <p>B2B Products</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.products.b2c.index') }}"
                                        class="nav-link {{ request()->routeIs('admin.products.b2c.*') ? 'active' : '' }}">
                                        <p>B2C Products</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.products.index') }}"
                                        class="nav-link {{ request()->routeIs('admin.products.index') ? 'active' : '' }}">
                                        <p>All Products</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Banners -->
                        <li class="nav-item">
                            <a href="{{ route('admin.banners.index') }}"
                                class="nav-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-card-image"></i>
                                <p>Banners</p>
                            </a>
                        </li>

                        <!-- Articles -->
                        <li class="nav-item">
                            <a href="{{ route('admin.articles.index') }}"
                                class="nav-link {{ request()->routeIs('admin.articles.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-card-image"></i>
                                <p>Articles</p>
                            </a>
                        </li>



                        <!-- Orders -->
                        <li class="nav-item">
                            <a href="{{ route('admin.orders.index') }}"
                                class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-cart-check"></i>
                                <p>Order Management</p>
                            </a>
                        </li>


                        <!-- Dashboard -->
                        <li class="nav-item">
                            <a href="{{ route('admin.bestsellingproduct') }}"
                                class="nav-link {{ request()->routeIs('admin.bestsellingproduct') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-speedometer2"></i>
                                <p>Best Selling Product</p>
                            </a>
                        </li>

                        <!-- Big Sale -->
                        <li class="nav-item">
                            <a href="{{ route('admin.big_sales.index') }}"
                                class="nav-link {{ request()->routeIs('admin.big_sales.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-tags"></i>
                                <p>News</p>
                            </a>
                        </li>

                        <!-- Feature Products -->
                        <li class="nav-item">
                            <a href="{{ route('admin.feature_products.index') }}"
                                class="nav-link {{ request()->routeIs('admin.feature_products.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-star"></i>
                                <p>Feature Products</p>
                            </a>
                        </li>

                        <!-- Promotions -->
                        <li class="nav-item">
                            <a href="{{ route('admin.promocodes.index') }}"
                                class="nav-link {{ request()->routeIs('admin.promocodes.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-megaphone"></i>
                                <p>Promotion Management</p>
                            </a>
                        </li>

                        <!-- Customer Management -->
                        <li class="nav-item">
                            <a href="#" class="nav-link" data-bs-toggle="collapse" data-bs-target="#customerMenu"
                                role="button"
                                aria-expanded="{{ request()->routeIs('admin.customers.*') ? 'true' : 'false' }}"
                                aria-controls="customerMenu">
                                <i class="nav-icon bi bi-people"></i>
                                <p>
                                    User Management
                                    <i class="right bi bi-chevron-down"></i>
                                </p>
                            </a>
                            <ul class="collapse nav flex-column ms-3 {{ request()->routeIs('admin.customers.*') ? 'show' : '' }}"
                                id="customerMenu">
                                <li class="nav-item">
                                    <a href="{{ route('admin.customers.b2b') }}"
                                        class="nav-link {{ request()->routeIs('admin.customers.b2b') ? 'active' : '' }}">B2B</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.customers.b2c') }}"
                                        class="nav-link {{ request()->routeIs('admin.customers.b2c') ? 'active' : '' }}">B2C</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.customers.create') }}"
                                        class="nav-link {{ request()->routeIs('admin.customers.create') ? 'active' : '' }}">
                                        <i class="bi bi-plus-circle me-1"></i> Add B2B
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Events -->
                        <li class="nav-item">
                            <a href="{{ route('admin.events.index') }}"
                                class="nav-link {{ request()->routeIs('admin.events.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-calendar-event"></i>
                                <p>Event Management</p>
                            </a>
                        </li>

                        <!-- Pages -->
                        <li class="nav-item">
                            <a href="{{ route('admin.pages.index') }}"
                                class="nav-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-calendar-event"></i>
                                <p>Pages</p>
                            </a>
                        </li>

                        <!-- Analytics & Reports -->
                        <li class="nav-item {{ request()->routeIs('admin.analytics.*') ? 'menu-open' : '' }}">
                            <a href="{{route('admin.analytics.index')}}"
                                class="nav-link {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-bar-chart-line"></i>
                                <p>
                                    Analytics & Reports
                                </p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- mobile overlay -->
        <div id="sidebarOverlay" class="sidebar-overlay"></div>


        <!--end::Sidebar-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
        function toggle2FA(userId) {
            console.log(userId);

            fetch(`/admin/toggle-2fa/${userId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({})
                })
                .then(response => {
                    if (!response.ok) throw new Error("Network response was not OK");
                    console.log("no data");

                    return response.json();
                })
                .then(data => {
                    const btn = document.getElementById('twoFaBtn');
                    console.log(data);

                    if (data.two_factor_enabled) {
                        btn.textContent = '2FA Enabled';
                        btn.classList.remove('btn-outline-success');
                        btn.classList.add('btn-success');
                    } else {
                        btn.textContent = '2FA Disabled';
                        btn.classList.remove('btn-success');
                        btn.classList.add('btn-outline-success');
                    }
                })
                .catch(err => console.error("Error:", err));
        }
        </script>

        <!-- Profile Modal -->
        <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 18px;">
                    <div class="modal-header bg-gradient-primary text-white" style="border-radius: 18px 18px 0 0;">
                        <h5 class="modal-title fw-bold" id="profileModalLabel">
                            <i class="fa fa-user-circle me-2"></i> Your Profile
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 bg-light" style="border-radius: 0 0 18px 18px;">
                        <div class="row g-4">
                            <!-- User Details Card -->
                            <div class="col-md-4">
                                <div class="card shadow-sm h-100 border-0" style="border-radius: 14px;">
                                    <div class="card-body text-center">
                                        <img src="{{ asset('assets/admin/images/admin.jpg') }}"
                                            class="rounded-circle shadow mb-3" alt="User Image"
                                            style="width: 90px; height: 90px; object-fit: cover;">
                                        <h6 class="card-title text-primary mb-2 fw-bold">{{ auth()->user()->name }}</h6>
                                        <p class="mb-1 text-muted"><i class="fa fa-envelope me-1"></i>
                                            {{ auth()->user()->email }}</p>
                                        <span class="badge bg-gradient-success px-3 py-2 mt-2">Administrator</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Profile Update Card -->
                            <div class="col-md-4">
                                <div class="card shadow-sm h-100 border-0" style="border-radius: 14px;">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3 fw-bold text-primary"><i
                                                class="fa fa-edit me-2"></i>Update Profile</h6>
                                        <form action="{{ route('admin.updateProfile') }}" method="POST">

                                            @csrf
                                            <div class="mb-3">
                                                <label for="name" class="form-label fw-semibold">Name</label>
                                                <input type="text" name="name" id="name" class="form-control"
                                                    value="{{ auth()->user()->name }}" required>
                                            </div>
                                            <button type="submit" class="btn btn-success w-100 fw-bold">
                                                <i class="fa fa-save me-1"></i> Update Profile
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- Change Password Card -->
                            <div class="col-md-4">
                                <div class="card shadow-sm h-100 border-0" style="border-radius: 14px;">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3 fw-bold text-primary"><i
                                                class="fa fa-key me-2"></i>Change Password</h6>
                                        <form action="{{ route('admin.changePassword') }}" method="POST">

                                            @csrf
                                            <div class="mb-3">
                                                <label for="current_password" class="form-label fw-semibold">Current
                                                    Password</label>
                                                <input type="password" name="current_password" id="current_password"
                                                    class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="new_password" class="form-label fw-semibold">New
                                                    Password</label>
                                                <input type="password" name="new_password" id="new_password"
                                                    class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="new_password_confirmation"
                                                    class="form-label fw-semibold">Confirm Password</label>
                                                <input type="password" name="new_password_confirmation"
                                                    id="new_password_confirmation" class="form-control" required>
                                            </div>
                                            <button type="submit" class="btn btn-warning w-100 fw-bold">
                                                <i class="fa fa-lock me-1"></i> Change Password
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
        (function() {
            const wrapper = document.querySelector('.app-wrapper');
            const toggle = document.getElementById('sidebarToggle');
            const overlay = document.getElementById('sidebarOverlay');

            function isMobile() {
                return window.innerWidth <= 991.98;
            }

            // Initialize state from localStorage if present
            const saved = localStorage.getItem('sidebar_state'); // 'expanded'|'collapsed'
            if (saved === 'collapsed' && !isMobile()) {
                wrapper.classList.add('sidebar-collapsed');
            }

            // Toggle behavior: desktop squeezes content, mobile slides overlay
            if (toggle) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (isMobile()) {
                        // mobile: open/close overlay sidebar
                        const opening = !wrapper.classList.contains('sidebar-open');
                        if (opening) {
                            wrapper.classList.add('sidebar-open');
                            // show overlay via class; overlay visibility handled by CSS
                        } else {
                            wrapper.classList.remove('sidebar-open');
                        }
                        // do not persist mobile open state
                    } else {
                        // desktop: toggle collapsed (squeezed)
                        const collapsed = wrapper.classList.toggle('sidebar-collapsed');
                        // persist desktop preference
                        localStorage.setItem('sidebar_state', collapsed ? 'collapsed' : 'expanded');
                        // ensure mobile class removed
                        wrapper.classList.remove('sidebar-open');
                    }
                });
            }

            // Overlay click closes on mobile
            if (overlay) {
                overlay.addEventListener('click', function() {
                    wrapper.classList.remove('sidebar-open');
                });
            }

            // Close mobile sidebar on ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    wrapper.classList.remove('sidebar-open');
                }
            });

            // On resize: if moving to desktop, remove mobile open class; apply saved desktop state
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    if (!isMobile()) {
                        wrapper.classList.remove('sidebar-open');
                        const pref = localStorage.getItem('sidebar_state');
                        if (pref === 'collapsed') wrapper.classList.add('sidebar-collapsed');
                        else wrapper.classList.remove('sidebar-collapsed');
                    } else {
                        // on mobile, ensure sidebar starts hidden
                        wrapper.classList.remove('sidebar-collapsed');
                    }
                }, 120);
            });
        })();
        </script>