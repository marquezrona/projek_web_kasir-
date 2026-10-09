@extends('layouts.admin')

@section('title', 'Laporan Penjualan')

@section('content')
    <header class="page-heading">
        <span class="text-secondary small text-uppercase fw-semibold">Ringkasan toko</span>
        <h1>Laporan Penjualan</h1>
        <p>Ringkasan penjualan seluruh kasir dan transaksi berdasarkan periode.</p>
    </header>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <section class="row g-3 mb-3">
        <div class="col-6 col-xl">
            <article class="admin-card report-stat-card report-stat-today h-100">
                <div class="admin-card-body">
                    <div class="report-stat-heading">
                        <span class="text-secondary small text-uppercase fw-semibold">Penjualan Hari Ini</span>
                        <span class="report-stat-icon" aria-hidden="true"><i class="bi bi-calendar-day"></i></span>
                    </div>
                    <div class="fs-5 fw-bold mt-2">Rp {{ number_format((float) $totalSales, 0, ',', '.') }}</div>
                </div>
            </article>
        </div>
        <div class="col-6 col-xl">
            <article class="admin-card report-stat-card report-stat-month h-100">
                <div class="admin-card-body">
                    <div class="report-stat-heading">
                        <span class="text-secondary small text-uppercase fw-semibold">Penjualan Bulan Ini</span>
                        <span class="report-stat-icon" aria-hidden="true"><i class="bi bi-calendar-month"></i></span>
                    </div>
                    <div class="fs-5 fw-bold mt-2">Rp {{ number_format((float) $monthlySales, 0, ',', '.') }}</div>
                </div>
            </article>
        </div>
        <div class="col-6 col-xl">
            <article class="admin-card report-stat-card report-stat-lifetime h-100">
                <div class="admin-card-body">
                    <div class="report-stat-heading">
                        <span class="text-secondary small text-uppercase fw-semibold">Penjualan Selama Ini</span>
                        <span class="report-stat-icon" aria-hidden="true"><i class="bi bi-graph-up-arrow"></i></span>
                    </div>
                    <div class="fs-5 fw-bold mt-2">Rp {{ number_format((float) $lifetimeSales, 0, ',', '.') }}</div>
                </div>
            </article>
        </div>
        <div class="col-6 col-xl">
            <article class="admin-card report-stat-card report-stat-transactions h-100">
                <div class="admin-card-body">
                    <div class="report-stat-heading">
                        <span class="text-secondary small text-uppercase fw-semibold">Transaksi {{ $periodLabel }}</span>
                        <span class="report-stat-icon" aria-hidden="true"><i class="bi bi-receipt"></i></span>
                    </div>
                    <div class="fs-5 fw-bold mt-2">{{ $transactionCount }}</div>
                </div>
            </article>
        </div>
        <div class="col-6 col-xl">
            <article class="admin-card report-stat-card report-stat-products h-100">
                <div class="admin-card-body">
                    <div class="report-stat-heading">
                        <span class="text-secondary small text-uppercase fw-semibold">Produk Aktif</span>
                        <span class="report-stat-icon" aria-hidden="true"><i class="bi bi-box-seam"></i></span>
                    </div>
                    <div class="fs-5 fw-bold mt-2">{{ $productsCount }}</div>
                </div>
            </article>
        </div>
    </section>

    <section class="admin-card mb-3">
        <div class="admin-card-header">
            <h2 class="h5 mb-0">Pilih Pengelompokan Transaksi</h2>
        </div>
        <form class="row g-3 p-3 align-items-end" method="GET" action="{{ route('admin.laporan') }}">
            <div class="col-12 col-md-3">
                <label class="form-label" for="period">Kelompokkan berdasarkan</label>
                <select class="form-select" id="period" name="period">
                    <option value="day" @selected($period === 'day')>Hari</option>
                    <option value="week" @selected($period === 'week')>Minggu</option>
                    <option value="month" @selected($period === 'month')>Bulan</option>
                </select>
            </div>

            @if ($period === 'day')
                <div class="col-12 col-md-4">
                    <label class="form-label" for="date">Tanggal</label>
                    <input class="form-control" id="date" name="date" type="date" value="{{ $selectedDate }}" required>
                </div>
            @elseif ($period === 'week')
                <div class="col-12 col-md-3">
                    <label class="form-label" for="week">Minggu</label>
                    <select class="form-select" id="week" name="week">
                        @foreach ($weekOptions as $weekNumber => $weekLabel)
                            <option value="{{ $weekNumber }}" @selected($selectedWeek === $weekNumber)>{{ $weekLabel }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label" for="week-year">Tahun</label>
                    <select class="form-select" id="week-year" name="year">
                        @foreach ($years as $year)
                            <option value="{{ $year }}" @selected($selectedYear === $year)>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <div class="col-12 col-md-3">
                    <label class="form-label" for="month">Bulan</label>
                    <select class="form-select" id="month" name="month">
                        @foreach ($monthNames as $monthNumber => $monthName)
                            <option value="{{ $monthNumber }}" @selected($selectedMonth === $monthNumber)>{{ $monthName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label" for="month-year">Tahun</label>
                    <select class="form-select" id="month-year" name="year">
                        @foreach ($years as $year)
                            <option value="{{ $year }}" @selected($selectedYear === $year)>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="col-12 col-md-auto">
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-funnel me-1" aria-hidden="true"></i>Tampilkan
                </button>
                <a class="btn btn-outline-secondary" href="{{ route('admin.laporan') }}">Hari ini</a>
            </div>
        </form>
    </section>

    <section class="admin-card">
        <div class="admin-card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h2 class="h5 mb-1">Transaksi {{ $periodLabel }}</h2>
                <span class="text-secondary small">Penjualan periode ini: Rp {{ number_format((float) $periodSales, 0, ',', '.') }}</span>
            </div>
            <span class="text-secondary small">{{ $transactions->total() }} transaksi</span>
        </div>
        @if ($transactions->isEmpty())
            <div class="p-5 text-center text-secondary">Belum ada transaksi pada periode ini.</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Waktu</th>
                            <th>Kasir</th>
                            <th>Pelanggan</th>
                            <th>Metode Pembayaran</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $sale)
                            <tr>
                                <td class="fw-semibold">{{ $sale->invoice }}</td>
                                <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $sale->cashier_name }}</td>
                                <td>
                                    {{ $sale->customer_type }}
                                    @if ($sale->customer_contact)
                                        <div class="small text-muted">{{ $sale->customer_contact }}</div>
                                    @endif
                                </td>
                                <td>{{ strtoupper($sale->payment_method) }}</td>
                                <td class="text-end fw-semibold">Rp {{ number_format((float) $sale->total, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($transactions->hasPages())
                <div class="p-3">
                    {{ $transactions->links() }}
                </div>
            @endif
        @endif
    </section>
@endsection
