@extends('layouts.admin')

@section('title', 'Riwayat Transaksi')

@section('content')
    <div class="page-heading">
        <h1>Riwayat Transaksi</h1>
        <p>Daftar transaksi yang telah diproses oleh akun kasir ini.</p>
    </div>

    <section class="admin-card">
        <div class="admin-card-header">
            <div>
                <h2>Transaksi Tersimpan</h2>
                <span class="text-muted small">{{ $sales->total() }} transaksi</span>
            </div>
        </div>

        <form class="d-flex flex-wrap gap-2 p-3" method="GET" action="{{ route('cashier.riwayat') }}" role="search">
            <label class="visually-hidden" for="saleSearch">Cari transaksi</label>
            <input
                class="form-control"
                id="saleSearch"
                name="search"
                type="search"
                value="{{ $search }}"
                placeholder="Cari invoice, pelanggan, barang, atau metode pembayaran..."
                maxlength="100"
            >
            <label class="visually-hidden" for="salePeriod">Periode transaksi</label>
            <select class="form-select" id="salePeriod" name="period" style="max-width: 190px">
                <option value="all" @selected($period === 'all')>Semua periode</option>
                <option value="day" @selected($period === 'day')>Hari ini</option>
                <option value="week" @selected($period === 'week')>Minggu ini</option>
                <option value="month" @selected($period === 'month')>Bulan ini</option>
                <option value="year" @selected($period === 'year')>Tahun ini</option>
            </select>
            <button class="btn btn-primary" type="submit">
                <i class="bi bi-funnel me-1" aria-hidden="true"></i>Terapkan
            </button>
            @if($search !== '' || $period !== 'all')
                <a class="btn btn-outline-secondary" href="{{ route('cashier.riwayat') }}">Reset</a>
            @endif
        </form>

        @if($sales->isEmpty())
            <div class="p-5 text-center text-muted">
                <i class="bi bi-receipt d-block mb-2 fs-3"></i>
                {{ $search !== '' || $period !== 'all' ? 'Transaksi tidak ditemukan. Coba kata kunci atau periode lain.' : 'Belum ada transaksi yang tersimpan.' }}
            </div>
        @else
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Invoice / Waktu</th>
                            <th>Pelanggan</th>
                            <th>Barang</th>
                            <th>Metode</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Kembali</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $sale)
                            <tr>
                                <td>
                                    <strong>{{ $sale->invoice }}</strong>
                                    <div class="small text-muted">{{ $sale->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td>
                                    {{ $sale->customer_type }}
                                    @if($sale->customer_contact)
                                        <div class="small text-muted">{{ $sale->customer_contact }}</div>
                                    @endif
                                </td>
                                <td>
                                    <details>
                                        <summary>{{ $sale->items->sum('quantity') }} item</summary>
                                        <ul class="small mb-0 mt-2 ps-3">
                                            @foreach($sale->items as $item)
                                                <li>{{ $item->product_name }} × {{ $item->quantity }} — Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</li>
                                            @endforeach
                                        </ul>
                                    </details>
                                </td>
                                <td>{{ strtoupper($sale->payment_method) }}</td>
                                <td class="text-end fw-semibold">Rp {{ number_format((float) $sale->total, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format((float) $sale->change, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                {{ $sales->links() }}
            </div>
        @endif
    </section>
@endsection
