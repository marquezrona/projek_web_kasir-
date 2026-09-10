<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Orbit</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background-color: #0d6efd;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            border-radius: 6px;
            margin-bottom: 4px;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Navbar -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse p-3">
            <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none fs-4 fw-bold">
                Orbit Admin
            </a>
            <hr class="text-white">
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link active">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="/products" class="nav-link">
                        <i class="bi bi-grid me-2"></i> Kelola Paket Wisata
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link">
                        <i class="bi bi-cart me-2"></i> Transaksi
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link">
                        <i class="bi bi-people me-2"></i> Pengguna
                    </a>
                </li>
            </ul>
            <hr class="text-white">
            <div class="dropdown">
                <a href="/" class="btn btn-danger w-100">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </a>
            </div>
        </nav>

        <!-- Main Content Area -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard Overview</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <span class="badge bg-primary fs-6">Selamat Datang, Admin</span>
                </div>
            </div>

            <!-- Ringkasan Statistik (Cards) -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-3">
                        <div class="d-flex align-items-center">
                            <div class="badge bg-primary p-3 rounded-circle me-3">
                                <i class="bi bi-geo-alt fs-3"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Total Paket Wisata</h6>
                                <h3 class="fw-bold mb-0">12</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-3">
                        <div class="d-flex align-items-center">
                            <div class="badge bg-success p-3 rounded-circle me-3">
                                <i class="bi bg-success bi-receipt fs-3"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Total Pesanan</h6>
                                <h3 class="fw-bold mb-0">48</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-3">
                        <div class="d-flex align-items-center">
                            <div class="badge bg-warning p-3 rounded-circle text-white me-3">
                                <i class="bi bi-people fs-3"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Total User Active</h6>
                                <h3 class="fw-bold mb-0">150</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Transaksi Terakhir -->
            <div class="card border-0 shadow-sm p-3">
                <h5 class="card-title mb-3">Pesanan Terbaru</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nama Pelanggan</th>
                                <th>Paket Wisata</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Budi Santoso</td>
                                <td>Paket Astrowisata Eksklusif</td>
                                <td>10 Sep 2026</td>
                                <td><span class="badge bg-success">Lunas</span></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Siti Rahma</td>
                                <td>Jelajah Bintang Tour</td>
                                <td>09 Sep 2026</td>
                                <td><span class="badge bg-warning text-dark">Pending</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<script href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>