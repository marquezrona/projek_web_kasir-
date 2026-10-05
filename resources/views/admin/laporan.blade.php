@extends('layouts.admin')

@section('title', 'Laporan Penjualan')

@section('content')
    <header class="page-heading">
        <span class="text-secondary small text-uppercase fw-semibold">Ringkasan toko</span>
        <h1>Laporan Penjualan</h1>
        <p>Ringkasan transaksi dan nilai penjualan.</p>
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
                    <div class="fs-4 fw-bold mt-2">{{ $todayTransactionCount }}</div>
                </div>
            </article>
        </div>
        <div class="col-md-4">
            <article class="admin-card h-100">
                <div class="admin-card-body">
                    <span class="text-secondary small text-uppercase fw-semibold">Produk Terdaftar</span>
                    <div class="fs-4 fw-bold mt-2">{{ $totalProducts }}</div>
                </div>
            </article>
        </div>
    </section>

    <section class="admin-card">
        <div class="admin-card-header d-flex justify-content-between align-items-center">
            <h2>Transaksi Terbaru</h2>
            <span class="text-secondary small">{{ $transactions->total() }} transaksi tersimpan</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Waktu</th>
                        <th>Kasir</th>
                        <th>Pelanggan</th>
                        <th>Metode</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $sale)
                        <tr>
                            <td class="fw-semibold">{{ $sale->invoice }}</td>
                            <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $sale->cashier_name }}</td>
                            <td>
                                {{ $sale->customer_type }}
                                @if($sale->customer_contact)
                                    <div class="small text-muted">{{ $sale->customer_contact }}</div>
                                @endif
                            </td>
                            <td>{{ strtoupper($sale->payment_method) }}</td>
                            <td class="fw-semibold">Rp {{ number_format((float) $sale->total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-5 text-center text-secondary">Belum ada transaksi tersimpan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->hasPages())
            <div class="p-3">
                {{ $transactions->links() }}
            </div>
        @endif
    </section>
@endsection
