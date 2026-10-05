<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Kasir - {{ $storeSettings->store_name }}</title>
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}">
    <style>
        :root { --teal:#07536a; --teal-deep:#063b4d; --coral:#e96357; --ink:#17212b; --muted:#77848c; --line:#e6ebed; --surface:#fff; --canvas:#f3f6f5; }
        * { box-sizing:border-box; }
        body { margin:0; background:var(--canvas); color:var(--ink); font-family:"Segoe UI",Tahoma,sans-serif; }
        button,input { font:inherit; }
        .app-shell { min-height:100vh; display:flex; }
        .sidebar { width:248px; flex:0 0 248px; display:flex; flex-direction:column; padding:24px 16px 18px; color:#fff; background:var(--teal-deep); }
        .brand { display:flex; align-items:center; gap:10px; padding:0 12px 25px; color:#fff; text-decoration:none; }
        .brand-logo { width:48px; height:48px; border:2px solid rgba(255,255,255,.85); border-radius:50%; background:#fff; object-fit:cover; }
        .brand strong { font-size:17px; letter-spacing:.02em; }
        .flag { width:20px; height:13px; display:inline-block; margin-left:3px; vertical-align:1px; background:linear-gradient(#e72b35 0 50%,#fff 50%); box-shadow:0 0 0 1px rgba(255,255,255,.25); }
        .eyebrow { padding:0 12px 9px; color:rgba(255,255,255,.46); font-size:10px; font-weight:700; letter-spacing:.14em; text-transform:uppercase; }
        .nav-list { display:grid; gap:5px; }
        .nav-link { display:flex; align-items:center; gap:12px; padding:11px 12px; border-radius:8px; color:rgba(255,255,255,.68); font-size:13px; text-decoration:none; transition:background .2s,color .2s; }
        .nav-link i { width:17px; font-size:16px; }
        .nav-link:hover,.nav-link.active { color:#fff; background:rgba(255,255,255,.13); }
        .sidebar-spacer { flex:1; }
        .user-mini { display:flex; align-items:center; gap:10px; padding:14px 12px; border-top:1px solid rgba(255,255,255,.1); }
        .avatar { width:31px; height:31px; display:grid; place-items:center; border-radius:50%; color:var(--teal-deep); background:#d9e7e7; font-size:12px; font-weight:800; }
        .user-mini strong { display:block; color:#fff; font-size:12px; }
        .user-mini span { display:block; margin-top:2px; color:rgba(255,255,255,.48); font-size:10px; }
        .logout { display:block; width:calc(100% - 24px); margin:3px 12px 0; padding:9px 0; border:1px solid rgba(255,255,255,.18); border-radius:7px; color:rgba(255,255,255,.7); background:transparent; cursor:pointer; font-size:11px; text-align:center; }
        .logout:hover { color:#fff; border-color:rgba(255,255,255,.5); }
        .main { min-width:0; flex:1; padding:25px 34px 40px; }
        .topbar { display:flex; align-items:center; justify-content:space-between; gap:18px; margin-bottom:29px; }
        .menu-button { display:none; border:0; color:var(--teal); background:transparent; font-size:23px; cursor:pointer; }
        .page-kicker { margin:0 0 4px; color:var(--coral); font-size:11px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; }
        h1 { margin:0; font-size:26px; letter-spacing:-.02em; }
        .topbar-note { color:var(--muted); font-size:12px; }
        .date-chip { display:flex; align-items:center; gap:8px; padding:9px 12px; border:1px solid var(--line); border-radius:7px; color:var(--muted); background:var(--surface); font-size:11px; white-space:nowrap; }
        .stats { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:14px; margin-bottom:23px; }
        .stat-card { position:relative; overflow:hidden; padding:17px; border:1px solid var(--line); border-radius:10px; background:var(--surface); box-shadow:0 5px 18px rgba(20,55,62,.04); }
        .stat-card::after { content:""; position:absolute; right:-20px; bottom:-27px; width:78px; height:78px; border-radius:50%; background:rgba(233,99,87,.08); }
        .stat-top { display:flex; align-items:center; justify-content:space-between; color:var(--muted); font-size:11px; }
        .stat-top i { display:grid; place-items:center; width:29px; height:29px; border-radius:7px; color:var(--teal); background:#e6f0f0; font-size:15px; }
        .stat-card.warn .stat-top i { color:var(--coral); background:#fff0ed; }
        .stat-card strong { display:block; margin-top:12px; font-size:24px; letter-spacing:-.04em; }
        .stat-card small { display:block; margin-top:2px; color:var(--muted); font-size:10px; }
        .content-grid { display:grid; grid-template-columns:minmax(0,1.55fr) minmax(250px,.75fr); gap:17px; }
        .panel { border:1px solid var(--line); border-radius:10px; background:var(--surface); box-shadow:0 5px 18px rgba(20,55,62,.04); }
        .panel-header { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:18px 19px 14px; border-bottom:1px solid var(--line); }
        .panel-header h2 { margin:0; font-size:15px; }
        .panel-header p { margin:4px 0 0; color:var(--muted); font-size:11px; }
        .search { position:relative; width:145px; }
        .search i { position:absolute; top:50%; left:9px; transform:translateY(-50%); color:var(--muted); font-size:12px; }
        .search input { width:100%; height:30px; padding:0 8px 0 27px; border:1px solid var(--line); border-radius:6px; outline:none; color:var(--ink); background:#fafcfc; font-size:11px; }
        .search input:focus { border-color:var(--teal); }
        .table-wrap { overflow-x:auto; padding:0 19px 9px; }
        table { width:100%; border-collapse:collapse; font-size:11px; }
        th { padding:12px 8px; color:var(--muted); font-size:10px; font-weight:600; text-align:left; }
        td { padding:12px 8px; border-top:1px solid #f0f2f2; white-space:nowrap; }
        td:first-child,th:first-child { padding-left:0; }
        .product-name { display:flex; align-items:center; gap:9px; font-weight:600; }
        .product-icon { display:grid; place-items:center; width:28px; height:28px; border-radius:6px; color:var(--teal); background:#e9f1f0; font-size:13px; }
        .muted { color:var(--muted); }
        .stock { color:#21805d; font-weight:700; }
        .stock.low { color:var(--coral); }
        .empty { padding:34px 10px; color:var(--muted); text-align:center; }
        .empty i { display:block; margin-bottom:9px; font-size:27px; color:#b8c6c8; }
        .quick-actions { display:grid; gap:9px; padding:17px 19px 20px; }
        .quick-action { display:flex; align-items:center; gap:11px; padding:12px; border:1px solid var(--line); border-radius:8px; color:var(--ink); background:#fbfcfc; text-decoration:none; }
        .quick-action:hover { border-color:#b9d0d1; background:#f4f9f8; }
        .quick-action i { display:grid; place-items:center; width:29px; height:29px; border-radius:7px; color:var(--teal); background:#e6f0f0; }
        .quick-action strong { display:block; font-size:11px; }
        .quick-action span span { display:block; margin-top:3px; color:var(--muted); font-size:10px; }
        .notice { margin:17px 19px 19px; padding:13px; border-left:3px solid var(--coral); border-radius:5px; color:#7d4c47; background:#fff4f1; font-size:11px; line-height:1.5; }
        @media (max-width:1050px) { .sidebar { width:220px; flex-basis:220px; } .main { padding:23px; } .stats { grid-template-columns:repeat(2,1fr); } .content-grid { grid-template-columns:1fr; } }
        @media (max-width:680px) { .sidebar { position:fixed; z-index:5; top:0; bottom:0; left:0; transform:translateX(-100%); transition:transform .25s; } .sidebar.open { transform:translateX(0); } .main { padding:18px 15px 30px; } .menu-button { display:block; } .topbar { align-items:flex-start; } .topbar-note,.date-chip { display:none; } .topbar-left { display:flex; gap:10px; align-items:flex-start; } .stats { gap:9px; } .stat-card { padding:13px; } .stat-card strong { font-size:20px; } .panel-header { align-items:flex-start; flex-wrap:wrap; } }
        @media (max-width:420px) { .stats { grid-template-columns:1fr 1fr; } .search { width:100%; } }
    </style>
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar" id="sidebar">
            <a class="brand" href="{{ route('dashboard') }}"><img class="brand-logo" src="{{ $storeSettings->logo_url }}" alt="Logo {{ $storeSettings->store_name }}"><strong>{{ $storeSettings->store_name }} <span class="flag"></span></strong></a>
            <div class="eyebrow">Menu Utama</div>
            <nav class="nav-list" aria-label="Navigasi utama">
                <a class="nav-link active" href="{{ route('dashboard') }}"><i class="bi bi-grid-1x2-fill"></i>Ringkasan</a>
                <a class="nav-link" href="{{ route('products.index') }}"><i class="bi bi-box-seam"></i>Kelola Produk</a>
                <a class="nav-link" href="#laporan"><i class="bi bi-bar-chart-line"></i>Laporan Penjualan</a>
            </nav>
            <div class="sidebar-spacer"></div>
            <div class="user-mini"><div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div><div><strong>{{ auth()->user()->name }}</strong><span>Administrator</span></div></div>
            <form method="POST" action="{{ route('logout') }}"><input type="hidden" name="_token" value="{{ csrf_token() }}"><button class="logout" type="submit"><i class="bi bi-box-arrow-right"></i> Keluar</button></form>
        </aside>
        <main class="main">
            <header class="topbar">
                <div class="topbar-left"><button class="menu-button" type="button" aria-label="Buka menu" aria-controls="sidebar" aria-expanded="false"><i class="bi bi-list"></i></button><div><p class="page-kicker">Selamat datang kembali</p><h1>Ringkasan Toko</h1><div class="topbar-note">Pantau stok dan aktivitas toko hari ini.</div></div></div>
                <div class="date-chip"><i class="bi bi-calendar3"></i>{{ now()->translatedFormat('d F Y') }}</div>
            </header>
            <section class="stats" aria-label="Statistik toko">
                <article class="stat-card"><div class="stat-top"><span>Total Produk</span><i class="bi bi-box-seam"></i></div><strong>{{ $totalProducts }}</strong><small>produk tersimpan</small></article>
                <article class="stat-card"><div class="stat-top"><span>Produk Aktif</span><i class="bi bi-check2-circle"></i></div><strong>{{ $activeProducts }}</strong><small>siap dijual</small></article>
                <article class="stat-card warn"><div class="stat-top"><span>Stok Menipis</span><i class="bi bi-exclamation-triangle"></i></div><strong>{{ $lowStockProducts }}</strong><small>perlu diperiksa</small></article>
                <article class="stat-card"><div class="stat-top"><span>Nilai Persediaan</span><i class="bi bi-wallet2"></i></div><strong>Rp {{ number_format($inventoryValue, 0, ',', '.') }}</strong><small>estimasi stok saat ini</small></article>
            </section>
            <section class="content-grid">
                <article class="panel">
                    <div class="panel-header"><div><h2>Produk Terbaru</h2><p>Daftar barang yang tersedia di toko</p></div><div class="search"><i class="bi bi-search"></i><input id="product-search" type="search" placeholder="Cari produk" aria-label="Cari produk"></div></div>
                    <div class="table-wrap"><table><thead><tr><th>Produk</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Status</th></tr></thead><tbody id="product-table">
                        @forelse ($products->take(7) as $product)
                            <tr><td><div class="product-name"><span class="product-icon"><i class="bi bi-basket2"></i></span>{{ $product->name }}</div></td><td class="muted">{{ $product->category ?: 'Umum' }}</td><td>Rp {{ number_format($product->price, 0, ',', '.') }}</td><td class="stock {{ $product->stock <= 5 ? 'low' : '' }}">{{ $product->stock }}</td><td class="muted">{{ $product->is_active ? 'Aktif' : 'Nonaktif' }}</td></tr>
                        @empty
                            <tr><td colspan="5"><div class="empty"><i class="bi bi-box2"></i>Belum ada produk. Tambahkan produk pertama Anda.</div></td></tr>
                        @endforelse
                    </tbody></table></div>
                </article>
                <aside class="panel" id="transaksi">
                    <div class="panel-header"><div><h2>Akses Cepat</h2><p>Kelola aktivitas toko</p></div></div>
                    <div class="quick-actions"><a class="quick-action" href="{{ route('products.create') }}"><i class="bi bi-plus-lg"></i><span><strong>Tambah Produk</strong><span>Masukkan barang baru</span></span></a><a class="quick-action" href="{{ route('products.index') }}"><i class="bi bi-boxes"></i><span><strong>Lihat Semua Produk</strong><span>Kelola stok dan harga</span></span></a><a class="quick-action" href="#laporan"><i class="bi bi-file-earmark-bar-graph"></i><span><strong>Laporan Penjualan</strong><span>Segera tersedia</span></span></a></div>
                    <div class="notice"><i class="bi bi-lightbulb"></i> Periksa stok yang berwarna coral agar persediaan toko tetap aman.</div>
                </aside>
            </section>
        </main>
    </div>
    <script>
        const menuButton = document.querySelector('.menu-button');
        const sidebar = document.querySelector('#sidebar');
        menuButton?.addEventListener('click', () => { const isOpen = sidebar.classList.toggle('open'); menuButton.setAttribute('aria-expanded', String(isOpen)); });
        const searchInput = document.querySelector('#product-search');
        searchInput?.addEventListener('input', (event) => { const query = event.target.value.toLowerCase(); document.querySelectorAll('#product-table tr').forEach((row) => { row.hidden = query && !row.textContent.toLowerCase().includes(query); }); });
    </script>
</body>
</html>
