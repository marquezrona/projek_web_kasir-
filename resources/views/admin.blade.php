@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@push('styles')
    <style>
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        .stat-card {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            min-height: 88px;
            padding: 15px 16px;
        }

        .stat-label {
            display: block;
            color: #34445b;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .07em;
            text-transform: uppercase;
        }

        .stat-value {
            display: block;
            margin-top: 7px;
            color: var(--ink);
            font-size: 20px;
            font-weight: 750;
            line-height: 1.1;
        }

        .stat-icon {
            display: grid;
            width: 36px;
            height: 36px;
            flex: 0 0 36px;
            place-items: center;
            border-radius: 8px;
            font-size: 17px;
        }

        .stat-icon.inventory { color: #087f8c; background: #e3f5f5; }
        .stat-icon.products { color: #356ac3; background: #eaf0ff; }
        .stat-icon.active { color: #21835b; background: #e7f5ec; }
        .stat-icon.low-stock { color: #bd6b12; background: #fff2df; }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 1.75fr .85fr;
            gap: 12px;
            margin-bottom: 16px;
        }

        .chart-card { min-height: 235px; }
        .chart-card .admin-card-body { padding: 12px 16px 16px; }

        .eyebrow {
            display: block;
            margin-bottom: 3px;
            color: #5f6d82;
            font-size: 11px;
            font-weight: 750;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .chart-area {
            position: relative;
            height: 175px;
            margin-top: 12px;
        }

        .sales-chart {
            display: block;
            width: 100%;
            height: 100%;
            overflow: visible;
        }

        .chart-gridline { stroke: #e4ebf1; stroke-width: 1; }
        .chart-area-fill { fill: rgba(7,83,106,.1); }
        .chart-line { fill: none; stroke: #087f8c; stroke-linecap: round; stroke-linejoin: round; stroke-width: 3; }
        .chart-point { fill: #fff; stroke: #087f8c; stroke-width: 3; }
        .chart-labels {
            display: flex;
            justify-content: space-between;
            gap: 4px;
            margin-top: 3px;
            color: #526176;
            font-size: 12px;
        }
        .chart-labels span { flex: 1 1 0; text-align: center; }
        .chart-empty { color: #5f6d82; font-size: 12px; font-style: italic; text-align: center; }

        .payment-empty {
            display: grid;
            min-height: 170px;
            place-items: center;
            color: #5f6d82;
            font-size: 12px;
            font-style: italic;
        }

        .stock-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 11px 0;
            border-bottom: 1px solid #edf1f5;
            font-size: 13px;
        }

        .stock-row:last-child { border-bottom: 0; }
        .stock-name { font-weight: 650; }
        .stock-category { display: block; margin-top: 3px; color: #5f6d82; font-size: 12px; }
        .stock-pill { padding: 4px 8px; border-radius: 99px; color: #3f4e63; background: #eef2f7; font-size: 11px; font-weight: 700; white-space: nowrap; }
        .stock-pill.low { color: #b54745; background: #fff0ef; }

        @media (max-width: 900px) {
            .dashboard-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 650px) {
            .dashboard-grid { grid-template-columns: 1fr; }
            .chart-card { min-height: 220px; }
        }

        @media (max-width: 420px) {
            .dashboard-stats { grid-template-columns: 1fr; }
        }
    </style>
@endpush

@section('content')
    <header class="page-heading">
        <h1>Dashboard Admin</h1>
        <p>Ringkasan stok dan aktivitas {{ $storeSettings->store_name }}</p>
    </header>

    <section class="dashboard-stats" aria-label="Ringkasan toko">
        <article class="admin-card stat-card">
            <div>
                <span class="stat-label">Nilai Persediaan</span>
                <strong class="stat-value">Rp {{ number_format($inventoryValue, 0, ',', '.') }}</strong>
            </div>
            <span class="stat-icon inventory"><i class="bi bi-wallet2"></i></span>
        </article>
        <article class="admin-card stat-card">
            <div>
                <span class="stat-label">Total Barang</span>
                <strong class="stat-value">{{ $totalProducts }}</strong>
            </div>
            <span class="stat-icon products"><i class="bi bi-box-seam"></i></span>
        </article>
        <article class="admin-card stat-card">
            <div>
                <span class="stat-label">Barang Aktif</span>
                <strong class="stat-value">{{ $activeProducts }}</strong>
            </div>
            <span class="stat-icon active"><i class="bi bi-check2-circle"></i></span>
        </article>
        <article class="admin-card stat-card">
            <div>
                <span class="stat-label">Stok Rendah</span>
                <strong class="stat-value">{{ $lowStockProducts }}</strong>
            </div>
            <span class="stat-icon low-stock"><i class="bi bi-exclamation-triangle"></i></span>
        </article>
    </section>

    <section class="dashboard-grid">
        <article class="admin-card chart-card">
            <div class="admin-card-header">
                <span class="eyebrow">Ringkasan</span>
                <h2>Aktivitas Penjualan</h2>
                <span class="small text-muted">7 hari terakhir · Rp {{ number_format((float) $salesChart->sum('total'), 0, ',', '.') }}</span>
            </div>
            <div class="admin-card-body">
                @php
                    $chartMax = max(1, (float) $salesChart->max('total'));
                    $chartPoints = $salesChart->values()->map(function ($day, $index) use ($chartMax) {
                        $x = 30 + ($index * 640 / 6);
                        $y = 140 - ((float) $day['total'] / $chartMax * 112);

                        return ['x' => $x, 'y' => $y, 'label' => $day['label'], 'total' => $day['total']];
                    });
                    $chartLinePoints = $chartPoints->map(fn ($point) => $point['x'].','.$point['y'])->implode(' ');
                    $chartAreaPoints = '30,140 '.$chartLinePoints.' 670,140';
                @endphp
                <div class="chart-area">
                    <svg class="sales-chart" viewBox="0 0 700 165" role="img" aria-label="Grafik penjualan tujuh hari terakhir">
                        <title>Nilai penjualan per hari selama tujuh hari terakhir</title>
                        <line class="chart-gridline" x1="30" y1="28" x2="670" y2="28"></line>
                        <line class="chart-gridline" x1="30" y1="84" x2="670" y2="84"></line>
                        <line class="chart-gridline" x1="30" y1="140" x2="670" y2="140"></line>
                        <polygon class="chart-area-fill" points="{{ $chartAreaPoints }}"></polygon>
                        <polyline class="chart-line" points="{{ $chartLinePoints }}"></polyline>
                        @foreach($chartPoints as $point)
                            <circle class="chart-point" cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="4">
                                <title>{{ $point['label'] }}: Rp {{ number_format((float) $point['total'], 0, ',', '.') }}</title>
                            </circle>
                        @endforeach
                    </svg>
                </div>
                <div class="chart-labels" aria-hidden="true">
                    @foreach($salesChart as $day)
                        <span>{{ $day['label'] }}</span>
                    @endforeach
                </div>
                @if((float) $salesChart->sum('total') === 0.0)
                    <p class="chart-empty mt-2 mb-0">Belum ada transaksi dalam tujuh hari terakhir.</p>
                @endif
            </div>
        </article>

        <article class="admin-card chart-card">
            <div class="admin-card-header">
                <span class="eyebrow">Katalog</span>
                <h2>Status Barang</h2>
            </div>
            <div class="admin-card-body">
                <div class="payment-empty">
                    <span>{{ $activeProducts }} barang aktif dari {{ $totalProducts }} barang</span>
                </div>
            </div>
        </article>
    </section>

    <section class="admin-card">
        <div class="admin-card-header d-flex justify-content-between align-items-center">
            <div>
                <span class="eyebrow">Persediaan</span>
                <h2>Stok Barang Terbaru</h2>
            </div>
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.barang') }}">Lihat semua</a>
        </div>
        <div class="admin-card-body py-1">
            @forelse($products as $product)
                <div class="stock-row">
                    <div>
                        <span class="stock-name">{{ $product->name }}</span>
                        <span class="stock-category">{{ $product->category ?? 'Umum' }}</span>
                    </div>
                    <span class="stock-pill {{ $product->stock <= 5 ? 'low' : '' }}">{{ $product->stock }} stok</span>
                </div>
            @empty
                <p class="py-3 mb-0 text-center text-secondary small">Belum ada barang yang terdaftar.</p>
            @endforelse
        </div>
    </section>
@endsection
