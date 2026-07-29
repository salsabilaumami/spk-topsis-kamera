<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') · {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body>
<div class="app-shell">
    <aside class="sidebar d-none d-lg-block">
        <div class="sidebar-inner d-flex flex-column h-100">
            <a href="{{ route('dashboard') }}" class="brand text-decoration-none">
                <span class="brand-mark">
                    <i class="bi bi-camera-fill"></i>
                </span>

                <span>
                    <strong>SPK Kamera</strong>
                    <small>Metode TOPSIS</small>
                </span>
            </a>

            <div class="sidebar-nav-wrapper flex-grow-1 overflow-auto pe-1">
                @include('partials.navigation')
            </div>

            <div class="sidebar-footer mt-3">
                <div class="small text-white-50">
                    Masuk sebagai
                </div>

                <div class="fw-semibold text-white text-truncate">
                    {{ auth()->user()->name }}
                </div>

                <div class="small text-white-50 text-truncate mb-3">
                    {{ auth()->user()->email }}
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit" class="btn btn-light btn-sm w-100">
                        <i class="bi bi-box-arrow-right me-1"></i>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="main-area">
        <header class="topbar">
            <div class="d-flex align-items-center gap-2">
                <button
                    type="button"
                    class="btn btn-light d-lg-none"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#mobileSidebar"
                    aria-controls="mobileSidebar"
                    aria-label="Buka menu navigasi"
                >
                    <i class="bi bi-list fs-4"></i>
                </button>

                <div>
                    <h1 class="page-title">
                        @yield('page-title', 'Dashboard')
                    </h1>

                    <div class="page-subtitle">
                        @yield(
                            'page-subtitle',
                            'Sistem Pendukung Keputusan Pemilihan Kamera Mirrorless Terbaik untuk Fotografer Pemula'
                        )
                    </div>
                </div>
            </div>

            <div class="ms-auto d-flex align-items-center gap-2 gap-md-3">
                <span class="date-pill d-none d-md-inline-flex align-items-center">
                    <i class="bi bi-calendar3 me-1"></i>
                    {{ now()->translatedFormat('d F Y') }}
                </span>

                <div class="dropdown">
                    <button
                        type="button"
                        class="btn account-toggle dropdown-toggle"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        <span class="account-avatar">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>

                        <span class="d-none d-sm-flex flex-column text-start lh-sm">
                            <strong class="account-name">
                                {{ auth()->user()->name }}
                            </strong>

                            <small class="account-role">
                                Administrator
                            </small>
                        </span>
                    </button>

                    <div class="dropdown-menu dropdown-menu-end account-dropdown shadow-sm border-0 p-2">
                        <div class="px-2 py-2 border-bottom mb-2">
                            <div class="fw-bold text-dark">
                                {{ auth()->user()->name }}
                            </div>

                            <div class="small text-secondary text-break">
                                {{ auth()->user()->email }}
                            </div>
                        </div>

                        <a class="dropdown-item rounded-3" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2 me-2"></i>
                            Dashboard
                        </a>

                        <a class="dropdown-item rounded-3" href="{{ route('methodology') }}">
                            <i class="bi bi-journal-text me-2"></i>
                            Metode TOPSIS
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="mt-2">
                            @csrf

                            <button type="submit" class="dropdown-item rounded-3 text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="content-area">
            @include('partials.flash')

            @yield('content')
        </main>

        <footer class="app-footer">
            SPK Pemilihan Kamera Mirrorless · Laravel &amp; TOPSIS
        </footer>
    </div>
</div>

<div
    class="offcanvas offcanvas-start mobile-sidebar"
    tabindex="-1"
    id="mobileSidebar"
    aria-labelledby="mobileSidebarLabel"
>
    <div class="offcanvas-header border-bottom border-light border-opacity-25">
        <a
            href="{{ route('dashboard') }}"
            class="brand text-decoration-none mb-0"
            id="mobileSidebarLabel"
        >
            <span class="brand-mark">
                <i class="bi bi-camera-fill"></i>
            </span>

            <span>
                <strong>SPK Kamera</strong>
                <small>Metode TOPSIS</small>
            </span>
        </a>

        <button
            type="button"
            class="btn-close btn-close-white"
            data-bs-dismiss="offcanvas"
            aria-label="Tutup menu"
        ></button>
    </div>

    <div class="offcanvas-body d-flex flex-column p-3">
        <div class="mobile-account-card mb-3">
            <div class="small text-white-50">
                Masuk sebagai
            </div>

            <div class="fw-semibold text-white">
                {{ auth()->user()->name }}
            </div>

            <div class="small text-white-50 text-truncate">
                {{ auth()->user()->email }}
            </div>
        </div>

        <div class="flex-grow-1 overflow-auto pe-1">
            @include('partials.navigation')
        </div>

        <form
            method="POST"
            action="{{ route('logout') }}"
            class="mt-3 pt-3 border-top border-light border-opacity-25"
        >
            @csrf

            <button type="submit" class="btn btn-light w-100">
                <i class="bi bi-box-arrow-right me-1"></i>
                Logout
            </button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>

@stack('scripts')
</body>
</html>