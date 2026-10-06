<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Kasir') - {{ $storeSettings->store_name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --ink: #17243a;
            --muted: #738198;
            --line: #e3e9f0;
            --teal: #07536a;
            --teal-soft: #dfeff2;
            --coral: #e96357;
            --coral-strong: #d64c3d;
            --amber: #f7b267;
            --canvas: #f5f8fc;
            --surface: #fff;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            color: var(--ink);
            background: var(--canvas);
            font-family: "Segoe UI", Tahoma, sans-serif;
        }

        .admin-navbar {
            position: sticky;
            top: 0;
            z-index: 1030;
            min-height: 66px;
            background: var(--teal);
            border-bottom: 1px solid #064456;
        }

        .admin-navbar .container-fluid {
            max-width: none;
        }

        .brand-mark {
            display: block;
            width: 44px;
            height: 44px;
            margin-right: 8px;
            border: 2px solid rgba(255,255,255,.85);
            border-radius: 50%;
            background: #fff;
            object-fit: cover;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            color: #fff;
            font-weight: 800;
            font-size: 14px;
        }

        .navbar-brand:hover { color: #fff; }
        .brand-caption { display: block; color: rgba(255,255,255,.75); font-size: 9px; font-weight: 600; letter-spacing: .08em; }

        .admin-menu {
            display: flex;
            align-items: center;
            flex: 1 1 auto;
            min-width: 0;
            flex-wrap: nowrap;
            overflow-x: auto;
            gap: 4px;
            margin: 0 12px 0 22px;
            scrollbar-width: none;
        }

        .admin-menu::-webkit-scrollbar { display: none; }

        .admin-menu .menu-link {
            display: inline-flex;
            align-items: center;
            min-height: 36px;
            padding: 0 13px;
            border-radius: 7px;
            color: #526176;
            font-size: 12px;
            font-weight: 650;
            text-decoration: none;
            white-space: nowrap;
            transition: color .15s ease, background .15s ease;
        }

        .admin-menu .menu-link:hover {
            color: var(--ink);
            background: #f0f3f8;
        }

        .admin-menu .menu-link.active {
            color: #fff;
            background: #17243a;
        }

        .admin-user {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255,255,255,.9);
            font-size: 11px;
            white-space: nowrap;
        }

        .admin-user .avatar {
            display: grid;
            width: 30px;
            height: 30px;
            place-items: center;
            border-radius: 50%;
            color: var(--teal);
            background: linear-gradient(135deg, #edf8f9, #dfeef1);
            font-weight: 800;
        }

        .admin-user .user-meta {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            line-height: 1.2;
        }

        .admin-user .user-role {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 56px;
            margin-top: 2px;
            padding: 3px 8px;
            border-radius: 999px;
            color: #1d3340;
            background: linear-gradient(135deg, #ffd49a, #f39b73);
            font-size: 8px;
            font-weight: 800;
            letter-spacing: .08em;
        }

        .admin-user form { margin: 0; }
        .admin-user button {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            min-height: 36px;
            padding: 0 12px;
            border: 1px solid rgba(255,255,255,.18);
            border-radius: 8px;
            color: #fff;
            background: linear-gradient(135deg, var(--coral), var(--coral-strong));
            box-shadow: 0 8px 18px rgba(214,76,61,.18);
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
            transition: transform .15s ease, filter .15s ease, border-color .15s ease;
        }
        .admin-user button:hover { border-color: rgba(255,255,255,.6); filter: brightness(1.04); transform: translateY(-1px); }
        .admin-user button:focus-visible { outline: 3px solid #fff; outline-offset: 2px; }
        .admin-user button i { font-size: 15px; }

        .admin-content {
            width: 100%;
            margin: 0;
            padding: 22px clamp(16px, 2vw, 32px) 44px;
            font-size: 15px;
        }

        .admin-content p,
        .admin-content .text-muted {
            color: #5f6d82 !important;
        }

        .admin-content .form-label {
            margin-bottom: 7px;
            color: #34445b;
            font-size: 15px;
            font-weight: 650;
        }

        .admin-content .form-control,
        .admin-content .form-select {
            min-height: 44px;
            color: #25364c;
            font-size: 15px;
        }

        .admin-content textarea.form-control {
            min-height: 110px;
        }

        .admin-content .btn {
            font-size: 14px;
        }

        .admin-content .small,
        .admin-content small {
            font-size: 13px;
        }

        .workspace-shell {
            position: relative;
            isolation: isolate;
            display: grid;
            min-height: calc(100vh - 66px);
            grid-template-columns: 250px minmax(0, 1fr);
        }

        .workspace-shell::before {
            position: absolute;
            z-index: -1;
            inset: 0;
            background:
                linear-gradient(rgba(245, 248, 252, .9), rgba(245, 248, 252, .9)),
                url('https://images.unsplash.com/photo-1604719312566-8912e9227c6a?auto=format&fit=crop&w=2200&q=85') center / cover;
            content: "";
            filter: blur(3px);
            transform: scale(1.01);
            pointer-events: none;
        }

        .workspace-sidebar {
            position: relative;
            isolation: isolate;
            overflow: hidden;
            padding: 22px 14px;
            border-right: 1px solid #064456;
            background: var(--teal);
        }

        .workspace-sidebar::before,
        .workspace-sidebar::after {
            position: absolute;
            z-index: -1;
            inset: 0;
            content: "";
            pointer-events: none;
        }

        .workspace-sidebar::before {
            background: url('https://images.unsplash.com/photo-1604719312566-8912e9227c6a?auto=format&fit=crop&w=1200&q=75') center / cover;
            filter: blur(7px);
            transform: scale(1.06);
        }

        .workspace-sidebar::after {
            background: rgba(7, 83, 106, .86);
        }

        .workspace-sidebar-title {
            position: relative;
            z-index: 1;
            margin: 0 10px 12px;
            color: rgba(255,255,255,.68);
            font-size: 11px;
            font-weight: 750;
            letter-spacing: .09em;
            text-transform: uppercase;
        }

        .workspace-sidebar .admin-menu {
            position: relative;
            z-index: 1;
            display: grid;
            gap: 5px;
            margin: 0;
            overflow: visible;
        }

        .workspace-sidebar .menu-link {
            min-height: 46px;
            padding: 0 14px;
            color: rgba(255,255,255,.88);
            font-size: 15px;
        }

        .workspace-sidebar .menu-link i {
            width: 24px;
            font-size: 18px;
        }

        .workspace-sidebar .menu-link:hover {
            color: #fff;
            background: rgba(255,255,255,.12);
        }

        .workspace-sidebar .menu-link.active {
            color: #fff;
            background: rgba(255,255,255,.2);
        }

        .workspace-layout .admin-navbar .container-fluid {
            padding-right: 20px !important;
            padding-left: 20px !important;
        }

        .page-heading { margin: 0 0 20px; }
        .page-heading h1 { margin: 0; color: var(--ink); font-size: 25px; font-weight: 750; letter-spacing: -.03em; }
        .page-heading p { margin: 4px 0 0; color: #5f6d82; font-size: 14px; }

        .admin-card {
            border: 1px solid var(--line);
            border-radius: 10px;
            background: var(--surface);
            box-shadow: 0 1px 3px rgba(19, 39, 67, .07);
        }

        .admin-card-header {
            padding: 15px 16px 12px;
            border-bottom: 1px solid #edf1f5;
        }

        .admin-card-header h2, .admin-card-header h3 {
            margin: 0;
            color: var(--ink);
            font-size: 15px;
            font-weight: 700;
        }

        .admin-card-body { padding: 16px; }
        .admin-card > .table-responsive,
        .admin-card > .products-table-wrap {
            width: calc(100% - 40px);
            margin-right: auto;
            margin-left: auto;
        }
        .admin-card .table {
            --bs-table-bg: transparent;
            margin-bottom: 0;
            border: 1px solid #d4dfe7;
            border-collapse: collapse;
            font-size: 13px;
        }
        .admin-card .table > :not(caption) > * > * {
            padding: 12px;
            border: 1px solid #d4dfe7;
            color: #29394f;
            vertical-align: middle;
        }
        .admin-card .table thead th {
            padding-top: 13px;
            padding-bottom: 13px;
            color: #34445b;
            background: #edf4f6;
            font-size: 11px;
            font-weight: 750;
            letter-spacing: .06em;
            text-transform: uppercase;
            white-space: nowrap;
        }
        .admin-card .table tbody tr:hover > * { background: #f5f9fb; }
        .btn-primary {
            --bs-btn-bg: var(--teal);
            --bs-btn-border-color: var(--teal);
            --bs-btn-hover-bg: #064456;
            --bs-btn-hover-border-color: #064456;
            --bs-btn-active-bg: #043b4b;
            --bs-btn-active-border-color: #043b4b;
        }
        .btn-outline-primary,
        .btn-outline-secondary {
            --bs-btn-color: #fff;
            --bs-btn-bg: var(--teal);
            --bs-btn-border-color: var(--teal);
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #064456;
            --bs-btn-hover-border-color: #064456;
            --bs-btn-active-color: #fff;
            --bs-btn-active-bg: #064456;
            --bs-btn-active-border-color: #064456;
            --bs-btn-focus-shadow-rgb: 7, 83, 106;
        }

        @media (max-width: 767px) {
            .admin-navbar .container-fluid {
                align-items: center;
                flex-wrap: wrap;
                row-gap: 10px;
                padding: 10px max(14px, env(safe-area-inset-right)) 10px max(14px, env(safe-area-inset-left)) !important;
            }
            .navbar-brand {
                flex: 1 1 100%;
                min-width: 0;
                margin-right: 0;
            }
            .navbar-brand > span { min-width: 0; overflow: hidden; text-overflow: ellipsis; }
            .brand-mark { width: 40px; height: 40px; }
            .admin-user {
                width: 100%;
                margin-left: 0;
                padding: 8px 10px;
                border: 1px solid rgba(255,255,255,.12);
                border-radius: 10px;
                background: rgba(255,255,255,.08);
            }
            .admin-user .user-meta { min-width: 0; flex: 1; }
            .admin-user .user-meta > span:first-child {
                max-width: 100%;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .admin-user button { margin-left: auto; }
            .workspace-shell {
                display: block;
                min-height: calc(100vh - 120px);
            }
            .workspace-sidebar {
                padding: 9px 12px;
                border-right: 0;
                border-bottom: 1px solid #064456;
            }
            .workspace-sidebar-title { display: none; }
            .workspace-sidebar .admin-menu {
                display: flex;
                gap: 6px;
                overflow-x: auto;
                padding-bottom: 2px;
                scrollbar-width: none;
                -webkit-overflow-scrolling: touch;
                overscroll-behavior-x: contain;
                scroll-snap-type: x proximity;
            }
            .workspace-sidebar .admin-menu::-webkit-scrollbar { display: none; }
            .workspace-sidebar .menu-link {
                flex: 0 0 auto;
                min-height: 44px;
                padding: 0 12px;
                font-size: 13px;
                scroll-snap-align: start;
            }
            .workspace-sidebar .menu-link i { width: 20px; font-size: 16px; }
            .workspace-layout .admin-content {
                padding: 18px max(14px, env(safe-area-inset-right)) 32px max(14px, env(safe-area-inset-left));
                padding-bottom: max(32px, env(safe-area-inset-bottom));
            }
            .admin-card > .table-responsive { -webkit-overflow-scrolling: touch; overscroll-behavior-x: contain; }
            .admin-user button { min-height: 44px; }
        }

        @media (max-width: 575px) {
            .admin-content { padding: 18px 12px 36px; }
            .page-heading h1 { font-size: 23px; }
            .workspace-layout .admin-content {
                min-width: 0;
                padding: 16px max(10px, env(safe-area-inset-right)) 30px max(10px, env(safe-area-inset-left));
                padding-bottom: max(30px, env(safe-area-inset-bottom));
            }
            .workspace-layout .admin-navbar .container-fluid {
                padding-right: max(12px, env(safe-area-inset-right)) !important;
                padding-left: max(12px, env(safe-area-inset-left)) !important;
            }
            .brand-caption { max-width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .admin-card > .table-responsive,
            .admin-card > .products-table-wrap { width: calc(100% - 20px); }
        }
    </style>
    @stack('styles')
</head>
<body class="workspace-layout">
    <nav class="navbar admin-navbar">
        <div class="container-fluid px-3 px-lg-0">
            <a class="navbar-brand" href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('cashier.index') }}">
                <img class="brand-mark" src="{{ $storeSettings->logo_url }}" alt="Logo {{ $storeSettings->store_name }}">
                <span>{{ $storeSettings->store_name }}<span class="brand-caption">{{ $storeSettings->store_address }}</span></span>
            </a>

            <div class="admin-user">
                <span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                <span class="user-meta">
                    <span>{{ auth()->user()->name }}</span>
                    <span class="user-role">{{ auth()->user()->role === 'admin' ? 'ADMIN' : 'KASIR' }}</span>
                </span>
                <button
                    type="button"
                    id="logoutConfirmationTrigger"
                    aria-label="Keluar"
                    title="Keluar"
                    data-bs-toggle="modal"
                    data-bs-target="#logoutConfirmationModal"
                >
                    <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                    <span>Keluar</span>
                </button>
            </div>
        </div>
    </nav>

    <div class="workspace-shell">
        <aside class="workspace-sidebar" aria-label="Menu {{ auth()->user()->role === 'admin' ? 'admin' : 'kasir' }}">
            <p class="workspace-sidebar-title">Menu {{ auth()->user()->role === 'admin' ? 'Admin' : 'Kasir' }}</p>
            <div class="admin-menu">
                @if(auth()->user()->role === 'admin')
                    <a class="menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-grid"></i>Dashboard</a>
                    <a class="menu-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}"><i class="bi bi-box-seam"></i>Kelola Barang</a>
                    <a class="menu-link {{ request()->routeIs('admin.kasir') ? 'active' : '' }}" href="{{ route('admin.kasir') }}"><i class="bi bi-people"></i>Kelola Kasir</a>
                    <a class="menu-link {{ request()->routeIs('admin.laporan') ? 'active' : '' }}" href="{{ route('admin.laporan') }}"><i class="bi bi-bar-chart"></i>Laporan</a>
                    <a class="menu-link {{ request()->routeIs('admin.pengaturan*') ? 'active' : '' }}" href="{{ route('admin.pengaturan') }}"><i class="bi bi-gear"></i>Pengaturan</a>
                @else
                    <a class="menu-link {{ request()->routeIs('cashier.index', 'cashier.transaksi') ? 'active' : '' }}" href="{{ route('cashier.index') }}"><i class="bi bi-cart3"></i>Transaksi Baru</a>
                    <a class="menu-link {{ request()->routeIs('cashier.produk') ? 'active' : '' }}" href="{{ route('cashier.produk') }}"><i class="bi bi-box-seam"></i>Daftar Produk</a>
                    <a class="menu-link {{ request()->routeIs('cashier.riwayat') ? 'active' : '' }}" href="{{ route('cashier.riwayat') }}"><i class="bi bi-receipt"></i>Riwayat Transaksi</a>
                    <a class="menu-link {{ request()->routeIs('cashier.laporan') ? 'active' : '' }}" href="{{ route('cashier.laporan') }}"><i class="bi bi-bar-chart"></i>Laporan</a>
                @endif
            </div>
        </aside>

        <main class="admin-content">
            @yield('content')
        </main>
    </div>

    @stack('modals')

    <div class="modal fade" id="logoutConfirmationModal" tabindex="-1" aria-labelledby="logoutConfirmationTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="logoutConfirmationTitle">Konfirmasi Keluar</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Apakah Anda yakin ingin keluar dari akun {{ auth()->user()->role === 'admin' ? 'Admin' : 'Kasir' }}?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-box-arrow-right me-1" aria-hidden="true"></i>Ya, Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
