<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Kasir Sembako</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --bg: #f4f0ea;
            --card: #ffffff;
            --line: #e8e1d5;
            --teal: #07536a;
            --teal-dark: #053e51;
            --green: #2f9e61;
            --amber: #f5b75e;
            --red: #d96262;
            --ink: #1d2a2f;
            --muted: #6b7a7f;
            --navy: #112d39;
            --shadow: 0 16px 40px rgba(13, 34, 40, 0.08);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, sans-serif;
            background: var(--bg);
            color: var(--ink);
        }

        .app-shell {
            min-height: 100vh;
            display: flex;
            background: linear-gradient(180deg, #f7f3ee 0%, #f0efe9 100%);
        }

        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, var(--navy) 0%, var(--teal) 100%);
            color: #fff;
            padding: 26px 18px 20px;
            position: relative;
            box-shadow: 12px 0 30px rgba(14, 38, 44, 0.12);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 6px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.18);
            margin-bottom: 20px;
        }

        .brand-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: rgba(255,255,255,0.13);
            display: grid;
            place-items: center;
            font-size: 20px;
        }

        .brand-text strong {
            display: block;
            font-size: 1rem;
            letter-spacing: 0.06em;
        }

        .brand-text small {
            color: rgba(255,255,255,0.72);
            font-size: 11px;
        }

        .nav-menu {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            gap: 10px;
        }

        .nav-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 12px;
            border-radius: 12px;
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .nav-menu a.active,
        .nav-menu a:hover {
            background: rgba(255,255,255,0.12);
            color: #fff;
            transform: translateX(2px);
        }

        .nav-menu i {
            width: 18px;
            text-align: center;
            font-size: 1rem;
        }

        .sidebar-footer {
            position: absolute;
            left: 18px;
            right: 18px;
            bottom: 18px;
        }

        .logout-btn {
            width: 100%;
            border: 0;
            border-radius: 12px;
            background: rgba(255,255,255,0.08);
            color: #fff;
            padding: 12px 14px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .main {
            flex: 1;
            padding: 28px 30px 30px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            margin-bottom: 26px;
            padding: 18px 22px;
            background: rgba(255,255,255,0.7);
            border: 1px solid var(--line);
            border-radius: 18px;
            box-shadow: var(--shadow);
        }

        .topbar h1 {
            margin: 0;
            font-size: clamp(1.4rem, 2vw, 2rem);
            font-weight: 800;
            color: var(--teal-dark);
        }

        .user-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 12px 6px 8px;
            border-radius: 999px;
            background: #edf8f9;
            border: 1px solid #d6e9ed;
            color: var(--teal);
            font-weight: 700;
        }

        .user-pill .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--teal), #1e7a96);
            color: #fff;
            display: grid;
            place-items: center;
            font-size: 0.8rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(180px, 1fr));
            gap: 18px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--card);
            border-radius: 18px;
            padding: 18px 18px 16px;
            border: 1px solid var(--line);
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
        }

        .stat-icon {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            font-size: 1.4rem;
            color: #fff;
        }

        .bg-teal { background: linear-gradient(135deg, var(--teal), #1f758b); }
        .bg-green { background: linear-gradient(135deg, var(--green), #6abf84); }
        .bg-amber { background: linear-gradient(135deg, var(--amber), #f7d27a); }
        .bg-red { background: linear-gradient(135deg, var(--red), #ef8b8b); }

        .stat-label {
            margin: 0;
            font-size: 12px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .stat-value {
            margin: 6px 0 0;
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--ink);
        }

        .stat-trend {
            font-size: 12px;
            font-weight: 700;
            margin-top: 6px;
        }

        .trend-up { color: var(--green); }
        .trend-down { color: var(--red); }

        .content-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 24px;
        }

        .panel {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 18px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .panel-header {
            padding: 18px 20px 14px;
            border-bottom: 1px solid var(--line);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .panel-header h3 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--teal-dark);
        }

        .badge-soft {
            background: #edf8f9;
            color: var(--teal);
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 11px;
            font-weight: 700;
        }

        .table-wrap {
            padding: 0 0 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 14px 20px;
            text-align: left;
            border-bottom: 1px solid #f0ece5;
            font-size: 0.95rem;
        }

        thead th {
            background: #faf7f2;
            color: var(--muted);
            font-size: 11px;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 92px;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .status-paid { background: rgba(47, 158, 97, 0.12); color: var(--green); }
        .status-waiting { background: rgba(245, 183, 94, 0.2); color: #a56600; }
        .status-cancel { background: rgba(217, 98, 98, 0.12); color: var(--red); }

        .stock-list {
            padding: 14px 18px 18px;
        }

        .stock-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f0ece5;
        }

        .stock-item:last-child {
            border-bottom: 0;
        }

        .product-name {
            font-weight: 700;
            margin-bottom: 4px;
        }

        .product-meta {
            font-size: 12px;
            color: var(--muted);
        }

        .stock-tag {
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .stock-low { background: rgba(217,98,98,0.12); color: var(--red); }
        .stock-good { background: rgba(47,158,97,0.12); color: var(--green); }

        @media (max-width: 980px) {
            .app-shell { display: block; }
            .sidebar {
                width: 100%;
                min-height: auto;
                padding-bottom: 90px;
            }
            .stats-grid { grid-template-columns: repeat(2, minmax(180px, 1fr)); }
            .content-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 620px) {
            .main { padding: 18px 14px 24px; }
            .stats-grid { grid-template-columns: 1fr; }
            .topbar { flex-direction: column; align-items: flex-start; }
            th, td { padding: 12px 14px; }
        }
    </style>
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-icon"><i class="bi bi-shop-window"></i></div>
                <div class="brand-text">
                    <strong>JAZZEL</strong>
                    <small>Kasir Sembako</small>
                </div>
            </div>

            <ul class="nav-menu">
                <li><a href="{{ route('admin.dashboard') }}" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                <li><a href="/products"><i class="bi bi-box-seam"></i> Produk</a></li>
                <li><a href="#"><i class="bi bi-receipt"></i> Transaksi</a></li>
                <li><a href="#"><i class="bi bi-bar-chart"></i> Laporan</a></li>
                <li><a href="#"><i class="bi bi-people"></i> Pelanggan</a></li>
                <li><a href="#"><i class="bi bi-gear"></i> Pengaturan</a></li>
            </ul>

            <div class="sidebar-footer">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="bi bi-box-arrow-right"></i> Keluar
                    </button>
                </form>
            </div>
        </aside>

        <main class="main">
            <div class="topbar">
                <h1>Dashboard Admin</h1>
                <div class="user-pill">
                    <div class="avatar">A</div>
                    <span>{{ auth()->user()->name ?? 'Admin' }}</span>
                </div>
            </div>

            <section class="stats-grid">
                <div class="stat-card">
                    <div>
                        <p class="stat-label">Penjualan Hari Ini</p>
                        <p class="stat-value">Rp 0</p>
                        <div class="stat-trend trend-up"><i class="bi bi-arrow-up-right"></i> 0%</div>
                    </div>
                    <div class="stat-icon bg-teal"><i class="bi bi-cart3"></i></div>
                </div>

                <div class="stat-card">
                    <div>
                        <p class="stat-label">Transaksi</p>
                        <p class="stat-value">0</p>
                        <div class="stat-trend trend-up"><i class="bi bi-arrow-up-right"></i> 0%</div>
                    </div>
                    <div class="stat-icon bg-green"><i class="bi bi-receipt-cutoff"></i></div>
                </div>

                <div class="stat-card">
                    <div>
                        <p class="stat-label">Stok Rendah</p>
                        <p class="stat-value">0</p>
                        <div class="stat-trend trend-down"><i class="bi bi-arrow-down-right"></i> 0 item</div>
                    </div>
                    <div class="stat-icon bg-amber"><i class="bi bi-exclamation-triangle"></i></div>
                </div>

                <div class="stat-card">
                    <div>
                        <p class="stat-label">Kunjungan</p>
                        <p class="stat-value">0</p>
                        <div class="stat-trend trend-up"><i class="bi bi-arrow-up-right"></i> 0%</div>
                    </div>
                    <div class="stat-icon bg-red"><i class="bi bi-people"></i></div>
                </div>
            </section>

            <section class="content-grid">
                <div class="panel">
                    <div class="panel-header">
                        <h3>Transaksi Terbaru</h3>
                        <span class="badge-soft">Hari ini</span>
                    </div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Pelanggan</th>
                                    <th>Produk</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>#INV-0000</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>Rp 0</td>
                                    <td><span class="status-badge status-paid">0</span></td>
                                </tr>
                                <tr>
                                    <td>#INV-0000</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>Rp 0</td>
                                    <td><span class="status-badge status-waiting">0</span></td>
                                </tr>
                                <tr>
                                    <td>#INV-0000</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>Rp 0</td>
                                    <td><span class="status-badge status-paid">0</span></td>
                                </tr>
                                <tr>
                                    <td>#INV-0000</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>Rp 0</td>
                                    <td><span class="status-badge status-cancel">0</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-header">
                        <h3>Stok Produk</h3>
                        <span class="badge-soft">Update</span>
                    </div>
                    <div class="stock-list">
                        <div class="stock-item">
                            <div>
                                <div class="product-name">Produk A</div>
                                <div class="product-meta">SKU: 0000</div>
                            </div>
                            <span class="stock-tag stock-good">0 pcs</span>
                        </div>
                        <div class="stock-item">
                            <div>
                                <div class="product-name">Produk B</div>
                                <div class="product-meta">SKU: 0000</div>
                            </div>
                            <span class="stock-tag stock-low">0 pcs</span>
                        </div>
                        <div class="stock-item">
                            <div>
                                <div class="product-name">Produk C</div>
                                <div class="product-meta">SKU: 0000</div>
                            </div>
                            <span class="stock-tag stock-good">0 pcs</span>
                        </div>
                        <div class="stock-item">
                            <div>
                                <div class="product-name">Produk D</div>
                                <div class="product-meta">SKU: 0000</div>
                            </div>
                            <span class="stock-tag stock-low">0 pcs</span>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
