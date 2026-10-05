@extends('layouts.admin')

@section('title', 'Laporan Kasir')

@section('content')
    <header class="page-heading">
        <span class="text-secondary small text-uppercase fw-semibold">Ringkasan kasir</span>
        <h1>Laporan Kasir</h1>
        <p>Ringkasan penjualan dan transaksi.</p>
    </header>

    <section class="row g-3 mb-3">
        <div class="col-md-4">
            <article class="admin-card h-100">
                <div class="admin-card-body">
                    <span class="text-secondary small text-uppercase fw-semibold">Penjualan Hari Ini</span>
                    <div class="fs-4 fw-bold mt-2">Rp {{ number_format($totalSales, 0, ',', '.') }}</div>
                </div>
            </article>
        </div>
        <div class="col-md-4">
            <article class="admin-card h-100">
                <div class="admin-card-body">
                    <span class="text-secondary small text-uppercase fw-semibold">Jumlah Transaksi</span>
                    <div class="fs-4 fw-bold mt-2">{{ count($transactions) }}</div>
                </div>
            </article>
        </div>
        <div class="col-md-4">
            <article class="admin-card h-100">
                <div class="admin-card-body">
                    <span class="text-secondary small text-uppercase fw-semibold">Produk Aktif</span>
                    <div class="fs-4 fw-bold mt-2">{{ $products->count() }}</div>
                </div>
            </article>
        </div>
    </section>

    <section class="admin-card">
        <div class="admin-card-header d-flex justify-content-between align-items-center">
            <h2>Transaksi Terbaru</h2>
            <span class="text-secondary small">Hari ini</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Pelanggan</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        <tr>
                            <td class="fw-semibold">{{ $transaction['invoice'] }}</td>
                            <td>{{ $transaction['customer'] }}</td>
                            <td>Rp {{ number_format($transaction['total'], 0, ',', '.') }}</td>
                            <td><span class="badge text-bg-success">{{ $transaction['status'] }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-5 text-center text-secondary">Belum ada transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
