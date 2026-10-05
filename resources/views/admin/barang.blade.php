@extends('layouts.admin')

@section('title', 'Database Barang')

@section('content')
    <header class="page-heading d-flex justify-content-between align-items-end gap-3">
        <div>
            <h1>Database Barang</h1>
            <p>Ringkasan inventaris barang Toko Sembako Jazzel</p>
        </div>
        <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Tambah Barang</a>
    </header>

    <section class="admin-card mb-3">
        <div class="admin-card-body">
            <div class="row g-3">
                <div class="col-sm-4">
                    <span class="text-secondary small text-uppercase fw-semibold">Total Barang</span>
                    <div class="fs-4 fw-bold mt-1">{{ $products->count() }}</div>
                </div>
                <div class="col-sm-4">
                    <span class="text-secondary small text-uppercase fw-semibold">Barang Aktif</span>
                    <div class="fs-4 fw-bold mt-1">{{ $products->where('is_active', true)->count() }}</div>
                </div>
                <div class="col-sm-4">
                    <span class="text-secondary small text-uppercase fw-semibold">Stok Rendah</span>
                    <div class="fs-4 fw-bold mt-1">{{ $products->where('stock', '<=', 5)->count() }}</div>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-card">
        <div class="admin-card-header d-flex justify-content-between align-items-center">
            <h2>Daftar Barang</h2>
            <span class="text-secondary small">{{ $products->count() }} barang</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $product->name }}</td>
                            <td>{{ $product->category ?? 'Umum' }}</td>
                            <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                            <td>{{ $product->stock }}</td>
                            <td>
                                <span class="badge {{ $product->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="text-nowrap">
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-5 text-center text-secondary">Belum ada barang yang terdaftar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
